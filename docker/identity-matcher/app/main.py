import hmac
import os
import re
import tempfile
from io import BytesIO
from pathlib import Path
from typing import Any

import cv2
import numpy as np
import pytesseract
from fastapi import FastAPI, File, Form, Header, HTTPException, UploadFile
from insightface.app import FaceAnalysis
from PIL import Image, ImageOps


app = FastAPI(title="KONEK Identity Matcher")
face_app: FaceAnalysis | None = None
startup_warnings: list[str] = []


def configured_token() -> str | None:
    token = os.getenv("IDENTITY_ANALYZER_TOKEN")
    return token if token else None


def require_token(x_analyzer_token: str | None) -> None:
    token = configured_token()
    if token is None:
        raise HTTPException(status_code=503, detail="Analyzer token is not configured.")
    if not hmac.compare_digest(x_analyzer_token or "", token):
        raise HTTPException(status_code=401, detail="Invalid analyzer token.")


def max_upload_bytes() -> int:
    return int(os.getenv("MAX_UPLOAD_MB", "5")) * 1024 * 1024


@app.on_event("startup")
def load_models() -> None:
    global face_app
    if os.getenv("ENABLE_FACE_MODEL", "false").lower() not in {"1", "true", "yes"}:
        startup_warnings.append("Face model loading is disabled. Set ENABLE_FACE_MODEL=true to enable biometric matching.")
        return

    try:
        face_app = FaceAnalysis(name=os.getenv("INSIGHTFACE_MODEL", "buffalo_l"), providers=["CPUExecutionProvider"])
        face_app.prepare(ctx_id=-1, det_size=(640, 640))
    except Exception as exc:
        face_app = None
        startup_warnings.append(f"Face model could not be loaded: {exc}")


@app.get("/health")
def health() -> dict[str, Any]:
    return {
        "ok": True,
        "ocr": "tesseract",
        "face_model": os.getenv("INSIGHTFACE_MODEL", "buffalo_l"),
        "face_model_loaded": face_app is not None,
        "warnings": startup_warnings,
    }


@app.post("/analyze-identity")
async def analyze_identity(
    school_id: str = Form(...),
    id_document: UploadFile = File(...),
    selfie: UploadFile = File(...),
    x_analyzer_token: str | None = Header(default=None),
) -> dict[str, Any]:
    require_token(x_analyzer_token)

    id_bytes = await read_upload(id_document)
    selfie_bytes = await read_upload(selfie)
    warnings: list[str] = [*startup_warnings]

    id_image = decode_image(id_bytes)
    selfie_image = decode_image(selfie_bytes)

    extracted_school_id, ocr_confidence = extract_school_id(id_image, school_id)
    if not extracted_school_id:
        warnings.append("No school ID number was detected in the bottom-left crop or full image.")

    id_face = best_face(id_image)
    selfie_face = best_face(selfie_image)
    biometric_score = None

    if id_face is None:
        warnings.append("No face was detected on the ID image.")
    if selfie_face is None:
        warnings.append("No face was detected on the selfie image.")
    if id_face is not None and selfie_face is not None:
        biometric_score = compare_faces(id_face.embedding, selfie_face.embedding)

    return {
        "extracted_school_id": extracted_school_id,
        "ocr_confidence": ocr_confidence,
        "id_face_detected": id_face is not None,
        "selfie_face_detected": selfie_face is not None,
        "biometric_score": biometric_score,
        "warnings": warnings,
    }


async def read_upload(upload: UploadFile) -> bytes:
    data = await upload.read()
    if len(data) > max_upload_bytes():
        raise HTTPException(status_code=413, detail="Upload is too large.")
    return data


def decode_image(data: bytes) -> np.ndarray:
    try:
        pil_image = Image.open(BytesIO(data))
        pil_image = ImageOps.exif_transpose(pil_image).convert("RGB")
        image = cv2.cvtColor(np.array(pil_image), cv2.COLOR_RGB2BGR)
    except Exception:
        array = np.frombuffer(data, np.uint8)
        image = cv2.imdecode(array, cv2.IMREAD_COLOR)

    if image is None:
        raise HTTPException(status_code=422, detail="Invalid image upload.")
    return image


def extract_school_id(image: np.ndarray, expected_school_id: str | None = None) -> tuple[str | None, int | None]:
    candidates = []
    ocr_image = resize_to_max(image, 2200)
    h, w = ocr_image.shape[:2]
    bottom_left = ocr_image[int(h * 0.62):h, 0:int(w * 0.58)]
    candidates.append(run_ocr(bottom_left))
    candidates.append(run_ocr(ocr_image))

    expected = normalize_id(expected_school_id or "")
    if expected:
        for text, confidence in candidates:
            if expected in normalize_id(text):
                return expected, confidence

    pattern = re.compile(os.getenv("SCHOOL_ID_REGEX", r"[A-Za-z0-9][A-Za-z0-9-]*\d[A-Za-z0-9-]*"))
    best_value = None
    best_confidence = None

    for text, confidence in candidates:
        for match in pattern.findall(text):
            normalized = normalize_id(match)
            if not normalized or not any(char.isdigit() for char in normalized):
                continue
            if best_confidence is None or confidence > best_confidence:
                best_value = normalized
                best_confidence = confidence

    return best_value, best_confidence


def run_ocr(image: np.ndarray) -> tuple[str, int]:
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    gray = cv2.resize(gray, None, fx=2, fy=2, interpolation=cv2.INTER_CUBIC)
    gray = cv2.GaussianBlur(gray, (3, 3), 0)
    _, threshold = cv2.threshold(gray, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)
    pil_image = Image.fromarray(threshold)
    data = pytesseract.image_to_data(pil_image, output_type=pytesseract.Output.DICT, config="--psm 6")
    words = []
    confidences = []

    for text, conf in zip(data.get("text", []), data.get("conf", [])):
        if text and text.strip():
            words.append(text.strip())
            try:
                confidence = int(float(conf))
                if confidence >= 0:
                    confidences.append(confidence)
            except ValueError:
                pass

    average_confidence = int(sum(confidences) / len(confidences)) if confidences else 0
    return " ".join(words), average_confidence


def normalize_id(value: str) -> str:
    return re.sub(r"[^A-Za-z0-9-]", "", value).upper()


def best_face(image: np.ndarray):
    if face_app is None:
        return None

    candidates = []
    for target in face_detection_variants(image):
        faces = face_app.get(target)
        if faces:
            candidates.extend(faces)

    if not candidates:
        for target in haar_face_crops(image):
            faces = face_app.get(target)
            if faces:
                candidates.extend(faces)

    if not candidates:
        return None

    return max(candidates, key=lambda face: (face.bbox[2] - face.bbox[0]) * (face.bbox[3] - face.bbox[1]))


def haar_face_crops(image: np.ndarray) -> list[np.ndarray]:
    cascade_path = cv2.data.haarcascades + "haarcascade_frontalface_alt2.xml"
    detector = cv2.CascadeClassifier(cascade_path)
    if detector.empty():
        return []

    base = resize_to_max(image, 900)
    gray = cv2.cvtColor(base, cv2.COLOR_BGR2GRAY)
    boxes = detector.detectMultiScale(gray, scaleFactor=1.05, minNeighbors=3, minSize=(80, 80))
    if len(boxes) == 0:
        return []

    h, w = base.shape[:2]
    crops = []
    for x, y, width, height in boxes[:3]:
        pad = int(max(width, height) * 0.65)
        left = max(0, x - pad)
        top = max(0, y - pad)
        right = min(w, x + width + pad)
        bottom = min(h, y + height + pad)
        crop = base[top:bottom, left:right]
        if crop.size:
            crops.append(resize_to_max(crop, 900))

    return crops


def face_detection_variants(image: np.ndarray) -> list[np.ndarray]:
    base = resize_to_max(image, 1280)
    h, w = base.shape[:2]
    border = int(max(h, w) * 0.2)
    padded = cv2.copyMakeBorder(base, border, border, border, border, cv2.BORDER_REPLICATE)

    return [base, resize_to_max(padded, 1280)]


def resize_to_max(image: np.ndarray, max_dimension: int) -> np.ndarray:
    h, w = image.shape[:2]
    largest = max(h, w)
    if largest <= max_dimension:
        return image

    scale = max_dimension / largest
    return cv2.resize(image, None, fx=scale, fy=scale, interpolation=cv2.INTER_AREA)


def compare_faces(id_embedding: np.ndarray, selfie_embedding: np.ndarray) -> int:
    left = id_embedding / np.linalg.norm(id_embedding)
    right = selfie_embedding / np.linalg.norm(selfie_embedding)
    cosine = float(np.dot(left, right))
    score = max(0.0, min(1.0, (cosine + 1.0) / 2.0))
    return int(round(score * 100))

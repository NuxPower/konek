# Identity Matcher

The identity matcher is a Dockerized FastAPI service used by Laravel for student ID OCR and selfie-to-ID face matching.

## Local Development

Start the matcher:

```bash
docker compose up --build identity-matcher
```

Check health:

```bash
curl http://127.0.0.1:8001/health
```

Use these Laravel `.env` values when Laravel runs directly on the host:

```dotenv
IDENTITY_ANALYZER=http
IDENTITY_ANALYZER_URL=http://127.0.0.1:8001/analyze-identity
IDENTITY_ANALYZER_TOKEN=local-dev-token
SMS_DRIVER=log
```

If Laravel runs inside the same Compose network, use:

```dotenv
IDENTITY_ANALYZER_URL=http://identity-matcher:8000/analyze-identity
```

## Railway

Create a Railway service from `docker/identity-matcher/Dockerfile`.

Set these Railway variables:

```dotenv
IDENTITY_ANALYZER_TOKEN=
SCHOOL_ID_REGEX=[A-Za-z0-9-]{4,}
OCR_CONFIDENCE_THRESHOLD=90
BIOMETRIC_SCORE_THRESHOLD=90
MAX_UPLOAD_MB=5
INSIGHTFACE_MODEL=buffalo_l
ENABLE_FACE_MODEL=true
```

Set Laravel production variables:

```dotenv
IDENTITY_ANALYZER=http
IDENTITY_ANALYZER_URL=https://<railway-identity-matcher-url>/analyze-identity
IDENTITY_ANALYZER_TOKEN=<same token as Railway>
SMS_DRIVER=smsapiph
SMSAPIPH_ENDPOINT=https://smsapiph.onrender.com/api/v1/send/sms
SMSAPIPH_API_KEY=
```

Raw ID/selfie files remain private in Laravel and are deleted after auto-approval, admin approval, or rejection.

For local smoke tests without downloading the InsightFace model, leave `ENABLE_FACE_MODEL=false`. The service will start and OCR will run, but biometric checks will return warnings and Laravel will route the proof to admin review.

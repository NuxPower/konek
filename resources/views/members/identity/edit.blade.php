<x-app-layout>
    @php
        $statusLabels = [
            'unsubmitted' => 'Not submitted',
            'pending' => 'Pending review',
            'verified' => 'Verified',
            'rejected' => 'Rejected',
        ];
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">

            <section class="rounded-lg border bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Current proof score</h3>
                        <p class="mt-1 text-sm text-slate-600">Only the score and safe verification labels appear on your profile.</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-emerald-700">{{ $verification->proof_points }}/100</p>
                        <p class="text-sm text-slate-500">{{ $statusLabels[$verification->status] ?? ucfirst($verification->status) }}</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <div class="rounded border border-slate-200 p-4">
                        <p class="text-sm font-semibold text-slate-800">Phone ownership</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $verification->phone_verified_at ? 'Verified' : 'Not verified' }}</p>
                    </div>
                    <div class="rounded border border-slate-200 p-4">
                        <p class="text-sm font-semibold text-slate-800">Student ID + biometric match</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $statusLabels[$verification->status] ?? ucfirst($verification->status) }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-lg border bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Verify phone number</h3>
                <p class="mt-1 text-sm text-slate-600">Phone proof points are awarded only after SMS code verification.</p>

                <form method="POST" action="{{ route('member.identity.phone.send') }}" class="mt-5 space-y-2">
                    @csrf
                    <div>
                        <label for="phone" class="text-sm font-medium text-slate-700">Phone number</label>
                        <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 w-full rounded border-slate-300" maxlength="20" required>
                        @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <button class="h-[42px] w-full rounded bg-emerald-700 px-4 text-sm font-semibold text-white">Send code</button>
                </form>

                <form method="POST" action="{{ route('member.identity.phone.verify') }}" class="mt-4 space-y-2">
                    @csrf
                    <div>
                        <label for="code" class="text-sm font-medium text-slate-700">SMS code</label>
                        <input id="code" name="code" inputmode="numeric" maxlength="6" class="mt-1 w-full rounded border-slate-300" required>
                        @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <button class="h-[42px] w-full rounded bg-slate-900 px-4 text-sm font-semibold text-white">Verify phone</button>
                </form>
            </section>

            <section class="rounded-lg border bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Submit student ID and selfie</h3>
                <p class="mt-1 text-sm text-slate-600">Upload your student ID once and a selfie once. The same ID image is used for ID-number OCR and face matching.</p>

                <form method="POST" action="{{ route('member.identity.submit') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="school_id" class="text-sm font-medium text-slate-700">School ID number</label>
                        <input id="school_id" name="school_id" value="{{ old('school_id', $verification->school_id) }}" class="mt-1 w-full rounded border-slate-300" maxlength="50" required>
                        @error('school_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="id_document" class="text-sm font-medium text-slate-700">Student ID image</label>
                            <input id="id_document" name="id_document" type="file" accept="image/*" class="mt-1 block w-full text-sm" required>
                            <p class="mt-1 text-xs text-slate-500">JPG, PNG, or WebP. Large photos are resized before upload.</p>
                            @error('id_document') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="selfie" class="text-sm font-medium text-slate-700">Selfie / face capture</label>
                            <input id="selfie" name="selfie" type="file" accept="image/*" capture="user" class="mt-1 block w-full text-sm" required>
                            <p class="mt-1 text-xs text-slate-500">Keep your face clear and well lit. Large photos are resized before upload.</p>
                            @error('selfie') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button class="rounded bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">Submit ID proof</button>
                        @if(in_array($verification->status, ['pending', 'rejected'], true))
                            <button form="reset-proof" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Clear submission</button>
                        @endif
                    </div>
                </form>

                <form id="reset-proof" method="POST" action="{{ route('member.identity.reset') }}">
                    @csrf
                    @method('DELETE')
                </form>
            </section>
        </div>
    </div>

    <script>
        (() => {
            const maxFileBytes = 10 * 1024 * 1024;
            const maxPostBytes = 24 * 1024 * 1024;
            const maxDimension = 1600;
            const inputs = ['id_document', 'selfie']
                .map((name) => document.querySelector(`input[name="${name}"]`))
                .filter(Boolean);
            const form = inputs[0]?.closest('form');
            const pendingOptimizations = new Map();

            if (!form || inputs.length === 0) {
                return;
            }

            const setMessage = (input, message, tone = 'error') => {
                let node = input.parentElement.querySelector(`[data-upload-message="${input.name}"]`);

                if (!node) {
                    node = document.createElement('p');
                    node.dataset.uploadMessage = input.name;
                    input.insertAdjacentElement('afterend', node);
                }

                node.className = `mt-1 text-xs ${tone === 'error' ? 'text-red-600' : 'text-slate-500'}`;
                node.textContent = message;
            };

            const clearMessage = (input) => {
                input.parentElement.querySelector(`[data-upload-message="${input.name}"]`)?.remove();
            };

            const readImage = (file) => new Promise((resolve, reject) => {
                const image = new Image();
                const url = URL.createObjectURL(file);

                image.onload = () => {
                    URL.revokeObjectURL(url);
                    resolve(image);
                };
                image.onerror = () => {
                    URL.revokeObjectURL(url);
                    reject(new Error('Invalid image'));
                };
                image.src = url;
            });

            const canvasToBlob = (canvas, quality) => new Promise((resolve) => {
                canvas.toBlob(resolve, 'image/jpeg', quality);
            });

            const optimizeImage = async (file) => {
                if (!file || !file.type.startsWith('image/')) {
                    return file;
                }

                if (file.size <= maxFileBytes) {
                    return file;
                }

                const image = await readImage(file);
                const scale = Math.min(1, maxDimension / Math.max(image.width, image.height));
                const canvas = document.createElement('canvas');
                canvas.width = Math.max(1, Math.round(image.width * scale));
                canvas.height = Math.max(1, Math.round(image.height * scale));

                const context = canvas.getContext('2d');
                context.fillStyle = '#ffffff';
                context.fillRect(0, 0, canvas.width, canvas.height);
                context.drawImage(image, 0, 0, canvas.width, canvas.height);

                for (const quality of [0.82, 0.74, 0.66, 0.58, 0.5]) {
                    const blob = await canvasToBlob(canvas, quality);

                    if (blob && (blob.size <= maxFileBytes || quality === 0.5)) {
                        return new File(
                            [blob],
                            file.name.replace(/\.[^.]+$/, '') + '.jpg',
                            { type: 'image/jpeg', lastModified: Date.now() }
                        );
                    }
                }

                return file;
            };

            const replaceFile = (input, file) => {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;
            };

            inputs.forEach((input) => {
                input.addEventListener('change', () => {
                    const task = (async () => {
                        clearMessage(input);
                        const file = input.files?.[0];

                        if (!file) {
                            return;
                        }

                        try {
                            const optimized = await optimizeImage(file);
                            replaceFile(input, optimized);

                            if (optimized.size > maxFileBytes) {
                            setMessage(input, 'This image is still over 10 MB. Choose a smaller photo.');
                            } else if (optimized.size < file.size) {
                                setMessage(input, `Resized to ${(optimized.size / 1024 / 1024).toFixed(1)} MB.`, 'info');
                            }
                        } catch (error) {
                            setMessage(input, 'Choose a valid image file.');
                        }
                    })();

                    pendingOptimizations.set(input, task);
                    task.finally(() => pendingOptimizations.delete(input));
                });
            });

            form.addEventListener('submit', async (event) => {
                if (pendingOptimizations.size > 0) {
                    event.preventDefault();
                    await Promise.all([...pendingOptimizations.values()]);
                    form.requestSubmit();
                    return;
                }

                let totalBytes = 0;
                let hasError = false;

                inputs.forEach((input) => {
                    const file = input.files?.[0];

                    if (!file) {
                        return;
                    }

                    totalBytes += file.size;

                    if (file.size > maxFileBytes) {
                        setMessage(input, 'This image must be 10 MB or smaller.');
                        hasError = true;
                    }
                });

                if (totalBytes > maxPostBytes) {
                    inputs.forEach((input) => setMessage(input, 'The combined upload is too large. Choose smaller photos.'));
                    hasError = true;
                }

                if (hasError) {
                    event.preventDefault();
                }
            });
        })();
    </script>
</x-app-layout>

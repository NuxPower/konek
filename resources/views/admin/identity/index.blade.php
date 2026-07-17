<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg border bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Member</th>
                            <th class="px-4 py-3">School ID</th>
                            <th class="px-4 py-3">OCR ID</th>
                            <th class="px-4 py-3">Biometric</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($verifications as $verification)
                            <tr>
                                <td class="px-4 py-3">{{ $verification->user->name }}</td>
                                <td class="px-4 py-3">{{ $verification->school_id ?? 'Not detected' }}</td>
                                <td class="px-4 py-3">{{ $verification->extracted_school_id ?? 'Not detected' }}</td>
                                <td class="px-4 py-3">{{ $verification->biometric_score ? $verification->biometric_score.'%' : 'Needs review' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.identity.show', $verification) }}" class="font-semibold text-emerald-700">Review</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">No pending identity verifications.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $verifications->links() }}</div>
        </div>
    </div>
</x-app-layout>

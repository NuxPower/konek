@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="page-eyebrow">Updates</p>
        <h1>Notifications</h1>
        <p class="page-subtitle">Application activity and important job updates across your workspace.</p>
    </div>
    @if(auth()->user()->unreadNotifications()->exists())
        <form method="POST" action="{{ route('notifications.read-all') }}" class="!m-0 !max-w-none !border-0 !bg-transparent !p-0 !shadow-none">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-secondary">Mark all as read</button>
        </form>
    @endif
</div>

<section class="panel">
    <div class="space-y-3">
        @forelse($notifications as $notification)
            <a href="{{ route('notifications.open', $notification) }}" class="group flex items-start gap-4 rounded-2xl border p-4 transition hover:border-emerald-200 hover:bg-emerald-50/40 sm:p-5 {{ $notification->read_at ? 'border-slate-200 bg-white' : 'border-emerald-200 bg-emerald-50/50' }}">
                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full {{ $notification->read_at ? 'bg-slate-200' : 'bg-emerald-500' }}"></span>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                        <h2 class="text-base">{{ $notification->data['title'] ?? 'KONEK update' }}</h2>
                        <span class="shrink-0 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ $notification->data['message'] ?? '' }}</p>
                </div>
                <svg class="mt-1 h-5 w-5 shrink-0 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/>
                </svg>
            </a>
        @empty
            <div class="empty-state">
                <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-emerald-50 text-emerald-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 0 1-6 0v-1"/>
                    </svg>
                </div>
                <h2 class="mt-4">You are all caught up</h2>
                <p class="mt-2">New application and job updates will appear here.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $notifications->links() }}</div>
</section>
@endsection

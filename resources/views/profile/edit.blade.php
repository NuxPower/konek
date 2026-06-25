<x-app-layout>
    <div class="page-header">
        <div><p class="page-eyebrow">Account settings</p><h1>Your account</h1><p class="page-subtitle">Manage your login identity, password, and account status.</p></div>
    </div>
    <div class="grid gap-5 xl:grid-cols-2">
        <section class="panel">@include('profile.partials.update-profile-information-form')</section>
        <section class="panel">@include('profile.partials.update-password-form')</section>
        <section class="panel border-red-100 xl:col-span-2">@include('profile.partials.delete-user-form')</section>
    </div>
</x-app-layout>

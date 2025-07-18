@extends('layouts.client')

@section('title', 'Account Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Account Settings</h1>
                <a href="{{ route('client.profile.show') }}" class="text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            </div>

            <!-- Notification Preferences -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Notification Preferences</h3>
                
                <form action="{{ route('client.profile.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-800">Email Notifications</h4>
                                <p class="text-sm text-gray-600">Receive notifications about job applications, messages, and updates</p>
                            </div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" 
                                       name="email_notifications" 
                                       value="1"
                                       {{ old('email_notifications', $user->email_notifications ?? true) ? 'checked' : '' }}
                                       class="form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500">
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-800">SMS Notifications</h4>
                                <p class="text-sm text-gray-600">Receive urgent notifications via SMS</p>
                            </div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" 
                                       name="sms_notifications" 
                                       value="1"
                                       {{ old('sms_notifications', $user->sms_notifications ?? false) ? 'checked' : '' }}
                                       class="form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500">
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-800">Marketing Emails</h4>
                                <p class="text-sm text-gray-600">Receive newsletters, tips, and promotional content</p>
                            </div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" 
                                       name="marketing_emails" 
                                       value="1"
                                       {{ old('marketing_emails', $user->marketing_emails ?? false) ? 'checked' : '' }}
                                       class="form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500">
                            </label>
                        </div>
                    </div>

                    <!-- Regional Settings -->
                    <div class="mt-8 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Regional Settings</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="timezone" class="block text-sm font-medium text-gray-700 mb-1">Timezone *</label>
                                <select id="timezone" 
                                        name="timezone" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('timezone') border-red-500 @enderror"
                                        required>
                                    <option value="">Select timezone</option>
                                    <option value="UTC" {{ old('timezone', $user->timezone) == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ old('timezone', $user->timezone) == 'America/New_York' ? 'selected' : '' }}>Eastern Time (US & Canada)</option>
                                    <option value="America/Chicago" {{ old('timezone', $user->timezone) == 'America/Chicago' ? 'selected' : '' }}>Central Time (US & Canada)</option>
                                    <option value="America/Denver" {{ old('timezone', $user->timezone) == 'America/Denver' ? 'selected' : '' }}>Mountain Time (US & Canada)</option>
                                    <option value="America/Los_Angeles" {{ old('timezone', $user->timezone) == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (US & Canada)</option>
                                    <option value="Europe/London" {{ old('timezone', $user->timezone) == 'Europe/London' ? 'selected' : '' }}>London</option>
                                    <option value="Europe/Paris" {{ old('timezone', $user->timezone) == 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                                    <option value="Europe/Berlin" {{ old('timezone', $user->timezone) == 'Europe/Berlin' ? 'selected' : '' }}>Berlin</option>
                                    <option value="Asia/Tokyo" {{ old('timezone', $user->timezone) == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo</option>
                                    <option value="Asia/Shanghai" {{ old('timezone', $user->timezone) == 'Asia/Shanghai' ? 'selected' : '' }}>Shanghai</option>
                                    <option value="Asia/Manila" {{ old('timezone', $user->timezone) == 'Asia/Manila' ? 'selected' : '' }}>Manila</option>
                                    <option value="Australia/Sydney" {{ old('timezone', $user->timezone) == 'Australia/Sydney' ? 'selected' : '' }}>Sydney</option>
                                </select>
                                @error('timezone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="language" class="block text-sm font-medium text-gray-700 mb-1">Language *</label>
                                <select id="language" 
                                        name="language" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('language') border-red-500 @enderror"
                                        required>
                                    <option value="">Select language</option>
                                    <option value="en" {{ old('language', $user->language) == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="es" {{ old('language', $user->language) == 'es' ? 'selected' : '' }}>Spanish</option>
                                    <option value="fr" {{ old('language', $user->language) == 'fr' ? 'selected' : '' }}>French</option>
                                    <option value="de" {{ old('language', $user->language) == 'de' ? 'selected' : '' }}>German</option>
                                    <option value="it" {{ old('language', $user->language) == 'it' ? 'selected' : '' }}>Italian</option>
                                    <option value="pt" {{ old('language', $user->language) == 'pt' ? 'selected' : '' }}>Portuguese</option>
                                    <option value="zh" {{ old('language', $user->language) == 'zh' ? 'selected' : '' }}>Chinese</option>
                                    <option value="ja" {{ old('language', $user->language) == 'ja' ? 'selected' : '' }}>Japanese</option>
                                </select>
                                @error('language')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h3>
            
            <form action="{{ route('client.profile.change-password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password *</label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror"
                               required>
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                               required>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Minimum 8 characters required</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password *</label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition duration-200">
                        Change Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Account Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Actions</h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div>
                        <h4 class="font-medium text-gray-800">Export Account Data</h4>
                        <p class="text-sm text-gray-600">Download a copy of your account information and activity</p>
                    </div>
                    <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition duration-200">
                        Export Data
                    </button>
                </div>

                <div class="flex items-center justify-between p-4 border border-red-200 rounded-lg bg-red-50">
                    <div>
                        <h4 class="font-medium text-red-800">Delete Account</h4>
                        <p class="text-sm text-red-600">Permanently delete your account and all associated data</p>
                    </div>
                    <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition duration-200" 
                            onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-checkbox {
    appearance: none;
    background-color: #fff;
    border: 2px solid #d1d5db;
    border-radius: 0.25rem;
    display: inline-block;
    position: relative;
    transition: all 0.2s;
}

.form-checkbox:checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.form-checkbox:checked::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 6px;
    width: 4px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.form-checkbox:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>
@endsection
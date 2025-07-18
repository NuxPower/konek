<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Display admin settings
     */
    public function index()
    {
        $settings = $this->getSettings();
        
        // System information
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_connection' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'mail_driver' => config('mail.default'),
            'app_environment' => app()->environment(),
            'app_debug' => config('app.debug') ? 'Enabled' : 'Disabled',
            'app_timezone' => config('app.timezone'),
        ];

        // Storage information
        $storageInfo = [
            'total_users' => \App\Models\User::count(),
            'total_jobs' => \App\Models\Job::count(),
            'total_applications' => \App\Models\Application::count(),
            'cache_size' => $this->getCacheSize(),
            'log_size' => $this->getLogSize(),
        ];

        return view('admin.settings.index', compact('settings', 'systemInfo', 'storageInfo'));
    }

    /**
     * Update admin settings
     */
    public function update(Request $request)
    {
        $request->validate([
            // General Settings
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'site_favicon' => 'nullable|image|mimes:ico,png|max:1024',
            'contact_email' => 'required|email',
            'support_email' => 'nullable|email',
            
            // User Settings
            'allow_registration' => 'boolean',
            'require_email_verification' => 'boolean',
            'enable_two_factor' => 'boolean',
            'default_user_role' => ['required', Rule::in(['client', 'freelancer'])],
            'max_login_attempts' => 'required|integer|min:1|max:10',
            'login_lockout_duration' => 'required|integer|min:1|max:1440', // minutes
            
            // Job Settings
            'max_jobs_per_client' => 'nullable|integer|min:1|max:1000',
            'job_posting_fee' => 'nullable|numeric|min:0|max:9999.99',
            'enable_job_approval' => 'boolean',
            'auto_close_jobs_days' => 'nullable|integer|min:1|max:365',
            
            // Application Settings
            'max_applications_per_job' => 'nullable|integer|min:1|max:1000',
            'application_deadline_days' => 'nullable|integer|min:1|max:90',
            'enable_application_tracking' => 'boolean',
            
            // Email Settings
            'email_notifications' => 'boolean',
            'email_from_name' => 'required|string|max:255',
            'email_from_address' => 'required|email',
            'smtp_host' => 'nullable|string',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string',
            'smtp_password' => 'nullable|string',
            'smtp_encryption' => ['nullable', Rule::in(['tls', 'ssl'])],
            
            // Security Settings
            'enable_captcha' => 'boolean',
            'session_lifetime' => 'required|integer|min:15|max:10080', // minutes
            'password_min_length' => 'required|integer|min:6|max:20',
            'require_strong_passwords' => 'boolean',
            'enable_activity_logging' => 'boolean',
            
            // System Settings
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string|max:500',
            'cache_enabled' => 'boolean',
            'debug_mode' => 'boolean',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'time_format' => 'required|string',
            
            // API Settings
            'api_enabled' => 'boolean',
            'api_rate_limit' => 'nullable|integer|min:10|max:10000',
            'webhook_enabled' => 'boolean',
            'webhook_url' => 'nullable|url',
        ]);

        try {
            // Handle file uploads
            if ($request->hasFile('site_logo')) {
                $logoPath = $request->file('site_logo')->store('public/settings');
                $this->setSetting('site_logo', Storage::url($logoPath));
            }

            if ($request->hasFile('site_favicon')) {
                $faviconPath = $request->file('site_favicon')->store('public/settings');
                $this->setSetting('site_favicon', Storage::url($faviconPath));
            }

            // Save all settings
            foreach ($request->all() as $key => $value) {
                if (!in_array($key, ['_token', '_method', 'site_logo', 'site_favicon'])) {
                    $this->setSetting($key, $value);
                }
            }

            // Clear cache if cache settings changed
            if ($request->has('cache_enabled')) {
                Cache::flush();
            }

            // Handle maintenance mode
            if ($request->boolean('maintenance_mode')) {
                Artisan::call('down', [
                    '--message' => $request->maintenance_message ?? 'Site is under maintenance'
                ]);
            } else {
                Artisan::call('up');
            }

            return redirect()->route('admin.settings')
                           ->with('success', 'Settings updated successfully.');

        } catch (\Exception $e) {
            Log::error('Settings update failed: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to update settings. Please try again.'])
                        ->withInput();
        }
    }

    /**
     * Clear application cache
     */
    public function clearCache()
    {
        try {
            Cache::flush();
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache.'
            ], 500);
        }
    }

    /**
     * Clear application logs
     */
    public function clearLogs()
    {
        try {
            $logPath = storage_path('logs');
            $files = glob($logPath . '/*.log');
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Logs cleared successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear logs.'
            ], 500);
        }
    }

    /**
     * Test email configuration
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            // Send test email
            \Mail::raw('This is a test email from your application.', function ($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('Test Email - ' . config('app.name'));
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export system settings
     */
    public function exportSettings()
    {
        $settings = $this->getSettings();
        
        $filename = 'system_settings_' . date('Y-m-d_H-i-s') . '.json';
        
        return response()->json($settings)
                        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Import system settings
     */
    public function importSettings(Request $request)
    {
        $request->validate([
            'settings_file' => 'required|file|mimes:json'
        ]);

        try {
            $content = file_get_contents($request->file('settings_file')->path());
            $settings = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['settings_file' => 'Invalid JSON file.']);
            }

            foreach ($settings as $key => $value) {
                $this->setSetting($key, $value);
            }

            return redirect()->route('admin.settings')
                           ->with('success', 'Settings imported successfully.');

        } catch (\Exception $e) {
            Log::error('Settings import failed: ' . $e->getMessage());
            
            return back()->withErrors(['settings_file' => 'Failed to import settings.']);
        }
    }

    /**
     * Reset settings to default
     */
    public function resetSettings()
    {
        try {
            $defaultSettings = $this->getDefaultSettings();
            
            foreach ($defaultSettings as $key => $value) {
                $this->setSetting($key, $value);
            }

            return redirect()->route('admin.settings')
                           ->with('success', 'Settings reset to default values.');

        } catch (\Exception $e) {
            Log::error('Settings reset failed: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to reset settings.']);
        }
    }

    /**
     * Get all settings
     */
    private function getSettings()
    {
        return Cache::remember('admin_settings', 3600, function () {
            $defaultSettings = $this->getDefaultSettings();
            $storedSettings = json_decode(file_get_contents(storage_path('app/settings.json')), true) ?? [];
            
            return array_merge($defaultSettings, $storedSettings);
        });
    }

    /**
     * Set a setting value
     */
    private function setSetting($key, $value)
    {
        $settings = $this->getSettings();
        $settings[$key] = $value;
        
        file_put_contents(storage_path('app/settings.json'), json_encode($settings, JSON_PRETTY_PRINT));
        Cache::forget('admin_settings');
    }

    /**
     * Get default settings
     */
    private function getDefaultSettings()
    {
        return [
            // General Settings
            'site_name' => config('app.name', 'Freelance Platform'),
            'site_description' => 'A platform connecting freelancers with clients',
            'site_logo' => null,
            'site_favicon' => null,
            'contact_email' => 'contact@example.com',
            'support_email' => 'support@example.com',
            
            // User Settings
            'allow_registration' => true,
            'require_email_verification' => true,
            'enable_two_factor' => false,
            'default_user_role' => 'freelancer',
            'max_login_attempts' => 5,
            'login_lockout_duration' => 15,
            
            // Job Settings
            'max_jobs_per_client' => 50,
            'job_posting_fee' => 0.00,
            'enable_job_approval' => false,
            'auto_close_jobs_days' => 30,
            
            // Application Settings
            'max_applications_per_job' => 100,
            'application_deadline_days' => 14,
            'enable_application_tracking' => true,
            
            // Email Settings
            'email_notifications' => true,
            'email_from_name' => config('mail.from.name'),
            'email_from_address' => config('mail.from.address'),
            
            // Security Settings
            'enable_captcha' => false,
            'session_lifetime' => 120,
            'password_min_length' => 8,
            'require_strong_passwords' => true,
            'enable_activity_logging' => true,
            
            // System Settings
            'maintenance_mode' => false,
            'maintenance_message' => 'We are currently performing maintenance.',
            'cache_enabled' => true,
            'debug_mode' => config('app.debug', false),
            'timezone' => config('app.timezone', 'UTC'),
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i:s',
            
            // API Settings
            'api_enabled' => false,
            'api_rate_limit' => 1000,
            'webhook_enabled' => false,
            'webhook_url' => null,
        ];
    }

    /**
     * Get cache size
     */
    private function getCacheSize()
    {
        try {
            $size = 0;
            $cachePath = storage_path('framework/cache');
            
            if (is_dir($cachePath)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($cachePath)
                );
                
                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $size += $file->getSize();
                    }
                }
            }
            
            return $this->formatBytes($size);
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get log size
     */
    private function getLogSize()
    {
        try {
            $size = 0;
            $logPath = storage_path('logs');
            $files = glob($logPath . '/*.log');
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    $size += filesize($file);
                }
            }
            
            return $this->formatBytes($size);
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
# KONEK - CMU Freelance Connect

A Laravel-based job board platform connecting students and employers in Central Mindanao University.

## Overview

KONEK is a centralized platform designed to bridge the gap between CMU students seeking freelance opportunities and local employers looking for skilled talent. The platform facilitates job postings, applications, and provides comprehensive dashboards for all user types.

## Features

- **Authentication System**: Laravel Breeze with email verification
- **Role-based Access Control**: Admin, Freelancer, and Client roles
- **Job Management**: Full CRUD operations for job postings
- **Application System**: Freelancers can apply, clients can manage applications
- **Dashboards**: Role-specific dashboards with analytics
- **Reporting**: Downloadable reports (PDF/Excel) by date, category, and status
- **Activity Logging**: Complete audit trail using Spatie Activity Log

## Technology Stack

- **Backend**: Laravel 10.x (PHP 8.1+)
- **Frontend**: Blade Templates with Bootstrap/Tailwind CSS
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Breeze
- **Version Control**: Git & GitHub
- **Additional Packages**:
  - Spatie Activity Log
  - Laravel Excel
  - Chart.js for analytics

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/konek-cmu-freelance.git
   cd konek-cmu-freelance
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   Update your `.env` file with database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=konek_db
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Build assets**
   ```bash
   npm run dev
   ```

7. **Start development server**
   ```bash
   php artisan serve
   ```

## User Roles

- **Admin**: Platform management, user oversight, reports generation
- **Client**: Job posting, application management, freelancer selection
- **Freelancer**: Job browsing, application submission, profile management

## Key Functionalities

### Job Posting System
- Create, edit, and delete job listings
- Categorize jobs by type and skills required
- Set application deadlines and requirements

### Application Management
- Freelancers can apply to multiple jobs
- Clients can view, filter, and manage applications
- Status tracking throughout the application process

### Dashboard Analytics
- Job posting statistics
- Application trends
- User engagement metrics
- Revenue tracking (for future implementation)

### Reporting System
- Generate reports by date range, category, and status
- Export functionality for PDF and Excel formats
- Admin-level analytics and insights

## Database Schema

### Core Tables
- `users` - User authentication and basic information
- `jobs` - Job postings with details and requirements
- `applications` - Job applications linking users to jobs
- `categories` - Job categories and classifications
- `activity_log` - Audit trail for all user actions

## API Endpoints

### Authentication
- `POST /register` - User registration (CMU email only)
- `POST /login` - User login
- `POST /logout` - User logout
- `POST /password/reset` - Password reset

### Jobs
- `GET /jobs` - List all jobs
- `POST /jobs` - Create new job (Client only)
- `GET /jobs/{id}` - View job details
- `PUT /jobs/{id}` - Update job (Client only)
- `DELETE /jobs/{id}` - Delete job (Client only)

### Applications
- `POST /jobs/{id}/apply` - Apply to job (Freelancer only)
- `GET /applications` - View applications
- `PUT /applications/{id}` - Update application status

## Security Features

- CMU email verification required for registration
- Role-based access control
- CSRF protection on all forms
- Input validation and sanitization
- Activity logging for audit trails

## Limitations

- No integrated payment system
- No SMS/email notifications (in-system only)
- CMU-only user registration
- Basic freelancer profiles (no ratings/portfolios)
- Manual job completion marking
- Manual content moderation

## Future Enhancements

- Payment integration
- Email/SMS notifications
- Advanced freelancer profiles with ratings
- Automated content moderation
- Mobile application
- Extended user base beyond CMU

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/new-feature`)
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions, please contact the development team or create an issue in the GitHub repository.

## Acknowledgments

- Central Mindanao University for project support
- Laravel community for excellent documentation
- Contributors and testers







# KONEK - Laravel Project Structure

```
konek-cmu-freelance/
├── app/
│   ├── Console/
│   │   └── Commands/
│   ├── Exceptions/
│   │   └── Handler.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AdminDashboardController.php
│   │   │   │   ├── UserManagementController.php
│   │   │   │   ├── JobManagementController.php
│   │   │   │   ├── ApplicationManagementController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   └── ActivityLogController.php
│   │   │   ├── Client/
│   │   │   │   ├── ClientDashboardController.php
│   │   │   │   ├── JobController.php
│   │   │   │   ├── ApplicationController.php
│   │   │   │   └── ClientProfileController.php
│   │   │   ├── Freelancer/
│   │   │   │   ├── FreelancerDashboardController.php
│   │   │   │   ├── JobBrowseController.php
│   │   │   │   ├── JobApplicationController.php
│   │   │   │   └── FreelancerProfileController.php
│   │   │   ├── Auth/
│   │   │   │   ├── AuthenticatedSessionController.php
│   │   │   │   ├── EmailVerificationNotificationController.php
│   │   │   │   ├── EmailVerificationPromptController.php
│   │   │   │   ├── NewPasswordController.php
│   │   │   │   ├── PasswordResetLinkController.php
│   │   │   │   ├── RegisteredUserController.php
│   │   │   │   └── VerifyEmailController.php
│   │   │   └── HomeController.php
│   │   ├── Middleware/
│   │   │   ├── Authenticate.php
│   │   │   ├── CheckRole.php
│   │   │   ├── EnsureEmailIsVerified.php
│   │   │   ├── ValidateCMUEmail.php
│   │   │   └── ActivityLogger.php
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   └── RegisterRequest.php
│   │   │   ├── Job/
│   │   │   │   ├── StoreJobRequest.php
│   │   │   │   └── UpdateJobRequest.php
│   │   │   ├── Application/
│   │   │   │   ├── StoreApplicationRequest.php
│   │   │   │   └── UpdateApplicationRequest.php
│   │   │   └── Profile/
│   │   │       ├── UpdateProfileRequest.php
│   │   │       └── UpdatePasswordRequest.php
│   │   └── Kernel.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Job.php
│   │   ├── Application.php
│   │   ├── Category.php
│   │   ├── Skill.php
│   │   └── ActivityLog.php
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── BroadcastServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── RouteServiceProvider.php
│   └── Services/
│       ├── JobService.php
│       ├── ApplicationService.php
│       ├── ReportService.php
│       ├── NotificationService.php
│       └── ActivityLogService.php
├── bootstrap/
│   ├── app.php
│   └── cache/
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── mail.php
│   └── activitylog.php
├── database/
│   ├── factories/
│   │   ├── UserFactory.php
│   │   ├── JobFactory.php
│   │   ├── ApplicationFactory.php
│   │   └── CategoryFactory.php
│   ├── migrations/
│   │   ├── 2024_01_01_000000_create_users_table.php
│   │   ├── 2024_01_01_000001_create_password_reset_tokens_table.php
│   │   ├── 2024_01_01_000002_create_failed_jobs_table.php
│   │   ├── 2024_01_01_000003_create_personal_access_tokens_table.php
│   │   ├── 2024_01_01_000004_create_categories_table.php
│   │   ├── 2024_01_01_000005_create_skills_table.php
│   │   ├── 2024_01_01_000006_create_jobs_table.php
│   │   ├── 2024_01_01_000007_create_applications_table.php
│   │   ├── 2024_01_01_000008_create_job_skill_table.php
│   │   ├── 2024_01_01_000009_create_user_skill_table.php
│   │   └── 2024_01_01_000010_create_activity_log_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       ├── CategorySeeder.php
│       ├── SkillSeeder.php
│       └── JobSeeder.php
├── public/
│   ├── index.php
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   └── images/
│       └── logo.png
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   ├── app.js
│   │   └── dashboard.js
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   ├── guest.blade.php
│       │   ├── navigation.blade.php
│       │   └── sidebar.blade.php
│       ├── components/
│       │   ├── alert.blade.php
│       │   ├── job-card.blade.php
│       │   ├── pagination.blade.php
│       │   ├── modal.blade.php
│       │   └── stats-card.blade.php
│       ├── auth/
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   ├── forgot-password.blade.php
│       │   ├── reset-password.blade.php
│       │   ├── verify-email.blade.php
│       │   └── confirm-password.blade.php
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── users/
│       │   │   ├── index.blade.php
│       │   │   ├── show.blade.php
│       │   │   ├── edit.blade.php
│       │   │   └── create.blade.php
│       │   ├── jobs/
│       │   │   ├── index.blade.php
│       │   │   ├── show.blade.php
│       │   │   └── edit.blade.php
│       │   ├── applications/
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   ├── reports/
│       │   │   ├── index.blade.php
│       │   │   ├── jobs.blade.php
│       │   │   ├── applications.blade.php
│       │   │   └── users.blade.php
│       │   └── activity-logs/
│       │       └── index.blade.php
│       ├── client/
│       │   ├── dashboard.blade.php
│       │   ├── jobs/
│       │   │   ├── index.blade.php
│       │   │   ├── show.blade.php
│       │   │   ├── create.blade.php
│       │   │   └── edit.blade.php
│       │   ├── applications/
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   └── profile/
│       │       ├── edit.blade.php
│       │       └── show.blade.php
│       ├── freelancer/
│       │   ├── dashboard.blade.php
│       │   ├── jobs/
│       │   │   ├── index.blade.php
│       │   │   ├── show.blade.php
│       │   │   └── search.blade.php
│       │   ├── applications/
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   └── profile/
│       │       ├── edit.blade.php
│       │       └── show.blade.php
│       ├── jobs/
│       │   ├── index.blade.php
│       │   ├── show.blade.php
│       │   └── search.blade.php
│       ├── profile/
│       │   ├── edit.blade.php
│       │   ├── partials/
│       │   │   ├── delete-user-form.blade.php
│       │   │   ├── update-password-form.blade.php
│       │   │   └── update-profile-information-form.blade.php
│       │   └── show.blade.php
│       ├── dashboard.blade.php
│       ├── welcome.blade.php
│       └── errors/
│           ├── 404.blade.php
│           ├── 403.blade.php
│           └── 500.blade.php
├── routes/
│   ├── api.php
│   ├── channels.php
│   ├── console.php
│   └── web.php
├── storage/
│   ├── app/
│   │   ├── private/
│   │   └── public/
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   └── views/
│   └── logs/
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   │   ├── AuthenticationTest.php
│   │   │   ├── EmailVerificationTest.php
│   │   │   ├── PasswordResetTest.php
│   │   │   └── RegistrationTest.php
│   │   ├── JobTest.php
│   │   ├── ApplicationTest.php
│   │   └── DashboardTest.php
│   ├── Unit/
│   │   ├── Models/
│   │   │   ├── UserTest.php
│   │   │   ├── JobTest.php
│   │   │   └── ApplicationTest.php
│   │   └── Services/
│   │       ├── JobServiceTest.php
│   │       └── ApplicationServiceTest.php
│   ├── CreatesApplication.php
│   └── TestCase.php
├── vendor/
├── .env.example
├── .gitignore
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── package-lock.json
├── phpunit.xml
├── README.md
└── vite.config.js
```

## Key Directory Purposes

### `/app/Http/Controllers/`
- **Admin/**: Administrative functions, user management, reports
- **Client/**: Client-specific features, job posting, application management
- **Freelancer/**: Freelancer dashboard, job browsing, applications
- **Auth/**: Authentication controllers from Laravel Breeze

### `/app/Models/`
- Core application models with relationships and business logic

### `/app/Services/`
- Business logic services for complex operations

### `/database/migrations/`
- Database schema definitions and modifications

### `/resources/views/`
- **layouts/**: Base templates and navigation
- **components/**: Reusable UI components
- **auth/**: Authentication views
- **admin/**: Admin panel views
- **client/**: Client dashboard views
- **freelancer/**: Freelancer interface views

### `/routes/`
- **web.php**: Main application routes
- **api.php**: API endpoints (if needed)

### `/tests/`
- **Feature/**: Integration tests
- **Unit/**: Unit tests for models and services









# KONEK - Database Migrations

## Migration Files and Purpose

### 1. `2024_01_01_000000_create_users_table.php`
**Purpose**: Create users table with role-based authentication
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->enum('role', ['admin', 'client', 'freelancer'])->default('freelancer');
    $table->string('phone')->nullable();
    $table->text('bio')->nullable();
    $table->string('student_id')->nullable(); // For CMU students
    $table->string('department')->nullable(); // For CMU affiliation
    $table->year('year_level')->nullable(); // For students
    $table->boolean('is_active')->default(true);
    $table->timestamp('last_login_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
```

### 2. `2024_01_01_000001_create_password_reset_tokens_table.php`
**Purpose**: Handle password reset functionality
```php
Schema::create('password_reset_tokens', function (Blueprint $table) {
    $table->string('email')->primary();
    $table->string('token');
    $table->timestamp('created_at')->nullable();
});
```

### 3. `2024_01_01_000002_create_failed_jobs_table.php`
**Purpose**: Track failed queue jobs
```php
Schema::create('failed_jobs', function (Blueprint $table) {
    $table->id();
    $table->string('uuid')->unique();
    $table->text('connection');
    $table->text('queue');
    $table->longText('payload');
    $table->longText('exception');
    $table->timestamp('failed_at')->useCurrent();
});
```

### 4. `2024_01_01_000003_create_personal_access_tokens_table.php`
**Purpose**: API authentication tokens (if needed)
```php
Schema::create('personal_access_tokens', function (Blueprint $table) {
    $table->id();
    $table->morphs('tokenable');
    $table->string('name');
    $table->string('token', 64)->unique();
    $table->text('abilities')->nullable();
    $table->timestamp('last_used_at')->nullable();
    $table->timestamp('expires_at')->nullable();
    $table->timestamps();
});
```

### 5. `2024_01_01_000004_create_categories_table.php`
**Purpose**: Job categories for classification
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('icon')->nullable(); // For UI icons
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### 6. `2024_01_01_000005_create_skills_table.php`
**Purpose**: Skills taxonomy for jobs and users
```php
Schema::create('skills', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### 7. `2024_01_01_000006_create_jobs_table.php`
**Purpose**: Main jobs table with all job posting details
```php
Schema::create('jobs', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description');
    $table->text('requirements');
    $table->foreignId('category_id')->constrained()->cascadeOnDelete();
    $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
    $table->enum('type', ['full-time', 'part-time', 'contract', 'internship']);
    $table->enum('experience_level', ['entry', 'intermediate', 'expert']);
    $table->decimal('budget_min', 10, 2)->nullable();
    $table->decimal('budget_max', 10, 2)->nullable();
    $table->enum('budget_type', ['hourly', 'fixed', 'negotiable']);
    $table->enum('status', ['draft', 'published', 'paused', 'closed', 'cancelled'])->default('draft');
    $table->timestamp('deadline')->nullable();
    $table->timestamp('published_at')->nullable();
    $table->integer('max_applications')->nullable();
    $table->integer('applications_count')->default(0);
    $table->boolean('is_featured')->default(false);
    $table->json('attachments')->nullable(); // For job-related files
    $table->timestamps();
});
```

### 8. `2024_01_01_000007_create_applications_table.php`
**Purpose**: Job applications with status tracking
```php
Schema::create('applications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('job_id')->constrained()->cascadeOnDelete();
    $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
    $table->text('cover_letter');
    $table->decimal('proposed_rate', 10, 2)->nullable();
    $table->enum('rate_type', ['hourly', 'fixed'])->nullable();
    $table->integer('estimated_hours')->nullable();
    $table->text('portfolio_links')->nullable();
    $table->json('attachments')->nullable(); // Resume, portfolio files
    $table->enum('status', ['pending', 'reviewing', 'shortlisted', 'rejected', 'accepted', 'withdrawn'])->default('pending');
    $table->text('client_notes')->nullable(); // Notes from client
    $table->timestamp('reviewed_at')->nullable();
    $table->timestamp('accepted_at')->nullable();
    $table->timestamps();
    
    // Prevent duplicate applications
    $table->unique(['job_id', 'freelancer_id']);
});
```

### 9. `2024_01_01_000008_create_job_skill_table.php`
**Purpose**: Many-to-many relationship between jobs and skills
```php
Schema::create('job_skill', function (Blueprint $table) {
    $table->id();
    $table->foreignId('job_id')->constrained()->cascadeOnDelete();
    $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
    $table->enum('proficiency_required', ['basic', 'intermediate', 'advanced'])->default('basic');
    $table->timestamps();
    
    $table->unique(['job_id', 'skill_id']);
});
```

### 10. `2024_01_01_000009_create_user_skill_table.php`
**Purpose**: Many-to-many relationship between users and their skills
```php
Schema::create('user_skill', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
    $table->enum('proficiency_level', ['basic', 'intermediate', 'advanced'])->default('basic');
    $table->integer('years_experience')->nullable();
    $table->timestamps();
    
    $table->unique(['user_id', 'skill_id']);
});
```

### 11. `2024_01_01_000010_create_activity_log_table.php`
**Purpose**: Audit trail using Spatie Activity Log
```php
Schema::create('activity_log', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('log_name')->nullable();
    $table->text('description');
    $table->nullableMorphs('subject', 'subject');
    $table->nullableMorphs('causer', 'causer');
    $table->json('properties')->nullable();
    $table->timestamps();
    
    $table->index('log_name');
});
```

## Additional Migrations (Optional/Future)

### 12. `create_notifications_table.php`
**Purpose**: In-system notifications
```php
Schema::create('notifications', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('type');
    $table->morphs('notifiable');
    $table->text('data');
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
});
```

### 13. `create_job_views_table.php`
**Purpose**: Track job view statistics
```php
Schema::create('job_views', function (Blueprint $table) {
    $table->id();
    $table->foreignId('job_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $table->ipAddress('ip_address');
    $table->text('user_agent')->nullable();
    $table->timestamps();
});
```

### 14. `create_saved_jobs_table.php`
**Purpose**: Allow users to save/bookmark jobs
```php
Schema::create('saved_jobs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('job_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
    
    $table->unique(['user_id', 'job_id']);
});
```

## Migration Commands

```bash
# Create a new migration
php artisan make:migration create_table_name

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Refresh migrations (rollback and migrate)
php artisan migrate:refresh

# Refresh with seeding
php artisan migrate:refresh --seed

# Check migration status
php artisan migrate:status
```

## Key Relationships

1. **Users** → **Jobs** (One-to-Many): Client creates jobs
2. **Jobs** → **Applications** (One-to-Many): Job receives applications
3. **Users** → **Applications** (One-to-Many): Freelancer submits applications
4. **Categories** → **Jobs** (One-to-Many): Job belongs to category
5. **Jobs** ↔ **Skills** (Many-to-Many): Job requires skills
6. **Users** ↔ **Skills** (Many-to-Many): User has skills
7. **Users** → **Activity Log** (One-to-Many): User actions logged
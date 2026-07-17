<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $freelancers = User::where('role', 'member')->get();
        $publishedJobs = Job::where('status', 'published')->get();
        $closedJobs = Job::where('status', 'closed')->get();

        // Create applications for published jobs
        foreach ($publishedJobs as $job) {
            $applicationsCount = rand(1, 8); // Random number of applications per job

            for ($i = 0; $i < $applicationsCount; $i++) {
                $freelancer = $freelancers->where('id', '!=', $job->client_id)->random();

                // Check if freelancer already applied to this job
                $existingApplication = Application::where('freelancer_id', $freelancer->id)
                    ->where('job_id', $job->id)
                    ->first();

                if (! $existingApplication) {
                    // Create different types of applications
                    $rand = rand(1, 100);

                    if ($rand <= 40) {
                        // 40% pending applications
                        $application = Application::factory()
                            ->pending()
                            ->create([
                                'freelancer_id' => $freelancer->id,
                                'job_id' => $job->id,
                            ]);
                    } elseif ($rand <= 60) {
                        // 20% reviewing applications
                        $application = Application::factory()
                            ->reviewing()
                            ->create([
                                'freelancer_id' => $freelancer->id,
                                'job_id' => $job->id,
                            ]);
                    } elseif ($rand <= 75) {
                        // 15% shortlisted applications
                        $application = Application::factory()
                            ->shortlisted()
                            ->create([
                                'freelancer_id' => $freelancer->id,
                                'job_id' => $job->id,
                            ]);
                    } elseif ($rand <= 85) {
                        // 10% accepted applications
                        $application = Application::factory()
                            ->accepted()
                            ->create([
                                'freelancer_id' => $freelancer->id,
                                'job_id' => $job->id,
                            ]);
                    } elseif ($rand <= 95) {
                        // 10% rejected applications
                        $application = Application::factory()
                            ->rejected()
                            ->create([
                                'freelancer_id' => $freelancer->id,
                                'job_id' => $job->id,
                            ]);
                    } else {
                        // 5% completed applications
                        $application = Application::factory()
                            ->completed()
                            ->create([
                                'freelancer_id' => $freelancer->id,
                                'job_id' => $job->id,
                            ]);
                    }
                }
            }

            // Update job applications count
            $job->applications_count = $job->applications()->count();
            $job->save();
        }

        // Create applications for closed jobs (these should be completed or rejected)
        foreach ($closedJobs as $job) {
            $applicationsCount = rand(3, 12); // More applications for closed jobs

            for ($i = 0; $i < $applicationsCount; $i++) {
                $freelancer = $freelancers->where('id', '!=', $job->client_id)->random();

                // Check if freelancer already applied to this job
                $existingApplication = Application::where('freelancer_id', $freelancer->id)
                    ->where('job_id', $job->id)
                    ->first();

                if (! $existingApplication) {
                    $rand = rand(1, 100);

                    if ($rand <= 20) {
                        // 20% completed applications
                        $application = Application::factory()
                            ->completed()
                            ->create([
                                'freelancer_id' => $freelancer->id,
                                'job_id' => $job->id,
                            ]);
                    } elseif ($rand <= 40) {
                        // 20% accepted applications
                        $application = Application::factory()
                            ->accepted()
                            ->create([
                                'freelancer_id' => $freelancer->id,
                                'job_id' => $job->id,
                            ]);
                    } else {
                        // 60% rejected applications
                        $application = Application::factory()
                            ->rejected()
                            ->create([
                                'freelancer_id' => $freelancer->id,
                                'job_id' => $job->id,
                            ]);
                    }
                }
            }

            // Update job applications count
            $job->applications_count = $job->applications()->count();
            $job->save();
        }

        // Create sample applications with specific data for testing
        $this->createSampleApplications();

        $this->command->info('Applications seeded successfully!');
    }

    /**
     * Create sample applications for testing specific scenarios
     */
    private function createSampleApplications(): void
    {
        $freelancers = User::where('role', 'member')->limit(5)->get();
        $jobs = Job::where('status', 'published')->limit(3)->get();

        $sampleApplications = [
            [
                'cover_letter' => 'I am very interested in this Laravel development project. I have over 3 years of experience working with Laravel framework and have built several inventory management systems. My expertise includes Laravel, PHP, MySQL, and API development. I can deliver this project within the specified timeline and budget. Please check my portfolio for similar projects.',
                'proposed_rate' => 25000,
                'estimated_hours' => 320, // 8 weeks
                'status' => 'pending',
                'portfolio_links' => json_encode([
                    'https://github.com/developer/laravel-inventory',
                    'https://portfolio.example.com/laravel-projects',
                    'https://demo.inventoryapp.com',
                ]),
            ],
            [
                'cover_letter' => 'Hello! I am a UI/UX designer with 4+ years of experience in mobile app design. I have designed over 20 mobile applications with a focus on user experience and modern design principles. I use Figma and Adobe XD for my designs and can provide interactive prototypes. I would love to work on your food delivery app design.',
                'proposed_rate' => 12000,
                'estimated_hours' => 120, // 3 weeks
                'status' => 'shortlisted',
                'portfolio_links' => json_encode([
                    'https://behance.net/designer/mobile-designs',
                    'https://figma.com/food-app-design',
                    'https://dribbble.com/shots/mobile-ui',
                ]),
            ],
            [
                'cover_letter' => 'I am an experienced content writer specializing in technology and web development topics. I have written for several tech blogs and have a deep understanding of programming concepts. I can write engaging, SEO-optimized articles that are both informative and easy to understand. My writing style is clear and accessible to both technical and non-technical audiences.',
                'proposed_rate' => 400,
                'estimated_hours' => 16, // 2 days per article
                'status' => 'accepted',
                'portfolio_links' => json_encode([
                    'https://medium.com/@techwriter/articles',
                    'https://dev.to/techwriter',
                    'https://techblog.example.com/author/writer',
                ]),
            ],
        ];

        foreach ($sampleApplications as $index => $applicationData) {
            if (isset($freelancers[$index]) && isset($jobs[$index])) {
                $freelancer = $freelancers
                    ->where('id', '!=', $jobs[$index]->client_id)
                    ->first();

                if (! $freelancer) {
                    continue;
                }

                Application::updateOrCreate(
                    [
                        'freelancer_id' => $freelancer->id,
                        'job_id' => $jobs[$index]->id,
                    ],
                    [
                        'cover_letter' => $applicationData['cover_letter'],
                        'proposed_rate' => $applicationData['proposed_rate'],
                        'estimated_hours' => $applicationData['estimated_hours'],
                        'status' => $applicationData['status'],
                        'portfolio_links' => $applicationData['portfolio_links'],
                        'reviewed_at' => in_array($applicationData['status'], ['shortlisted', 'accepted', 'rejected']) ? now() : null,
                    ]
                );
            }
        }
    }
}

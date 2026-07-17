<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Job;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = User::where('role', 'member')->get();
        $categories = Category::where('is_active', true)->get();
        $skills = Skill::where('is_active', true)->get();

        // Create specific sample jobs
        $sampleJobs = [
            [
                'title' => 'E-commerce Website Development',
                'description' => 'We need a complete e-commerce website for our small business. The site should include product catalog, shopping cart, payment integration, and admin panel. We prefer WordPress with WooCommerce but are open to other solutions.',
                'requirements' => 'Experience with e-commerce development, WordPress/WooCommerce, payment gateway integration, responsive design, and SEO optimization.',
                'budget_min' => 15000,
                'budget_max' => 25000,
                'type' => 'full-time',
                'experience_level' => 'entry',
                'status' => 'published',
                'category' => 'E-commerce',
                'skills' => ['WordPress', 'WooCommerce', 'PHP', 'JavaScript', 'HTML', 'CSS'],
            ],
            [
                'title' => 'Mobile App UI/UX Design',
                'description' => 'Looking for a talented UI/UX designer to create an intuitive and modern design for our mobile application. The app is for food delivery service targeting young professionals.',
                'requirements' => 'Portfolio of mobile app designs, experience with design tools (Figma/Sketch), understanding of user experience principles, and ability to create interactive prototypes.',
                'budget_min' => 8000,
                'budget_max' => 15000,
                'type' => 'part-time',
                'experience_level' => 'intermediate',
                'status' => 'published',
                'category' => 'UI/UX Design',
                'skills' => ['Figma', 'Adobe XD', 'Sketch', 'UI/UX Design'],
            ],
            [
                'title' => 'Content Writing for Tech Blog',
                'description' => 'We need experienced tech writers to create engaging blog posts about web development, programming tutorials, and technology trends. Must be able to write in clear, accessible language.',
                'requirements' => 'Strong writing skills, knowledge of web development technologies, experience with SEO writing, and ability to research technical topics.',
                'budget_min' => 300,
                'budget_max' => 500,
                'type' => 'internship',
                'experience_level' => 'entry',
                'status' => 'published',
                'category' => 'Content Writing',
                'skills' => ['Content Writing', 'SEO', 'Technical Writing'],
            ],
            [
                'title' => 'Social Media Management',
                'description' => 'Small business needs someone to manage social media presence across Facebook, Instagram, and Twitter. Includes content creation, posting schedule, and engagement with followers.',
                'requirements' => 'Experience with social media management, content creation skills, basic graphic design, and knowledge of social media analytics.',
                'budget_min' => 5000,
                'budget_max' => 8000,
                'type' => 'part-time',
                'experience_level' => 'entry',
                'status' => 'published',
                'category' => 'Social Media Management',
                'skills' => ['Social Media Marketing', 'Content Marketing', 'Canva', 'Facebook Ads'],
            ],
            [
                'title' => 'Data Entry and Processing',
                'description' => 'Need assistance with data entry from physical documents to digital format. Approximately 500 records need to be processed with high accuracy.',
                'requirements' => 'Excellent attention to detail, fast and accurate typing, experience with Excel/Google Sheets, and ability to maintain data confidentiality.',
                'budget_min' => 2000,
                'budget_max' => 3000,
                'type' => 'internship',
                'experience_level' => 'entry',
                'status' => 'published',
                'category' => 'Data Entry',
                'skills' => ['Data Entry', 'Microsoft Excel', 'Google Sheets'],
            ],
            [
                'title' => 'Laravel Web Application Development',
                'description' => 'Looking for an experienced Laravel developer to build a custom web application for inventory management. The system should include user authentication, CRUD operations, reporting, and API integration.',
                'requirements' => 'Strong experience with Laravel framework, MySQL database, RESTful API development, and modern JavaScript. Portfolio of Laravel projects required.',
                'budget_min' => 20000,
                'budget_max' => 35000,
                'type' => 'full-time',
                'experience_level' => 'expert',
                'status' => 'published',
                'category' => 'Web Development',
                'skills' => ['Laravel', 'PHP', 'MySQL', 'JavaScript', 'REST API'],
            ],
            [
                'title' => 'Logo Design for New Restaurant',
                'description' => 'New restaurant needs a professional logo design that reflects our modern Filipino cuisine concept. Looking for creative and unique design that works well in both digital and print media.',
                'requirements' => 'Portfolio of logo designs, experience with restaurant/food industry branding, proficiency in Adobe Illustrator or similar tools, and ability to provide multiple concepts.',
                'budget_min' => 3000,
                'budget_max' => 6000,
                'type' => 'part-time',
                'experience_level' => 'intermediate',
                'status' => 'published',
                'category' => 'Logo Design',
                'skills' => ['Adobe Illustrator', 'Logo Design', 'Graphic Design'],
            ],
            [
                'title' => 'Video Editing for YouTube Channel',
                'description' => 'Educational YouTube channel needs video editor for weekly uploads. Content includes tutorials, interviews, and presentations that need professional editing with graphics and transitions.',
                'requirements' => 'Experience with video editing software, knowledge of YouTube optimization, ability to add graphics and animations, and fast turnaround time.',
                'budget_min' => 1000,
                'budget_max' => 2000,
                'type' => 'internship',
                'experience_level' => 'entry',
                'status' => 'published',
                'category' => 'Video Editing',
                'skills' => ['Adobe Premiere Pro', 'After Effects', 'Video Editing'],
            ],
        ];

        foreach ($sampleJobs as $jobData) {
            $category = $categories->where('name', $jobData['category'])->first();
            $client = $clients->random();

            $job = Job::create([
                'title' => $jobData['title'],
                'description' => $jobData['description'],
                'requirements' => $jobData['requirements'],
                'budget_min' => $jobData['budget_min'],
                'budget_max' => $jobData['budget_max'],
                'type' => $jobData['type'],
                'experience_level' => $jobData['experience_level'],
                'deadline' => fake()->dateTimeBetween('+1 week', '+2 months'),
                'status' => $jobData['status'],
                'is_featured' => fake()->boolean(30),
                'applications_count' => 0, // Will be updated when applications are created
                'client_id' => $client->id,
                'category_id' => $category->id,
            ]);

            // Attach skills to job
            $jobSkills = $skills->whereIn('name', $jobData['skills']);
            $job->skills()->attach($jobSkills->pluck('id'));
        }

        // Create additional random jobs
        $randomJobs = Job::factory()
            ->count(40)
            ->published()
            ->create();

        // Attach random skills to the generated jobs
        foreach ($randomJobs as $job) {
            $randomSkills = $skills->random(rand(2, 6));
            $job->skills()->attach($randomSkills->pluck('id'));
        }

        // Create some draft jobs
        $draftJobs = Job::factory()
            ->count(8)
            ->draft()
            ->create();

        foreach ($draftJobs as $job) {
            $randomSkills = $skills->random(rand(2, 5));
            $job->skills()->attach($randomSkills->pluck('id'));
        }

        // Create some closed jobs
        $closedJobs = Job::factory()
            ->count(12)
            ->closed()
            ->create();

        foreach ($closedJobs as $job) {
            $randomSkills = $skills->random(rand(2, 5));
            $job->skills()->attach($randomSkills->pluck('id'));
        }

        // Create some featured jobs
        $featuredJobs = Job::factory()
            ->count(5)
            ->featured()
            ->published()
            ->create();

        foreach ($featuredJobs as $job) {
            $randomSkills = $skills->random(rand(3, 7));
            $job->skills()->attach($randomSkills->pluck('id'));
        }

        $this->command->info('Jobs seeded successfully!');
    }
}

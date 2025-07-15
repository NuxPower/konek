<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            // Programming Languages
            ['name' => 'PHP', 'description' => 'Server-side scripting language for web development'],
            ['name' => 'JavaScript', 'description' => 'Client-side scripting language for interactive web pages'],
            ['name' => 'Python', 'description' => 'High-level programming language for various applications'],
            ['name' => 'Java', 'description' => 'Object-oriented programming language'],
            ['name' => 'C++', 'description' => 'General-purpose programming language'],
            ['name' => 'C#', 'description' => 'Programming language developed by Microsoft'],
            ['name' => 'Ruby', 'description' => 'Dynamic programming language for web development'],
            
            // Frameworks & Libraries
            ['name' => 'Laravel', 'description' => 'PHP web application framework'],
            ['name' => 'React', 'description' => 'JavaScript library for building user interfaces'],
            ['name' => 'Vue.js', 'description' => 'Progressive JavaScript framework'],
            ['name' => 'Angular', 'description' => 'TypeScript-based web application framework'],
            ['name' => 'Node.js', 'description' => 'JavaScript runtime for server-side development'],
            ['name' => 'Django', 'description' => 'High-level Python web framework'],
            ['name' => 'Express.js', 'description' => 'Web application framework for Node.js'],
            ['name' => 'Bootstrap', 'description' => 'CSS framework for responsive design'],
            ['name' => 'Tailwind CSS', 'description' => 'Utility-first CSS framework'],
            ['name' => 'jQuery', 'description' => 'JavaScript library for DOM manipulation'],
            
            // Mobile Development
            ['name' => 'React Native', 'description' => 'Framework for building mobile apps'],
            ['name' => 'Flutter', 'description' => 'UI toolkit for mobile app development'],
            ['name' => 'Android Development', 'description' => 'Native Android app development'],
            ['name' => 'iOS Development', 'description' => 'Native iOS app development'],
            ['name' => 'Xamarin', 'description' => 'Cross-platform mobile development'],
            
            // Database Technologies
            ['name' => 'MySQL', 'description' => 'Relational database management system'],
            ['name' => 'PostgreSQL', 'description' => 'Advanced relational database'],
            ['name' => 'MongoDB', 'description' => 'NoSQL document database'],
            ['name' => 'SQLite', 'description' => 'Lightweight database engine'],
            ['name' => 'Redis', 'description' => 'In-memory data structure store'],
            
            // Web Technologies
            ['name' => 'HTML', 'description' => 'Markup language for web pages'],
            ['name' => 'CSS', 'description' => 'Style sheet language for web design'],
            ['name' => 'SASS', 'description' => 'CSS preprocessor'],
            ['name' => 'REST API', 'description' => 'Web service architecture'],
            ['name' => 'GraphQL', 'description' => 'Query language for APIs'],
            ['name' => 'WordPress', 'description' => 'Content management system'],
            ['name' => 'WooCommerce', 'description' => 'E-commerce plugin for WordPress'],
            ['name' => 'Shopify', 'description' => 'E-commerce platform'],
            
            // Design & Creative
            ['name' => 'Adobe Photoshop', 'description' => 'Image editing software'],
            ['name' => 'Adobe Illustrator', 'description' => 'Vector graphics editor'],
            ['name' => 'Adobe InDesign', 'description' => 'Desktop publishing software'],
            ['name' => 'Figma', 'description' => 'Collaborative design tool'],
            ['name' => 'Canva', 'description' => 'Graphic design platform'],
            ['name' => 'Sketch', 'description' => 'Digital design toolkit'],
            ['name' => 'Adobe XD', 'description' => 'User experience design software'],
            ['name' => 'CorelDRAW', 'description' => 'Vector graphics editor'],
            
            // Video & Audio
            ['name' => 'Adobe Premiere Pro', 'description' => 'Video editing software'],
            ['name' => 'Final Cut Pro', 'description' => 'Video editing software for Mac'],
            ['name' => 'After Effects', 'description' => 'Motion graphics and visual effects'],
            ['name' => 'DaVinci Resolve', 'description' => 'Video editing and color correction'],
            ['name' => 'Audacity', 'description' => 'Audio editing software'],
            
            // Digital Marketing
            ['name' => 'SEO', 'description' => 'Search engine optimization'],
            ['name' => 'Google Analytics', 'description' => 'Web analytics service'],
            ['name' => 'Google Ads', 'description' => 'Online advertising platform'],
            ['name' => 'Facebook Ads', 'description' => 'Social media advertising'],
            ['name' => 'Email Marketing', 'description' => 'Marketing via email campaigns'],
            ['name' => 'Content Marketing', 'description' => 'Strategic content creation'],
            ['name' => 'Social Media Marketing', 'description' => 'Marketing on social platforms'],
            
            // Business & Office
            ['name' => 'Microsoft Excel', 'description' => 'Spreadsheet application'],
            ['name' => 'Google Sheets', 'description' => 'Online spreadsheet tool'],
            ['name' => 'PowerPoint', 'description' => 'Presentation software'],
            ['name' => 'QuickBooks', 'description' => 'Accounting software'],
            ['name' => 'Xero', 'description' => 'Cloud-based accounting software'],
            ['name' => 'Salesforce', 'description' => 'Customer relationship management'],
            
            // Development Tools
            ['name' => 'Git', 'description' => 'Version control system'],
            ['name' => 'GitHub', 'description' => 'Git repository hosting service'],
            ['name' => 'Docker', 'description' => 'Containerization platform'],
            ['name' => 'AWS', 'description' => 'Amazon Web Services cloud platform'],
            ['name' => 'Google Cloud', 'description' => 'Cloud computing services'],
            ['name' => 'Linux', 'description' => 'Open-source operating system'],
            
            // Languages
            ['name' => 'English', 'description' => 'English language proficiency'],
            ['name' => 'Filipino', 'description' => 'Filipino language proficiency'],
            ['name' => 'Cebuano', 'description' => 'Cebuano language proficiency'],
            ['name' => 'Spanish', 'description' => 'Spanish language proficiency'],
            ['name' => 'Japanese', 'description' => 'Japanese language proficiency'],
            
            // Soft Skills
            ['name' => 'Project Management', 'description' => 'Planning and managing projects'],
            ['name' => 'Customer Service', 'description' => 'Customer support and relations'],
            ['name' => 'Data Entry', 'description' => 'Accurate data input and processing'],
            ['name' => 'Virtual Assistant', 'description' => 'Remote administrative support'],
            ['name' => 'Technical Writing', 'description' => 'Technical documentation'],
            ['name' => 'Content Writing', 'description' => 'Creative and informative writing'],
            ['name' => 'Copywriting', 'description' => 'Persuasive marketing copy'],
            ['name' => 'Translation', 'description' => 'Language translation services'],
            ['name' => 'Transcription', 'description' => 'Audio to text conversion'],
            
            // Specialized Skills
            ['name' => 'Machine Learning', 'description' => 'AI and machine learning'],
            ['name' => 'Data Analysis', 'description' => 'Data interpretation and insights'],
            ['name' => 'Cybersecurity', 'description' => 'Information security'],
            ['name' => 'Blockchain', 'description' => 'Blockchain technology'],
            ['name' => 'Game Development', 'description' => 'Video game creation'],
            ['name' => 'Unity', 'description' => 'Game development platform'],
            ['name' => 'Unreal Engine', 'description' => 'Game development engine'],
        ];

        foreach ($skills as $skill) {
            $baseSlug = Str::slug($skill['name']);
            // Fallback if slug is empty
            if (empty($baseSlug)) {
                $baseSlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $skill['name']));
                $baseSlug = trim($baseSlug, '-');
                if (empty($baseSlug)) {
                    $baseSlug = 'skill-' . Str::random(5);
                }
            }
            $slug = $baseSlug;
            $counter = 1;
            // Ensure uniqueness of slug
            while (Skill::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            // Final robust check: ensure slug is a valid, non-empty, non-'?' string with only slug characters
            if (empty($slug) || !is_string($slug) || $slug === '?' || !preg_match('/^[a-z0-9\-]+$/', $slug)) {
                $slug = 'skill-' . Str::random(8);
            }
            if (isset($this->command)) {
                $this->command->info('Seeding skill: ' . $skill['name'] . ' | Slug: ' . $slug);
            }
            Skill::create([
                'name' => $skill['name'],
                'description' => $skill['description'],
                'slug' => $slug,
                'is_active' => true,
            ]);
        }

        // Create a few inactive skills
        // Skill::factory()
        //     ->count(5)
        //     ->inactive()
        //     ->create();

        // Attach skills to freelancers
        $this->attachSkillsToFreelancers();

        $this->command->info('Skills seeded successfully!');
    }

    /**
     * Attach random skills to freelancers
     */
    private function attachSkillsToFreelancers(): void
    {
        $freelancers = User::where('role', 'freelancer')->get();
        $skills = Skill::where('is_active', true)->get();

        foreach ($freelancers as $freelancer) {
            // Attach 3-8 random skills to each freelancer
            $randomSkills = $skills->random(rand(3, 8));
            $freelancer->skills()->syncWithoutDetaching($randomSkills->pluck('id'));
        }

        $this->command->info('Skills attached to freelancers successfully!');
    }
}
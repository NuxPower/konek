<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Web Development',
                'description' => 'Full-stack web development, front-end and back-end development services including HTML, CSS, JavaScript, PHP, and various frameworks.',
                'is_active' => true,
            ],
            [
                'name' => 'Mobile Development',
                'description' => 'iOS and Android app development, cross-platform mobile applications using React Native, Flutter, and native development.',
                'is_active' => true,
            ],
            [
                'name' => 'Graphic Design',
                'description' => 'Visual design services including logo design, branding, print design, digital graphics, and creative visual solutions.',
                'is_active' => true,
            ],
            [
                'name' => 'Content Writing',
                'description' => 'Professional writing services including blog posts, articles, copywriting, technical writing, and content strategy.',
                'is_active' => true,
            ],
            [
                'name' => 'Digital Marketing',
                'description' => 'Online marketing services including SEO, social media marketing, PPC advertising, email marketing, and digital strategy.',
                'is_active' => true,
            ],
            [
                'name' => 'Data Entry',
                'description' => 'Data processing, data entry, data mining, spreadsheet management, and database administration services.',
                'is_active' => true,
            ],
            [
                'name' => 'Translation',
                'description' => 'Language translation services for documents, websites, and multimedia content in various languages.',
                'is_active' => true,
            ],
            [
                'name' => 'Video Editing',
                'description' => 'Video production and editing services including promotional videos, tutorials, documentaries, and multimedia content.',
                'is_active' => true,
            ],
            [
                'name' => 'Photography',
                'description' => 'Professional photography services including event photography, product photography, and photo editing.',
                'is_active' => true,
            ],
            [
                'name' => 'Accounting',
                'description' => 'Financial services including bookkeeping, accounting, tax preparation, and financial analysis.',
                'is_active' => true,
            ],
            [
                'name' => 'Virtual Assistant',
                'description' => 'Administrative support services including email management, scheduling, customer service, and general assistance.',
                'is_active' => true,
            ],
            [
                'name' => 'SEO Services',
                'description' => 'Search engine optimization services to improve website visibility and ranking on search engines.',
                'is_active' => true,
            ],
            [
                'name' => 'Social Media Management',
                'description' => 'Social media strategy, content creation, community management, and social media advertising services.',
                'is_active' => true,
            ],
            [
                'name' => 'UI/UX Design',
                'description' => 'User interface and user experience design for websites, mobile apps, and digital products.',
                'is_active' => true,
            ],
            [
                'name' => 'Database Administration',
                'description' => 'Database design, management, optimization, and maintenance services for various database systems.',
                'is_active' => true,
            ],
            [
                'name' => 'Technical Writing',
                'description' => 'Technical documentation, user manuals, API documentation, and technical content creation.',
                'is_active' => true,
            ],
            [
                'name' => 'E-commerce',
                'description' => 'Online store development, e-commerce platform setup, product management, and online business solutions.',
                'is_active' => true,
            ],
            [
                'name' => 'WordPress Development',
                'description' => 'WordPress website development, theme customization, plugin development, and WordPress maintenance.',
                'is_active' => true,
            ],
            [
                'name' => 'Logo Design',
                'description' => 'Professional logo design and branding services for businesses and organizations.',
                'is_active' => true,
            ],
            [
                'name' => 'Tutoring',
                'description' => 'Educational services including academic tutoring, skill training, and educational content development.',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            $slug = Str::slug($category['name']);
            if (Category::where('slug', $slug)->exists()) {
                continue; // Skip if slug already exists
            }
            Category::create([
                'name' => $category['name'],
                'description' => $category['description'],
                'slug' => $slug,
                'is_active' => $category['is_active'],
            ]);
        }

        // Create a few inactive categories
        // Category::factory()
        //     ->count(5)
        //     ->inactive()
        //     ->create();

        $this->command->info('Categories seeded successfully!');
    }
}

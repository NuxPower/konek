<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catalog = [
            'Web & Software Development' => [
                'description' => 'Websites, software tools, automation, and application development.',
                'skills' => [
                    'HTML', 'CSS', 'JavaScript', 'PHP', 'Laravel', 'Python', 'Java', 'C++', 'SQL',
                    'React', 'Vue.js', 'Node.js', 'Git', 'API Integration', 'WordPress',
                ],
            ],
            'Design & Creative Media' => [
                'description' => 'Visual design, branding, multimedia, and creative production.',
                'skills' => [
                    'Graphic Design', 'Logo Design', 'Layout Design', 'Canva', 'Adobe Photoshop',
                    'Adobe Illustrator', 'Figma', 'Video Editing', 'Photo Editing', 'Photography',
                    'Motion Graphics', 'Digital Illustration',
                ],
            ],
            'Writing, Communication & Languages' => [
                'description' => 'Writing, editing, public communication, and translation.',
                'skills' => [
                    'Academic Writing', 'Creative Writing', 'Copywriting', 'Technical Writing',
                    'Proofreading', 'Editing', 'Public Speaking', 'Presentation Design',
                    'Translation', 'Filipino Language', 'English Communication', 'Content Writing',
                ],
            ],
            'Education & Tutoring' => [
                'description' => 'Academic support, coaching, teaching, and peer learning.',
                'skills' => [
                    'Math Tutoring', 'Science Tutoring', 'English Tutoring', 'Research Tutoring',
                    'Peer Mentoring', 'Lesson Planning', 'Study Coaching', 'Exam Review Support',
                    'Laboratory Assistance',
                ],
            ],
            'Business, Marketing & Administration' => [
                'description' => 'Operational support, promotion, coordination, and business tasks.',
                'skills' => [
                    'Data Entry', 'Microsoft Excel', 'Google Sheets', 'Bookkeeping',
                    'Customer Service', 'Virtual Assistance', 'Event Coordination',
                    'Social Media Management', 'Digital Marketing', 'Market Research',
                    'Sales Support', 'Inventory Management',
                ],
            ],
            'Research, Data & Analysis' => [
                'description' => 'Research assistance, fieldwork, data handling, and reporting.',
                'skills' => [
                    'Survey Design', 'Data Collection', 'Data Cleaning', 'Statistical Analysis',
                    'SPSS', 'R Programming', 'Report Writing', 'Literature Review',
                    'Field Research', 'Interviewing', 'Transcription',
                ],
            ],
            'Agriculture, Food & Environment' => [
                'description' => 'Agricultural, food, environmental, and campus field skills.',
                'skills' => [
                    'Crop Production', 'Animal Care', 'Food Preparation', 'Food Safety',
                    'Gardening', 'Landscaping', 'Environmental Monitoring', 'Composting',
                    'Farm Record Keeping',
                ],
            ],
            'Health, Wellness & Community' => [
                'description' => 'Health promotion, wellness support, outreach, and service work.',
                'skills' => [
                    'First Aid', 'Basic Life Support', 'Health Education', 'Community Outreach',
                    'Counseling Support', 'Fitness Coaching', 'Sports Officiating',
                    'Event Safety Support',
                ],
            ],
            'Technical, Trades & Practical Services' => [
                'description' => 'Hands-on technical, repair, setup, and practical service work.',
                'skills' => [
                    'Computer Troubleshooting', 'Network Setup', 'Audio-Visual Setup',
                    'Electronics Repair', 'Basic Carpentry', 'Basic Electrical Work',
                    'Printing Services', 'Equipment Maintenance', 'Driving',
                ],
            ],
            'Arts, Music & Performance' => [
                'description' => 'Performing arts, music, cultural work, and event entertainment.',
                'skills' => [
                    'Singing', 'Guitar', 'Keyboard', 'Dance', 'Theater Performance',
                    'Hosting', 'Voice Over', 'Stage Management', 'Music Arrangement',
                ],
            ],
        ];

        foreach ($catalog as $categoryName => $categoryData) {
            $category = Category::updateOrCreate(
                ['name' => $categoryName],
                [
                    'slug' => $this->uniqueSlug(Category::class, $categoryName),
                    'description' => $categoryData['description'],
                    'is_active' => true,
                ]
            );

            foreach ($categoryData['skills'] as $skillName) {
                Skill::updateOrCreate(
                    ['name' => $skillName],
                    [
                        'slug' => $this->uniqueSlug(Skill::class, $skillName),
                        'category_id' => $category->id,
                        'description' => "{$skillName} skill for student freelance and campus work.",
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('University-wide skills seeded successfully.');
    }

    private function uniqueSlug(string $modelClass, string $name): string
    {
        $base = Str::slug($name) ?: 'item';
        $slug = $base;
        $suffix = 1;

        while ($modelClass::query()
            ->where('slug', $slug)
            ->where('name', '!=', $name)
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}

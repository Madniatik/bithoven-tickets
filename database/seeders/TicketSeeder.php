<?php

namespace Bithoven\Tickets\Database\Seeders;

use Illuminate\Database\Seeder;
use Bithoven\Tickets\Models\TicketCategory;
use Bithoven\Tickets\Models\Ticket;
use App\Models\User;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default categories
        $categories = [
            [
                'name' => 'Technical Support',
                'slug' => 'technical-support',
                'description' => 'Technical issues and bug reports',
                'color' => '#3b82f6',
                'icon' => 'fa-wrench',
                'sort_order' => 1,
            ],
            [
                'name' => 'Billing',
                'slug' => 'billing',
                'description' => 'Billing and payment related issues',
                'color' => '#10b981',
                'icon' => 'fa-dollar-sign',
                'sort_order' => 2,
            ],
            [
                'name' => 'Feature Request',
                'slug' => 'feature-request',
                'description' => 'Suggestions for new features',
                'color' => '#8b5cf6',
                'icon' => 'fa-lightbulb',
                'sort_order' => 3,
            ],
            [
                'name' => 'General Inquiry',
                'slug' => 'general-inquiry',
                'description' => 'General questions and inquiries',
                'color' => '#6366f1',
                'icon' => 'fa-question-circle',
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $categoryData) {
            TicketCategory::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        $this->command->info('Ticket categories created successfully.');

        // Optionally create sample tickets (only in development)
        if (app()->environment('local')) {
            $users = User::limit(3)->get();
            
            if ($users->count() > 0) {
                $techCategory = TicketCategory::where('slug', 'technical-support')->first();
                
                Ticket::create([
                    'subject' => 'Sample Ticket - Login Issue',
                    'description' => 'I am unable to login to my account. Getting an error message.',
                    'priority' => 'high',
                    'status' => 'open',
                    'user_id' => $users->first()->id,
                    'category_id' => $techCategory?->id,
                ]);

                $this->command->info('Sample ticket created.');
            }
        }
    }
}

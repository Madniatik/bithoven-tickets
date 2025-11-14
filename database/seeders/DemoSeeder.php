<?php

namespace Bithoven\Tickets\Database\Seeders;

use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds for demo data.
     *
     * This seeder creates ONLY demo tickets with comments.
     * Essential data (categories, templates, responses, rules) 
     * are seeded during installation via DatabaseSeeder.
     *
     * Can be run multiple times safely - skips if demo tickets already exist.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Tickets Demo Data...');

        // Check if demo tickets already exist
        $existingTickets = \Bithoven\Tickets\Models\Ticket::count();

        if ($existingTickets > 0) {
            $this->command->warn('⚠️  Demo tickets already exist!');
            $this->command->info("   Tickets: {$existingTickets}");
            $this->command->newLine();
            $this->command->info('💡 To reload demo data, delete existing tickets first.');
            return;
        }

        // Run ONLY demo tickets seeder
        $this->call([
            TicketsDemoSeeder::class,
        ]);

        $this->command->info('✅ Demo tickets loaded successfully!');
    }
}

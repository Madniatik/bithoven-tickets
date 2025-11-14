<?php

namespace Bithoven\Tickets\Database\Seeders;

use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds for demo data.
     * 
     * This seeder orchestrates all demo data seeders for the tickets extension.
     * It can be run multiple times safely as all seeders use updateOrCreate or similar.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Tickets Demo Seeders...');
        
        $this->call([
            CategorySeeder::class,
            TemplatesResponsesSeeder::class,
            AutomationRulesSeeder::class,
            TicketsDemoSeeder::class,
        ]);
        
        $this->command->info('✅ All demo data loaded successfully!');
    }
}

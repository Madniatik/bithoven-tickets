<?php

namespace Bithoven\Tickets\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder runs during extension installation to populate
     * essential data: categories, templates, canned responses, and automation rules.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding essential Tickets data...');
        
        $this->call([
            CategorySeeder::class,
            TemplatesResponsesSeeder::class,
            AutomationRulesSeeder::class,
        ]);
        
        $this->command->info('✅ Essential data loaded successfully!');
    }
}

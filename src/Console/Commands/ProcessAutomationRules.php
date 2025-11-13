<?php

namespace Bithoven\Tickets\Console\Commands;

use Bithoven\Tickets\Models\Ticket;
use Bithoven\Tickets\Models\TicketAutomationRule;
use Illuminate\Console\Command;

class ProcessAutomationRules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:process-automation 
                            {--type= : Process only specific rule type}
                            {--dry-run : Show what would happen without executing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process ticket automation rules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $dryRun = $this->option('dry-run');

        $this->info('🤖 Processing Ticket Automation Rules...');
        
        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No changes will be made');
        }

        // Get active rules
        $rules = TicketAutomationRule::active()->ordered();
        
        if ($type) {
            $rules = $rules->byType($type);
        }
        
        $rules = $rules->get();

        if ($rules->isEmpty()) {
            $this->warn('No active automation rules found.');
            return 0;
        }

        $this->info("Found {$rules->count()} active rule(s)");

        $totalProcessed = 0;
        $totalMatched = 0;
        $totalExecuted = 0;

        foreach ($rules as $rule) {
            $this->line('');
            $this->info("📋 Processing: {$rule->name} ({$rule->type_label})");

            // Get tickets to process based on rule type
            $tickets = $this->getTicketsForRule($rule);
            
            $totalProcessed += $tickets->count();

            if ($tickets->isEmpty()) {
                $this->comment('  No tickets match this rule');
                continue;
            }

            $matched = 0;
            $executed = 0;

            foreach ($tickets as $ticket) {
                if ($rule->matches($ticket)) {
                    $matched++;
                    $totalMatched++;

                    $this->comment("  ✓ Ticket #{$ticket->ticket_number} matches conditions");

                    if (!$dryRun) {
                        if ($rule->execute($ticket)) {
                            $executed++;
                            $totalExecuted++;
                            $this->info("    → Actions executed successfully");
                        } else {
                            $this->error("    → Failed to execute actions");
                        }
                    } else {
                        $this->warn("    → [DRY RUN] Would execute: " . json_encode($rule->actions));
                    }
                }
            }

            $this->comment("  Matched: {$matched}, Executed: " . ($dryRun ? '0 (dry run)' : $executed));
        }

        $this->line('');
        $this->info('✅ Automation processing complete!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Tickets Evaluated', $totalProcessed],
                ['Rules Matched', $totalMatched],
                ['Actions Executed', $dryRun ? '0 (dry run)' : $totalExecuted],
            ]
        );

        return 0;
    }

    /**
     * Get tickets to evaluate for a specific rule
     */
    protected function getTicketsForRule(TicketAutomationRule $rule)
    {
        $query = Ticket::query();

        // Pre-filter based on rule type to reduce load
        switch ($rule->type) {
            case TicketAutomationRule::TYPE_AUTO_CLOSE:
                // Only check resolved tickets
                $query->where('status', 'resolved');
                break;

            case TicketAutomationRule::TYPE_AUTO_ESCALATE:
                // Only check open/in_progress tickets
                $query->whereIn('status', ['open', 'in_progress']);
                break;

            case TicketAutomationRule::TYPE_AUTO_ASSIGN:
                // Only check unassigned tickets
                $query->whereNull('assigned_to');
                break;

            case TicketAutomationRule::TYPE_AUTO_RESPONSE:
                // Check pending tickets
                $query->where('status', 'pending');
                break;
        }

        // Don't process closed tickets
        $query->where('status', '!=', 'closed');

        return $query->with(['user', 'category', 'comments'])->get();
    }
}

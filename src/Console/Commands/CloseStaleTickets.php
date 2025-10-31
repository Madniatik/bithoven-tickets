<?php

namespace Bithoven\Tickets\Console\Commands;

use Illuminate\Console\Command;
use Bithoven\Tickets\Services\TicketService;

class CloseStaleTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:close-stale 
                            {--days= : Number of days after resolution to close tickets}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close resolved tickets that have been stale for a specified number of days';

    /**
     * Execute the console command.
     */
    public function handle(TicketService $ticketService): int
    {
        $days = $this->option('days') ?? config('tickets.close_after_days', 30);
        
        $this->info("Closing tickets resolved more than {$days} days ago...");
        
        $count = $ticketService->closeStaleTickets($days);
        
        $this->info("Closed {$count} ticket(s)");
        
        return self::SUCCESS;
    }
}

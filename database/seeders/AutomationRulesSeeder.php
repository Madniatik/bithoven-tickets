<?php

namespace Bithoven\Tickets\Database\Seeders;

use Bithoven\Tickets\Models\TicketAutomationRule;
use Illuminate\Database\Seeder;

class AutomationRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Auto-Close Resolved Tickets After 7 Days
        TicketAutomationRule::create([
            'name' => 'Auto-Close Resolved Tickets (7 days)',
            'type' => TicketAutomationRule::TYPE_AUTO_CLOSE,
            'is_active' => true,
            'description' => 'Automatically close tickets that have been resolved for 7 days without customer response',
            'conditions' => [
                'status' => 'resolved',
                'inactive_hours' => 168, // 7 days
            ],
            'actions' => [
                'close' => true,
                'add_comment' => 'This ticket has been automatically closed due to inactivity. If you need further assistance, please open a new ticket.',
            ],
            'config' => [
                'notify_user' => true,
                'add_internal_note' => true,
            ],
            'execution_order' => 10,
        ]);

        // 2. Auto-Escalate Urgent Tickets After 2 Hours
        TicketAutomationRule::create([
            'name' => 'Escalate Urgent Tickets (2 hours)',
            'type' => TicketAutomationRule::TYPE_AUTO_ESCALATE,
            'is_active' => true,
            'description' => 'Escalate priority of urgent unassigned tickets after 2 hours',
            'conditions' => [
                'priority' => 'high',
                'unassigned' => true,
                'inactive_hours' => 2,
            ],
            'actions' => [
                'escalate_priority' => 'urgent',
                'add_comment' => '[AUTOMATED] Priority escalated to URGENT due to no assignment after 2 hours.',
            ],
            'config' => [
                'notify_supervisor' => true,
            ],
            'execution_order' => 1, // High priority, run first
        ]);

        // 3. Auto-Escalate Medium to High After 24 Hours
        TicketAutomationRule::create([
            'name' => 'Escalate Medium Tickets (24 hours)',
            'type' => TicketAutomationRule::TYPE_AUTO_ESCALATE,
            'is_active' => true,
            'description' => 'Escalate medium priority tickets to high after 24 hours without resolution',
            'conditions' => [
                'priority' => 'medium',
                'inactive_hours' => 24,
                'status' => 'open',
            ],
            'actions' => [
                'escalate_priority' => 'high',
                'add_comment' => '[AUTOMATED] Priority escalated to HIGH due to pending status for 24 hours.',
            ],
            'config' => [
                'notify_assigned_agent' => true,
            ],
            'execution_order' => 5,
        ]);

        // 4. Auto-Assign by Round Robin (Example - would need custom logic)
        TicketAutomationRule::create([
            'name' => 'Auto-Assign New Tickets',
            'type' => TicketAutomationRule::TYPE_AUTO_ASSIGN,
            'is_active' => false, // Disabled by default, needs configuration
            'description' => 'Automatically assign unassigned tickets to available agents using round-robin',
            'conditions' => [
                'unassigned' => true,
                'status' => 'open',
            ],
            'actions' => [
                'assign_to' => null, // Would be calculated dynamically
            ],
            'config' => [
                'assignment_method' => 'round_robin',
                'consider_workload' => true,
                'max_tickets_per_agent' => 10,
            ],
            'execution_order' => 2,
        ]);

        // 5. Auto-Response Out of Hours
        TicketAutomationRule::create([
            'name' => 'Auto-Response Outside Business Hours',
            'type' => TicketAutomationRule::TYPE_AUTO_RESPONSE,
            'is_active' => false, // Disabled, requires time-based logic
            'description' => 'Send automatic response to tickets created outside business hours',
            'conditions' => [
                'status' => 'open',
                'unassigned' => true,
            ],
            'actions' => [
                'add_comment' => 'Thank you for contacting us. Your ticket was received outside our business hours (Mon-Fri, 9AM-6PM). We will respond during our next business day.',
                'change_status' => 'pending',
            ],
            'config' => [
                'business_hours' => [
                    'timezone' => 'UTC',
                    'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                    'start_time' => '09:00',
                    'end_time' => '18:00',
                ],
            ],
            'execution_order' => 3,
        ]);

        // 6. Close Spam/Invalid Tickets
        TicketAutomationRule::create([
            'name' => 'Close Pending Tickets (14 days)',
            'type' => TicketAutomationRule::TYPE_AUTO_CLOSE,
            'is_active' => true,
            'description' => 'Close tickets in pending status for more than 14 days',
            'conditions' => [
                'status' => 'pending',
                'inactive_hours' => 336, // 14 days
            ],
            'actions' => [
                'close' => true,
                'add_comment' => 'This ticket has been automatically closed due to prolonged inactivity while pending customer response.',
            ],
            'config' => [
                'notify_user' => true,
            ],
            'execution_order' => 15,
        ]);
    }
}

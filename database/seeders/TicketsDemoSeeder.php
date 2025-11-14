<?php

namespace Bithoven\Tickets\Database\Seeders;

use Illuminate\Database\Seeder;
use Bithoven\Tickets\Models\Ticket;
use Bithoven\Tickets\Models\TicketComment;
use Bithoven\Tickets\Models\TicketCategory;
use App\Models\User;

class TicketsDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates realistic demo tickets with comments for testing and demonstration.
     */
    public function run(): void
    {
        $this->command->info('Creating demo tickets and comments...');
        
        // Get users for ticket assignment
        $users = User::limit(5)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('⚠️  No users found. Please create users first.');
            return;
        }
        
        // Get categories
        $categories = TicketCategory::all();
        
        if ($categories->isEmpty()) {
            $this->command->warn('⚠️  No categories found. Running CategorySeeder first...');
            $this->call(CategorySeeder::class);
            $categories = TicketCategory::all();
        }
        
        $tecnico = $categories->where('slug', 'tecnico')->first();
        $facturacion = $categories->where('slug', 'facturacion')->first();
        $general = $categories->where('slug', 'general')->first();
        $cuenta = $categories->where('slug', 'cuenta')->first();
        $funcionalidad = $categories->where('slug', 'funcionalidad')->first();
        $seguridad = $categories->where('slug', 'seguridad')->first();
        $documentacion = $categories->where('slug', 'documentacion')->first();
        $integracion = $categories->where('slug', 'integracion')->first();
        
        // Demo tickets data
        $demoTickets = [
            [
                'subject' => 'Login Error - 500 Internal Server Error',
                'description' => "Users are experiencing 500 errors when trying to login to their accounts. This started happening after the latest deployment yesterday.\n\nSteps to reproduce:\n1. Go to login page\n2. Enter valid credentials\n3. Click Login button\n\nExpected: User should be logged in\nActual: 500 error page is shown\n\nAffected users: ~50% of login attempts\nBrowser: All browsers\nPlatform: Web application",
                'category_id' => $tecnico?->id,
                'priority' => 'urgent',
                'status' => 'open',
                'user_id' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'created_at' => now()->subDays(2),
            ],
            [
                'subject' => 'Request for Refund - Order #12345',
                'description' => "I would like to request a full refund for my order #12345 placed on " . now()->subDays(15)->format('M d, Y') . ".\n\nReason: Product not as described\nAmount: $149.99\nPayment method: Credit Card ending in 4532\n\nI have already returned the product and have the tracking number: 1Z999AA10123456784\n\nPlease process this refund at your earliest convenience.",
                'category_id' => $facturacion?->id,
                'priority' => 'normal',
                'status' => 'pending',
                'user_id' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'created_at' => now()->subDays(5),
            ],
            [
                'subject' => 'Question about product features',
                'description' => "Hi,\n\nI'm considering upgrading to the Pro plan and I have a few questions:\n\n1. Does the Pro plan include API access?\n2. What's the rate limit for API calls?\n3. Can I downgrade back to Free plan if needed?\n4. Is there a trial period for Pro features?\n\nThank you!",
                'category_id' => $general?->id,
                'priority' => 'low',
                'status' => 'new',
                'user_id' => $users->random()->id,
                'assigned_to' => null,
                'created_at' => now()->subHours(6),
            ],
            [
                'subject' => 'Cannot change email address',
                'description' => "I'm trying to change my account email address but the verification email never arrives.\n\nSteps:\n1. Go to Settings > Account\n2. Enter new email: newemail@example.com\n3. Click 'Send Verification Email'\n4. Wait...\n\nThe email never arrives. I've checked spam folder, tried different email addresses, and waited over 24 hours.\n\nCurrent email: oldemail@example.com\nDesired email: newemail@example.com",
                'category_id' => $cuenta?->id,
                'priority' => 'normal',
                'status' => 'open',
                'user_id' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'created_at' => now()->subDays(1),
            ],
            [
                'subject' => 'Feature Request: Dark Mode',
                'description' => "It would be great to have a dark mode option for the dashboard.\n\nMany users work late at night and bright screens can be tiring. A dark theme would:\n\n- Reduce eye strain\n- Save battery on mobile devices\n- Look more modern\n- Match the trend of most applications\n\nSuggestion: Add a toggle in user preferences to switch between light/dark themes.\n\nThanks for considering!",
                'category_id' => $funcionalidad?->id,
                'priority' => 'low',
                'status' => 'new',
                'user_id' => $users->random()->id,
                'assigned_to' => null,
                'created_at' => now()->subDays(3),
            ],
            [
                'subject' => 'Security Concern: Exposed API Keys in Response',
                'description' => "⚠️ SECURITY ISSUE\n\nI noticed that the /api/v1/user endpoint is returning sensitive information including:\n\n- Full API keys (not masked)\n- Internal user IDs\n- Database table names in error messages\n\nThis is a serious security concern as API keys should never be exposed in responses.\n\nEndpoint: GET /api/v1/user\nSeverity: HIGH\nCVSS Score: 7.5\n\nPlease fix ASAP and rotate all exposed keys.",
                'category_id' => $seguridad?->id,
                'priority' => 'critical',
                'status' => 'open',
                'user_id' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'created_at' => now()->subHours(12),
            ],
            [
                'subject' => 'Documentation Missing for Webhooks',
                'description' => "The API documentation doesn't have any information about webhooks.\n\nI need to know:\n- What events trigger webhooks?\n- What is the payload structure?\n- How to verify webhook signatures?\n- How to retry failed webhooks?\n- What are the timeout settings?\n\nCould you please add a webhooks section to the docs?\n\nThanks!",
                'category_id' => $documentacion?->id,
                'priority' => 'low',
                'status' => 'resolved',
                'user_id' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'created_at' => now()->subDays(10),
                'resolved_at' => now()->subDays(2),
            ],
            [
                'subject' => 'Webhook Integration Not Working',
                'description' => "Our webhook endpoint is not receiving any events from your system.\n\nSetup:\n- Endpoint URL: https://api.example.com/webhooks/tickets\n- Events subscribed: ticket.created, ticket.updated\n- Secret key configured: whs_***************\n\nTested with:\n- Created test tickets ✗ No webhook received\n- Updated existing tickets ✗ No webhook received\n- Webhook endpoint is accessible and returns 200 OK\n\nCan you check if webhooks are being sent correctly?",
                'category_id' => $integracion?->id,
                'priority' => 'high',
                'status' => 'open',
                'user_id' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'created_at' => now()->subDays(1),
            ],
            [
                'subject' => 'Password Reset Email Not Received',
                'description' => "I clicked 'Forgot Password' but never received the reset email.\n\nEmail address: user@example.com\nTime attempted: " . now()->subHours(3)->format('Y-m-d H:i') . "\nChecked spam folder: Yes\nUsed correct email: Yes (verified with registration)\n\nI need to access my account urgently. Can you manually reset my password or resend the email?",
                'category_id' => $tecnico?->id,
                'priority' => 'urgent',
                'status' => 'open',
                'user_id' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'created_at' => now()->subHours(4),
            ],
            [
                'subject' => 'Cancel Subscription',
                'description' => "I would like to cancel my Pro subscription.\n\nSubscription ID: sub_1234567890\nBilling cycle: Monthly\nNext billing date: " . now()->addDays(5)->format('M d, Y') . "\n\nReason for cancellation: Switching to competitor\n\nPlease confirm:\n1. Immediate cancellation or at end of billing period?\n2. Will I still have access until billing date?\n3. Is there a cancellation fee?\n\nThank you.",
                'category_id' => $facturacion?->id,
                'priority' => 'normal',
                'status' => 'pending',
                'user_id' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'created_at' => now()->subDays(2),
            ],
            [
                'subject' => 'How to export my data?',
                'description' => "I need to export all my data from the platform. Is there a way to do this?\n\nI'm looking to export:\n- All my tickets\n- My profile information\n- Any uploaded files\n- Activity logs\n\nPreferably in JSON or CSV format.\n\nIs this possible? If so, how?\n\nThanks!",
                'category_id' => $general?->id,
                'priority' => 'low',
                'status' => 'closed',
                'user_id' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'created_at' => now()->subDays(15),
                'resolved_at' => now()->subDays(12),
                'closed_at' => now()->subDays(10),
            ],
            [
                'subject' => 'Account Locked After Failed Login Attempts',
                'description' => "My account has been locked after several failed login attempts.\n\nUsername: john.doe\nEmail: john@example.com\nLocked since: " . now()->subHours(2)->format('Y-m-d H:i') . "\n\nI was trying to login with my old password (forgot I changed it last week). After 5 failed attempts, the account got locked.\n\nCan you please unlock my account? I now remember the correct password.\n\nThank you for your help!",
                'category_id' => $cuenta?->id,
                'priority' => 'high',
                'status' => 'resolved',
                'user_id' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'created_at' => now()->subHours(3),
                'resolved_at' => now()->subHours(1),
            ],
            [
                'subject' => 'UI Bug: Button Not Clickable on Mobile',
                'description' => "The 'Save Changes' button on the profile settings page is not clickable on mobile devices.\n\nDevice: iPhone 13 Pro\niOS version: 17.1\nBrowser: Safari\nScreen size: 390 x 844\n\nSteps:\n1. Open settings on mobile\n2. Change profile picture\n3. Try to click 'Save Changes' button\n4. Nothing happens\n\nWorkaround: Desktop version works fine\n\nThis seems to be a CSS/touch event issue.",
                'category_id' => $funcionalidad?->id,
                'priority' => 'normal',
                'status' => 'on_hold',
                'user_id' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'created_at' => now()->subDays(4),
            ],
            [
                'subject' => '2FA Code Not Being Sent via SMS',
                'description' => "I have 2FA enabled via SMS but I'm not receiving the verification codes.\n\nPhone number: +1 (555) 123-4567\nCarrier: Verizon\nCountry: United States\n\nI've tried:\n- Logging out and back in ✗\n- Requesting code multiple times ✗\n- Checking with carrier (no blocks) ✗\n- Waiting 10+ minutes ✗\n\nI can't access my account without the 2FA code. Please disable 2FA temporarily so I can login.\n\nUrgent!",
                'category_id' => $seguridad?->id,
                'priority' => 'urgent',
                'status' => 'open',
                'user_id' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'created_at' => now()->subHours(8),
            ],
            [
                'subject' => 'API Documentation Outdated',
                'description' => "The API documentation shows endpoints that no longer exist and is missing new endpoints.\n\nDeprecated endpoints still in docs:\n- POST /api/v1/users/create (use POST /api/v2/users instead)\n- GET /api/v1/stats (removed completely)\n\nMissing from docs:\n- POST /api/v2/tickets/bulk\n- GET /api/v2/analytics/dashboard\n- PATCH /api/v2/settings/preferences\n\nCan you please update the documentation to reflect the current API?\n\nVersion discrepancy:\nDocs version: v1.2.0\nActual API version: v2.1.0",
                'category_id' => $documentacion?->id,
                'priority' => 'normal',
                'status' => 'new',
                'user_id' => $users->random()->id,
                'assigned_to' => null,
                'created_at' => now()->subDays(6),
            ],
        ];
        
        $createdTickets = 0;
        $createdComments = 0;
        
        foreach ($demoTickets as $ticketData) {
            $ticket = Ticket::create($ticketData);
            $createdTickets++;
            
            // Add comments to some tickets based on status
            $commentCount = $this->addCommentsToTicket($ticket, $users);
            $createdComments += $commentCount;
        }
        
        $this->command->info("✓ Created {$createdTickets} demo tickets");
        $this->command->info("✓ Created {$createdComments} demo comments");
    }
    
    /**
     * Add realistic comments to a ticket based on its status and priority
     * 
     * @param Ticket $ticket
     * @param \Illuminate\Support\Collection $users
     * @return int Number of comments created
     */
    private function addCommentsToTicket(Ticket $ticket, $users): int
    {
        $comments = [];
        
        // Don't add comments to new tickets
        if ($ticket->status === 'new') {
            return 0;
        }
        
        // Determine number of comments based on status
        $commentCount = 1;
        if (in_array($ticket->status, ['closed', 'resolved'])) {
            $commentCount = rand(3, 6);
        } elseif (in_array($ticket->status, ['open', 'on_hold'])) {
            $commentCount = rand(2, 4);
        } elseif ($ticket->status === 'pending') {
            $commentCount = rand(1, 3);
        } else {
            $commentCount = rand(1, 2);
        }
        
        // Templates for different types of comments
        $openingComments = [
            "Thank you for contacting support. I'm looking into this issue now.",
            "I've received your ticket and will investigate this right away.",
            "Thanks for reporting this. Let me check on this for you.",
            "I'm on this case. Give me a few minutes to look into it.",
        ];
        
        $updateComments = [
            "I've found the issue. Working on a fix now.",
            "Update: I've escalated this to our engineering team.",
            "Still investigating. I'll have an update for you within the hour.",
            "I've identified the root cause and am implementing a solution.",
            "Our team is actively working on this. ETA: 2 hours.",
        ];
        
        $resolutionComments = [
            "This issue has been resolved. Please verify on your end.",
            "Fix deployed. Can you confirm it's working now?",
            "The problem should be fixed now. Let me know if you still see issues.",
            "Resolved. Please test and confirm.",
            "This has been taken care of. Everything should be working correctly now.",
        ];
        
        $closingComments = [
            "Closing this ticket as resolved. Feel free to reopen if needed.",
            "Marking this as closed. Thank you for your patience!",
            "Issue resolved and ticket closed. Have a great day!",
            "All set! Closing this ticket now.",
        ];
        
        $userComments = [
            "Thanks for the quick response!",
            "Yes, I can confirm it's working now. Thank you!",
            "Perfect, everything is working as expected.",
            "Appreciate the help!",
            "Still having the same issue unfortunately.",
            "Can you provide more details on the fix?",
        ];
        
        for ($i = 0; $i < $commentCount; $i++) {
            $isStaff = rand(0, 100) > 40; // 60% staff responses
            $hoursAgo = ($commentCount - $i) * rand(1, 8);
            
            // Select appropriate comment based on sequence
            if ($i === 0) {
                $body = $openingComments[array_rand($openingComments)];
                $isStaff = true;
            } elseif ($i === $commentCount - 1 && in_array($ticket->status, ['closed', 'resolved'])) {
                if ($ticket->status === 'closed') {
                    $body = $closingComments[array_rand($closingComments)];
                } else {
                    $body = $resolutionComments[array_rand($resolutionComments)];
                }
                $isStaff = true;
            } elseif ($isStaff) {
                $body = $updateComments[array_rand($updateComments)];
            } else {
                $body = $userComments[array_rand($userComments)];
            }
            
            $comments[] = [
                'ticket_id' => $ticket->id,
                'user_id' => $isStaff ? $users->random()->id : $ticket->user_id,
                'comment' => $body,
                'is_internal' => $isStaff && rand(0, 100) > 80, // 20% internal notes
                'created_at' => now()->subHours($hoursAgo),
                'updated_at' => now()->subHours($hoursAgo),
            ];
        }
        
        foreach ($comments as $commentData) {
            TicketComment::create($commentData);
        }
        
        return count($comments);
    }
}

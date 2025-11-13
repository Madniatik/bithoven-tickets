# Bithoven Tickets Extension

Complete support ticket system for the Bithoven framework.

## Features

- ✅ **Ticket Management:** Create, update, assign, and resolve tickets
- ✅ **Categories:** Organize tickets by category
- ✅ **Comments:** Thread-based discussions on tickets
- ✅ **Attachments:** Upload files to tickets
- ✅ **Assignments:** Assign tickets to team members
- ✅ **Status Tracking:** Open, In Progress, Pending, Resolved, Closed
- ✅ **Priority Levels:** Low, Medium, High, Urgent
- ✅ **Permissions:** Role-based access control
- ✅ **Email Notifications:** Async notifications with user preferences (FASE 4)
- ✅ **User Preferences:** Customizable notification settings per user
- ✅ **Queue Support:** Background email sending via Laravel queues
- ✅ **API Support:** RESTful API for integrations

## Installation

### Via Composer

```bash
composer require bithoven/tickets
```

### Activate Extension

```bash
# Run migrations and activate
php artisan bithoven:extension:install tickets --seed

# Or manually:
php artisan migrate
php artisan bithoven:extension:enable tickets
```

## Configuration

Publish configuration file:

```bash
php artisan vendor:publish --tag=bithoven-tickets-config
```

Edit `config/tickets.php`:

```php
return [
    'database' => [
        'connection' => env('TICKETS_DB_CONNECTION', 'mysql'),
    ],
    'auto_assign' => env('TICKETS_AUTO_ASSIGN', false),
    'default_priority' => env('TICKETS_DEFAULT_PRIORITY', 'medium'),
    'close_after_days' => env('TICKETS_CLOSE_AFTER_DAYS', 30),
];
```

## Email Notifications

### Setup

1. **Configure email in `.env`:**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

2. **Configure queue (recommended for async sending):**

```env
QUEUE_CONNECTION=database
TICKETS_QUEUE_ENABLED=true
```

3. **Run queue worker:**

```bash
php artisan queue:work
```

### Notification Types

The system sends emails for the following events:

- **Ticket Created**: Notifies admins and assigned agent
- **Ticket Assigned**: Notifies the newly assigned agent
- **Comment Added**: Notifies ticket creator and assigned agent (public comments only)
- **Status Changed**: Notifies ticket creator and assigned agent
- **Priority Escalated**: Notifies admins, assigned agent, and ticket creator

### User Preferences

Users can manage their notification preferences at:

```
http://your-domain/settings/notifications
```

Each user can enable/disable notifications for:
- Ticket created
- Ticket assigned to me
- Comment added
- Status changed
- Priority escalated

### Programmatic Access

```php
use Bithoven\Tickets\Models\NotificationPreference;

// Get user preferences
$preferences = NotificationPreference::forUser(auth()->id());

// Check if user wants a specific notification
if ($preferences->wantsNotification('ticket_created')) {
    // Send notification
}

// Update preferences
$preferences->update([
    'ticket_created' => true,
    'comment_added' => false,
]);
```

### Email Templates

Email templates are published to `resources/views/extensions/tickets/emails/`:

- `ticket-created.blade.php`
- `ticket-assigned.blade.php`
- `comment-added.blade.php`
- `status-changed.blade.php`
- `priority-escalated.blade.php`

You can customize these templates to match your brand.

## Usage

### Web Interface

Navigate to: `http://your-domain/tickets`

### API Endpoints

```bash
GET    /api/tickets              # List all tickets
POST   /api/tickets              # Create ticket
GET    /api/tickets/{id}         # Show ticket
PUT    /api/tickets/{id}         # Update ticket
DELETE /api/tickets/{id}         # Delete ticket
POST   /api/tickets/{id}/assign  # Assign ticket
POST   /api/tickets/{id}/comment # Add comment
```

### Programmatic Usage

```php
use Bithoven\Tickets\Services\TicketService;
use Bithoven\Tickets\Models\Ticket;

// Create ticket
$ticketService = app(TicketService::class);
$ticket = $ticketService->createTicket([
    'subject' => 'Need help with billing',
    'description' => 'Cannot access invoice',
    'priority' => 'high',
    'category_id' => 1,
]);

// Assign ticket
$ticket->assignedUser()->associate($user);
$ticket->save();

// Add comment
$ticket->comments()->create([
    'user_id' => auth()->id(),
    'comment' => 'Working on this issue',
]);
```

## Database Schema

The extension creates the following tables:

- `tickets` - Main ticket records
- `ticket_categories` - Ticket categories
- `ticket_comments` - Comments on tickets
- `ticket_attachments` - File attachments

### Separate Database (Optional)

To use a separate database for tickets:

**1. Add to `config/database.php`:**

```php
'connections' => [
    'tickets' => [
        'driver' => 'mysql',
        'host' => env('TICKETS_DB_HOST', '127.0.0.1'),
        'database' => env('TICKETS_DB_DATABASE', 'tickets_db'),
        'username' => env('TICKETS_DB_USERNAME', 'root'),
        'password' => env('TICKETS_DB_PASSWORD', ''),
    ],
],
```

**2. Update `.env`:**

```env
TICKETS_DB_CONNECTION=tickets
TICKETS_DB_HOST=127.0.0.1
TICKETS_DB_DATABASE=tickets_db
TICKETS_DB_USERNAME=root
TICKETS_DB_PASSWORD=secret
```

## Permissions

The extension registers these permissions:

- `view-tickets` - View tickets list
- `create-tickets` - Create new tickets
- `edit-tickets` - Edit existing tickets
- `delete-tickets` - Delete tickets
- `assign-tickets` - Assign tickets to users
- `manage-ticket-categories` - Manage categories

Assign permissions to roles:

```php
$role = Role::findByName('support-agent');
$role->givePermissionTo('view-tickets', 'create-tickets', 'edit-tickets');
```

## Events

The extension fires these events:

- `TicketCreated` - When a ticket is created
- `TicketAssigned` - When a ticket is assigned
- `TicketResolved` - When a ticket is resolved
- `TicketCommentAdded` - When a comment is added

Listen to events:

```php
Event::listen(TicketCreated::class, function ($event) {
    // Send notification
    Mail::to($event->ticket->user)->send(new TicketCreatedMail($event->ticket));
});
```

## CLI Commands

```bash
# Close stale tickets
php artisan tickets:close-stale

# Generate ticket report
php artisan tickets:report --from=2025-01-01 --to=2025-12-31
```

## Testing

```bash
composer test
```

## Development

### Local Development

**1. Clone repository:**

```bash
cd ~/CODE/LARAVEL/BITHOVEN/
git clone https://github.com/your-username/bithoven-tickets.git
```

**2. Link to project:**

In your main project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../bithoven-tickets"
        }
    ],
    "require": {
        "bithoven/tickets": "@dev"
    }
}
```

**3. Install:**

```bash
cd LARAVEL
composer require bithoven/tickets:@dev
```

Changes in `../bithoven-tickets` will reflect immediately.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.

## License

MIT License. See [LICENSE](LICENSE) for details.

## Support

- **Documentation:** https://docs.bithoven.com/extensions/tickets
- **Issues:** https://github.com/bithoven/tickets/issues
- **Discord:** https://discord.gg/bithoven
See UNINSTALL.md for uninstallation options

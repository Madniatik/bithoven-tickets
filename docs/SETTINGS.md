# Tickets Extension - Settings Guide

Complete guide to configure the Bithoven Tickets extension through the web interface.

**Access URL:** `http://your-domain/admin/extensions/tickets/settings`

---

## 📋 Table of Contents

1. [Database Settings](#database-settings)
2. [Ticket Settings](#ticket-settings)
3. [Ticket Number Format](#ticket-number-format)
4. [Notifications](#notifications)
5. [File Uploads](#file-uploads)
6. [Pagination](#pagination)
7. [Status Options](#status-options)
8. [Priority Options](#priority-options)
9. [SLA Settings](#sla-settings)
10. [Environment Variables](#environment-variables)

---

## Database Settings

### Connection
**Field:** `database.connection`  
**Type:** String  
**Default:** `mysql`  
**Environment Variable:** `TICKETS_DB_CONNECTION`

Specifies which database connection to use for the tickets extension.

**Options:**
- `mysql` - Use default Laravel database (recommended for most cases)
- `tickets` - Use separate database connection (requires additional configuration)

**Use Cases:**
- **Default (`mysql`)**: Keep tickets data in the same database as your application
- **Separate database**: Isolate tickets data for better organization, security, or scaling

**How to Configure Separate Database:**

1. Add connection in `config/database.php`:
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

2. Update `.env`:
```env
TICKETS_DB_CONNECTION=tickets
TICKETS_DB_HOST=127.0.0.1
TICKETS_DB_DATABASE=tickets_db
TICKETS_DB_USERNAME=root
TICKETS_DB_PASSWORD=secret
```

3. Run migrations on the tickets database:
```bash
php artisan migrate --database=tickets
```

---

## Ticket Settings

### Auto Assign
**Field:** `auto_assign`  
**Type:** Boolean (Toggle)  
**Default:** `false` (Disabled)  
**Environment Variable:** `TICKETS_AUTO_ASSIGN`

Automatically assign new tickets to available agents based on workload distribution.

**When Enabled:**
- System automatically assigns tickets when created
- Assignment algorithm distributes based on current agent workload
- Reduces manual assignment overhead

**When Disabled:**
- Tickets remain unassigned until manually assigned
- Gives managers full control over ticket distribution
- Useful for specialized teams or manual triage workflows

**Best Practice:** Enable for high-volume support teams, disable for specialized support requiring manual assignment.

---

### Default Priority
**Field:** `default_priority`  
**Type:** String (Selection)  
**Default:** `medium`  
**Environment Variable:** `TICKETS_DEFAULT_PRIORITY`

Sets the default priority level for new tickets if not specified by the user.

**Options:**
- `low` - Non-urgent issues (7+ days response time)
- `medium` - Standard issues (1-3 days response time)
- `high` - Important issues (4-24 hours response time)
- `urgent` - Critical issues (1-8 hours response time)

**Use Cases:**
- Set to `low` for feature requests or general inquiries
- Set to `medium` for standard support tickets
- Set to `high` for paying customer support portals
- Set to `urgent` only for emergency/critical systems

**Recommendation:** Keep as `medium` for balanced workflow management.

---

### Close After Days
**Field:** `close_after_days`  
**Type:** Number  
**Default:** `30`  
**Environment Variable:** `TICKETS_CLOSE_AFTER_DAYS`

Number of days after which resolved tickets are automatically closed.

**How It Works:**
1. Ticket is marked as "Resolved"
2. System waits X days (configured value)
3. If no activity occurs, ticket status changes to "Closed"
4. Prevents ticket list cluttering with old resolved issues

**Recommended Values:**
- `7` days - Fast-moving support teams
- `14` days - Standard support workflows
- `30` days - Conservative approach (default)
- `0` - Disable auto-closing (manual close only)

**Note:** Set to `0` to disable automatic closing. Tickets will remain in "Resolved" status until manually closed.

---

## Ticket Number Format

### Format Pattern
**Field:** `ticket_number_format`  
**Type:** String  
**Default:** `TKT-{sequence}`  
**Environment Variable:** `TICKETS_NUMBER_FORMAT`

Defines how ticket numbers are generated and displayed.

**Available Placeholders:**
- `{year}` - Current year (4 digits, e.g., `2025`)
- `{month}` - Current month (2 digits, e.g., `11`)
- `{day}` - Current day (2 digits, e.g., `13`)
- `{sequence}` - Auto-incrementing number with padding

**Examples:**

| Format | Generated Number | Use Case |
|--------|------------------|----------|
| `TKT-{sequence}` | `TKT-000001` | Simple sequential numbering |
| `{year}-{sequence}` | `2025-000001` | Year-based tracking |
| `SUP-{year}{month}-{sequence}` | `SUP-202511-0001` | Year-month organization |
| `TICKET-{day}{month}{year}-{sequence}` | `TICKET-13112025-001` | Full date tracking |
| `{sequence}` | `000001` | Numbers only |

**Best Practices:**
- Keep format short and readable (max 20 characters)
- Use consistent prefix for easy filtering
- Include date components for long-term tracking
- Avoid special characters that cause URL encoding issues

---

### Number Padding
**Field:** `ticket_number_padding`  
**Type:** Number  
**Default:** `6`  
**Environment Variable:** `TICKETS_NUMBER_PADDING`

Number of digits for the `{sequence}` placeholder (left-padded with zeros).

**Examples:**

| Padding | Sequence Value | Formatted Output |
|---------|----------------|------------------|
| `3` | 1 | `001` |
| `4` | 42 | `0042` |
| `6` | 1 | `000001` |
| `6` | 999 | `000999` |
| `8` | 1 | `00000001` |

**Recommended Values:**
- `4` - Small organizations (up to 9,999 tickets/year)
- `6` - Medium organizations (up to 999,999 tickets/year) - Default
- `8` - Large organizations or long-term tracking

**Note:** Padding only affects display, not the actual database sequence.

---

## Notifications

### Enabled
**Field:** `notifications.enabled`  
**Type:** Boolean (Toggle)  
**Default:** `true` (Enabled)  
**Environment Variable:** `TICKETS_NOTIFICATIONS_ENABLED`

Master switch to enable/disable all notification functionality.

**When Enabled:**
- System sends notifications based on channel and event settings
- Users can manage their own notification preferences

**When Disabled:**
- No notifications are sent regardless of other settings
- Useful for testing or maintenance

**Use Cases:**
- Disable temporarily during system maintenance
- Disable for development/testing environments
- Keep enabled for production

---

### Notification Channels

#### Mail Notifications
**Field:** `notifications.channels.mail`  
**Type:** Boolean (Toggle)  
**Default:** `true` (Enabled)  
**Environment Variable:** `TICKETS_EMAIL_NOTIFICATIONS`

Send email notifications for ticket events.

**Requirements:**
- Valid SMTP configuration in `.env`
- Queue worker running (recommended for async sending)

**Email Types Sent:**
1. **Ticket Created** - To assigned agent and admins
2. **Ticket Assigned** - To newly assigned agent
3. **Comment Added** - To ticket creator and assigned agent
4. **Status Changed** - To ticket creator and assigned agent
5. **Priority Escalated** - To admins, assigned agent, and creator

**Configure SMTP:**
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

---

#### Database Notifications
**Field:** `notifications.channels.database`  
**Type:** Boolean (Toggle)  
**Default:** `true` (Enabled)  
**Environment Variable:** `TICKETS_DATABASE_NOTIFICATIONS`

Store notifications in the database for in-app display.

**Features:**
- Notifications appear in user's notification bell/dropdown
- Persistent record of all notification history
- Can be marked as read/unread
- Queryable for audit trails

**Storage:** `notifications` table (Laravel standard)

---

#### Slack Notifications
**Field:** `notifications.channels.slack`  
**Type:** Boolean (Toggle)  
**Default:** `false` (Disabled)  
**Environment Variable:** `TICKETS_SLACK_NOTIFICATIONS`

Send notifications to Slack workspace channels.

**⚠️ Note:** Currently not implemented in v1.1.0. Planned for v1.2.0.

**Planned Configuration:**
```env
TICKETS_SLACK_WEBHOOK_URL=https://hooks.slack.com/services/YOUR/WEBHOOK/URL
TICKETS_SLACK_CHANNEL=#support-tickets
```

---

### Notification Events

Configure which ticket events trigger notifications:

| Event | Default | Description |
|-------|---------|-------------|
| `created` | `true` | New ticket created |
| `assigned` | `true` | Ticket assigned to agent |
| `updated` | `true` | Ticket details updated |
| `commented` | `true` | Comment added to ticket |
| `resolved` | `true` | Ticket marked as resolved |
| `closed` | `true` | Ticket closed |

**Recommendation:** Keep all events enabled. Users can manage their own preferences at `/settings/notifications`.

---

### Queue Configuration

#### Queue Enabled
**Field:** `notifications.queue.enabled`  
**Type:** Boolean (Toggle)  
**Default:** `true` (Enabled)  
**Environment Variable:** `TICKETS_QUEUE_ENABLED`

Send email notifications asynchronously using Laravel queues.

**Benefits:**
- Faster response times (no waiting for email sending)
- Handles SMTP failures gracefully
- Can retry failed notifications
- Reduces server load during peak times

**Requirements:**
1. Configure queue connection in `.env`:
```env
QUEUE_CONNECTION=database
```

2. Run queue worker:
```bash
php artisan queue:work
```

3. (Production) Use supervisor for persistent queue workers:
```bash
php artisan queue:work --daemon
```

**When Disabled:**
- Emails sent synchronously (page waits for email to send)
- Suitable for low-volume or testing environments
- Not recommended for production

---

#### Queue Connection
**Field:** `notifications.queue.connection`  
**Type:** String  
**Default:** `database`  
**Environment Variable:** `TICKETS_QUEUE_CONNECTION`

Which queue connection to use for notifications.

**Options:**
- `sync` - No queue (synchronous sending) - Not recommended
- `database` - Store jobs in database - Recommended for most cases
- `redis` - Use Redis for high performance
- `beanstalkd` - Use Beanstalk queue server
- `sqs` - Amazon SQS for cloud deployments

**Recommendation:** Use `database` for simplicity, `redis` for high-volume production.

---

#### Queue Name
**Field:** `notifications.queue.queue`  
**Type:** String  
**Default:** `default`  
**Environment Variable:** `TICKETS_QUEUE_NAME`

Name of the queue for ticket notifications.

**Use Cases:**
- `default` - Use default application queue
- `emails` - Separate queue for all emails
- `notifications` - Dedicated queue for notifications
- `tickets-notifications` - Specific queue for ticket emails

**Advanced Setup (Multiple Workers):**
```bash
# High priority queue for tickets
php artisan queue:work --queue=tickets-notifications,default

# Separate worker for notifications
php artisan queue:work --queue=notifications --tries=3
```

---

## File Uploads

### Uploads Enabled
**Field:** `uploads.enabled`  
**Type:** Boolean (Toggle)  
**Default:** `true` (Enabled)  
**Environment Variable:** `TICKETS_UPLOADS_ENABLED`

Allow file attachments on tickets and comments.

**When Enabled:**
- Users can attach files to tickets and comments
- Files stored according to disk and path settings
- Attachments visible in ticket detail view

**When Disabled:**
- Upload button hidden from UI
- Existing attachments remain accessible
- Useful for security-sensitive environments

---

### Storage Disk
**Field:** `uploads.disk`  
**Type:** String  
**Default:** `public`  
**Environment Variable:** `TICKETS_UPLOADS_DISK`

Laravel filesystem disk for storing attachments.

**Options:**
- `public` - Publicly accessible (`storage/app/public`)
- `local` - Private storage (`storage/app`)
- `s3` - Amazon S3 cloud storage
- `spaces` - DigitalOcean Spaces

**Configuration Examples:**

**Public Disk (Default):**
- Files accessible via URL: `/storage/tickets/attachments/filename.pdf`
- Requires: `php artisan storage:link`
- Good for: Screenshots, images, public documents

**Local Disk (Private):**
- Files NOT directly accessible via URL
- Requires controller-based download route
- Good for: Sensitive documents, private data

**S3 Disk:**
```php
// config/filesystems.php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
],
```

---

### Storage Path
**Field:** `uploads.path`  
**Type:** String  
**Default:** `tickets/attachments`  
**Environment Variable:** `TICKETS_UPLOADS_PATH`

Directory path within the storage disk for ticket attachments.

**Examples:**

| Path | Full Path (public disk) | Use Case |
|------|------------------------|----------|
| `tickets/attachments` | `storage/app/public/tickets/attachments` | Default organization |
| `tickets` | `storage/app/public/tickets` | Simpler structure |
| `attachments/tickets` | `storage/app/public/attachments/tickets` | Group with other attachments |
| `{year}/{month}/tickets` | `storage/app/public/2025/11/tickets` | Date-based organization |

**Best Practices:**
- Use descriptive path names
- Keep paths lowercase
- Avoid spaces (use hyphens or underscores)
- Consider future organization needs

---

### Max File Size
**Field:** `uploads.max_size`  
**Type:** Number  
**Default:** `5120` (5 MB)  
**Environment Variable:** `TICKETS_MAX_UPLOAD_SIZE`

Maximum file size for uploads in **kilobytes (KB)**.

**Common Values:**

| Value (KB) | Size | Use Case |
|------------|------|----------|
| `1024` | 1 MB | Text documents only |
| `2048` | 2 MB | Small images and PDFs |
| `5120` | 5 MB | Standard documents and images (Default) |
| `10240` | 10 MB | High-res images and presentations |
| `20480` | 20 MB | Videos and large files |
| `51200` | 50 MB | Development/debugging environments |

**Important Considerations:**

1. **PHP Configuration** - Must also configure in `php.ini`:
```ini
upload_max_filesize = 5M
post_max_size = 6M
```

2. **Nginx Configuration** - Add to nginx config:
```nginx
client_max_body_size 5M;
```

3. **Hosting Limits** - Check your hosting provider's restrictions

**Recommendation:** 
- `5120` KB (5 MB) for most use cases
- `10240` KB (10 MB) for image-heavy support
- Higher values require server configuration changes

---

### Allowed File Types
**Field:** `uploads.allowed_types`  
**Type:** Array (Comma-separated in UI)  
**Default:** Images, Documents, Text files, Archives

Whitelist of file extensions permitted for upload.

**Default Extensions:**

**Images:**
- `jpg`, `jpeg`, `png`, `gif`, `webp`

**Documents:**
- `pdf` - PDF documents
- `doc`, `docx` - Microsoft Word
- `xls`, `xlsx` - Microsoft Excel

**Text Files:**
- `txt` - Plain text
- `csv` - Comma-separated values
- `json` - JSON data
- `xml` - XML documents

**Archives:**
- `zip`, `rar`, `7z` - Compressed archives

**How to Modify:**

In the web interface, enter extensions separated by commas:
```
jpg, jpeg, png, pdf, doc, docx, txt, zip
```

**Security Best Practices:**
- ⚠️ **Never allow:** `exe`, `bat`, `sh`, `php`, `js`, `html` (executable files)
- ✅ **Recommended:** Only enable file types your team actually needs
- ✅ **Consider:** Scanning uploads with antivirus (ClamAV, VirusTotal)

**Example Configurations:**

**Images Only:**
```
jpg, jpeg, png, gif, webp
```

**Documents Only:**
```
pdf, doc, docx, xls, xlsx, txt
```

**Strict Security (No Archives):**
```
jpg, jpeg, png, pdf, txt
```

---

## Pagination

### Per Page
**Field:** `pagination.per_page`  
**Type:** Number  
**Default:** `20`  
**Environment Variable:** `TICKETS_PER_PAGE`

Number of tickets displayed per page in DataTables views.

**Note:** This setting is **informational only**. Tickets extension uses **Yajra DataTables** with server-side processing, which has its own pagination controls.

**DataTables Default:** 10 entries per page (configurable in DataTable class)

**Recommended Values:**
- `10` - Clean, focused view
- `20` - Good balance
- `50` - Power users
- `100` - Bulk operations

**To Change DataTables Pagination:**

Edit DataTable class (e.g., `src/DataTables/TicketsDataTable.php`):

```php
public function html()
{
    return $this->builder()
        ->parameters([
            'pageLength' => 20, // Default page length
            'lengthMenu' => [[10, 20, 50, 100], [10, 20, 50, 100]], // Options
        ]);
}
```

---

### Per Page Options
**Field:** `pagination.per_page_options`  
**Type:** Array (Comma-separated in UI)  
**Default:** `10, 20, 50, 100`

Available page size options in the dropdown.

**Note:** Informational only for standard pagination. DataTables uses `lengthMenu` parameter.

---

## Status Options

Configure available ticket statuses with labels, colors, and icons.

### Status Configuration Structure

Each status has:
- **label** - Display name
- **color** - Bootstrap/Metronic color class
- **icon** - FontAwesome icon class

### Available Statuses

#### Open
**Key:** `open`  
**Label:** `Open`  
**Color:** `primary` (Blue #009ef7)  
**Icon:** `fa-folder-open`

**When to Use:**
- New ticket just created
- Initial state for all tickets
- Awaiting assignment or review

---

#### In Progress
**Key:** `in_progress`  
**Label:** `In Progress`  
**Color:** `warning` (Orange #ffc700)  
**Icon:** `fa-spinner`

**When to Use:**
- Agent actively working on ticket
- Investigation or troubleshooting underway
- Waiting for internal resources

---

#### Pending
**Key:** `pending`  
**Label:** `Pending`  
**Color:** `info` (Light Blue #7239ea)  
**Icon:** `fa-clock`

**When to Use:**
- Waiting for customer response
- Awaiting third-party information
- Scheduled for future action

---

#### Resolved
**Key:** `resolved`  
**Label:** `Resolved`  
**Color:** `success` (Green #50cd89)  
**Icon:** `fa-check-circle`

**When to Use:**
- Issue solved
- Waiting for customer confirmation
- Will auto-close after X days (see `close_after_days`)

---

#### Closed
**Key:** `closed`  
**Label:** `Closed`  
**Color:** `secondary` (Gray #a1a5b7)  
**Icon:** `fa-times-circle`

**When to Use:**
- Issue confirmed resolved
- No further action needed
- Final state for completed tickets

---

### Customizing Statuses

**⚠️ Important:** Status keys (`open`, `in_progress`, etc.) are hardcoded in the application logic. You can customize labels, colors, and icons, but **do not change the keys**.

**Safe to Modify:**
```php
'open' => [
    'label' => 'New Ticket',           // ✅ Safe to change
    'color' => 'danger',               // ✅ Safe to change
    'icon' => 'fa-bell',               // ✅ Safe to change
],
```

**Do NOT Modify:**
```php
'custom_status' => [  // ❌ Will not work (unknown key)
    'label' => 'Custom',
    // ...
],
```

**Available Color Classes:**
- `primary` - Blue
- `secondary` - Gray
- `success` - Green
- `danger` - Red
- `warning` - Orange
- `info` - Light Blue
- `light` - Light Gray
- `dark` - Dark Gray

---

## Priority Options

Configure available ticket priorities with labels, colors, and icons.

### Priority Configuration Structure

Each priority has:
- **label** - Display name
- **color** - Bootstrap/Metronic color class
- **icon** - FontAwesome icon class

### Available Priorities

#### Low
**Key:** `low`  
**Label:** `Low`  
**Color:** `secondary` (Gray #a1a5b7)  
**Icon:** `fa-arrow-down`

**When to Use:**
- Feature requests
- General inquiries
- Non-urgent issues
- Documentation questions

**SLA:** 48 hours response / 7 days resolution (if SLA enabled)

---

#### Medium
**Key:** `medium`  
**Label:** `Medium`  
**Color:** `primary` (Blue #009ef7)  
**Icon:** `fa-minus`

**When to Use:**
- Standard support requests
- Minor bugs
- Account issues
- Default priority

**SLA:** 24 hours response / 3 days resolution (if SLA enabled)

---

#### High
**Key:** `high`  
**Label:** `High`  
**Color:** `warning` (Orange #ffc700)  
**Icon:** `fa-arrow-up`

**When to Use:**
- System errors affecting multiple users
- Payment/billing issues
- Data integrity concerns
- Service degradation

**SLA:** 4 hours response / 1 day resolution (if SLA enabled)

---

#### Urgent
**Key:** `urgent`  
**Label:** `Urgent`  
**Color:** `danger` (Red #f1416c)  
**Icon:** `fa-exclamation-triangle`

**When to Use:**
- Complete system outage
- Security vulnerabilities
- Data loss risks
- Critical production issues

**SLA:** 1 hour response / 8 hours resolution (if SLA enabled)

---

### Priority Escalation

Priorities can be escalated automatically based on:
- Time since creation
- SLA breach risk
- Automation rules

**Email Notification:** When priority is escalated, system sends "Priority Escalated" email to admins, assigned agent, and ticket creator.

### Customizing Priorities

**⚠️ Important:** Priority keys (`low`, `medium`, `high`, `urgent`) are hardcoded. You can customize labels, colors, and icons, but **do not change the keys**.

**Safe to Modify:**
```php
'urgent' => [
    'label' => 'Critical',             // ✅ Safe to change
    'color' => 'danger',               // ✅ Safe to change
    'icon' => 'fa-fire',               // ✅ Safe to change
],
```

---

## SLA Settings

Service Level Agreement configuration for response and resolution times.

### SLA Enabled
**Field:** `sla.enabled`  
**Type:** Boolean (Toggle)  
**Default:** `false` (Disabled)  
**Environment Variable:** `TICKETS_SLA_ENABLED`

Enable SLA tracking and breach alerts.

**When Enabled:**
- System tracks first response time
- Monitors resolution deadlines
- Can trigger alerts on SLA breach (future feature)
- Displays SLA indicators in UI

**When Disabled:**
- No SLA tracking
- Timestamps still recorded but not enforced
- Suitable for informal support workflows

**Recommendation:** Enable for professional support teams with defined SLAs.

---

### Response Times

**Field:** `sla.response_times`  
**Type:** Array of Numbers (Hours)

Maximum time to first response by priority level.

| Priority | Default Time | Description |
|----------|--------------|-------------|
| `urgent` | 1 hour | Critical issues require immediate attention |
| `high` | 4 hours | Important issues need same-day response |
| `medium` | 24 hours | Standard issues within business day |
| `low` | 48 hours | Non-urgent can wait 2 business days |

**How It Works:**
1. Ticket created at 10:00 AM
2. System calculates deadline based on priority
3. First agent response (comment) stops the timer
4. If deadline passes without response, ticket flagged (future feature)

**Customization Examples:**

**24/7 Support Team:**
```php
'response_times' => [
    'urgent' => 0.5,  // 30 minutes
    'high' => 2,      // 2 hours
    'medium' => 8,    // 8 hours
    'low' => 24,      // 1 day
],
```

**Business Hours Only (9-5):**
```php
'response_times' => [
    'urgent' => 2,    // 2 hours
    'high' => 8,      // Same day
    'medium' => 24,   // Next business day
    'low' => 72,      // 3 business days
],
```

---

### Resolution Times

**Field:** `sla.resolution_times`  
**Type:** Array of Numbers (Hours)

Maximum time to resolve ticket by priority level.

| Priority | Default Time | Description |
|----------|--------------|-------------|
| `urgent` | 8 hours | Same-day resolution for critical issues |
| `high` | 24 hours | 1 business day for important issues |
| `medium` | 72 hours | 3 business days for standard issues |
| `low` | 168 hours | 7 days for non-urgent issues |

**How It Works:**
1. Ticket created at Monday 10:00 AM
2. System calculates resolution deadline
3. When ticket marked "Resolved", timer stops
4. If deadline passes, SLA breach recorded (future feature)

**Customization Examples:**

**Aggressive SLAs:**
```php
'resolution_times' => [
    'urgent' => 4,    // 4 hours
    'high' => 12,     // 12 hours
    'medium' => 48,   // 2 days
    'low' => 120,     // 5 days
],
```

**Conservative SLAs:**
```php
'resolution_times' => [
    'urgent' => 24,   // 1 day
    'high' => 72,     // 3 days
    'medium' => 168,  // 1 week
    'low' => 336,     // 2 weeks
],
```

---

## Environment Variables

Complete list of `.env` variables for overriding config settings.

### Database
```env
TICKETS_DB_CONNECTION=mysql
TICKETS_DB_HOST=127.0.0.1
TICKETS_DB_DATABASE=tickets_db
TICKETS_DB_USERNAME=root
TICKETS_DB_PASSWORD=secret
```

### Ticket Behavior
```env
TICKETS_AUTO_ASSIGN=false
TICKETS_DEFAULT_PRIORITY=medium
TICKETS_CLOSE_AFTER_DAYS=30
```

### Ticket Numbering
```env
TICKETS_NUMBER_FORMAT=TKT-{sequence}
TICKETS_NUMBER_PADDING=6
```

### Notifications
```env
TICKETS_NOTIFICATIONS_ENABLED=true
TICKETS_EMAIL_NOTIFICATIONS=true
TICKETS_DATABASE_NOTIFICATIONS=true
TICKETS_SLACK_NOTIFICATIONS=false
```

### Queue
```env
TICKETS_QUEUE_ENABLED=true
TICKETS_QUEUE_CONNECTION=database
TICKETS_QUEUE_NAME=default
```

### File Uploads
```env
TICKETS_UPLOADS_ENABLED=true
TICKETS_UPLOADS_DISK=public
TICKETS_UPLOADS_PATH=tickets/attachments
TICKETS_MAX_UPLOAD_SIZE=5120
```

### Pagination
```env
TICKETS_PER_PAGE=20
```

### SLA
```env
TICKETS_SLA_ENABLED=false
```

---

## Configuration Workflow

### Step-by-Step Setup Guide

#### 1. Initial Configuration (Required)

**Access Settings:**
1. Navigate to `http://your-domain/admin/extensions/tickets/settings`
2. Ensure extension is installed and active

**Configure Email:**
1. Set up SMTP in `.env` (see [Mail Notifications](#mail-notifications))
2. Enable email notifications in settings
3. Test email delivery: `php artisan tinker` → `Mail::to('test@example.com')->send(...)`

**Configure Storage:**
1. Run `php artisan storage:link` if using `public` disk
2. Verify upload directory is writable: `chmod 755 storage/app/public/tickets`
3. Test file upload in a ticket

---

#### 2. Queue Setup (Recommended for Production)

**Configure Queue:**
```bash
# 1. Update .env
echo "QUEUE_CONNECTION=database" >> .env
echo "TICKETS_QUEUE_ENABLED=true" >> .env

# 2. Run queue tables migration
php artisan queue:table
php artisan migrate

# 3. Start queue worker
php artisan queue:work
```

**Production Setup (Supervisor):**

Create `/etc/supervisor/conf.d/tickets-queue.conf`:
```ini
[program:tickets-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --queue=default --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/queue-worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start tickets-queue:*
```

---

#### 3. Advanced Configuration (Optional)

**Enable SLA Tracking:**
1. Set `sla.enabled` to `true` in settings
2. Customize response/resolution times by priority
3. Train team on SLA expectations

**Configure Auto-Assignment:**
1. Enable `auto_assign` in settings
2. Ensure agent roles have `assign-tickets` permission
3. Monitor distribution fairness

**Separate Database (Large Deployments):**
1. Create new database: `tickets_db`
2. Add connection to `config/database.php`
3. Update settings: `database.connection` = `tickets`
4. Run migrations: `php artisan migrate --database=tickets`

---

## Troubleshooting

### Common Issues

**❌ Settings Not Saving**
- Check file permissions: `chmod 664 config/tickets.php`
- Verify user has `manage-extensions` permission
- Clear config cache: `php artisan config:clear`

**❌ Emails Not Sending**
- Test SMTP connection: `php artisan tinker` → `Mail::raw('Test', ...)`
- Check `.env` MAIL_* variables
- Verify queue worker is running: `php artisan queue:work`
- Check failed jobs: `php artisan queue:failed`

**❌ File Uploads Failing**
- Verify storage link exists: `ls -la public/storage`
- Check directory permissions: `chmod 755 storage/app/public/tickets`
- Verify PHP upload limits: `php -i | grep upload_max_filesize`
- Check Nginx client_max_body_size

**❌ Queue Jobs Not Processing**
- Restart queue worker: `php artisan queue:restart`
- Check queue connection: `QUEUE_CONNECTION` in `.env`
- Verify database queue table exists
- Monitor queue: `php artisan queue:listen --verbose`

**❌ Configuration Not Taking Effect**
- Clear all caches:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```
- Reload web server: `sudo service nginx reload`

---

## Best Practices

### Security
- ✅ Use `local` disk for sensitive attachments
- ✅ Whitelist only necessary file types
- ✅ Set reasonable max upload size (5-10 MB)
- ✅ Never allow executable file types (exe, sh, php)
- ✅ Consider antivirus scanning for uploads
- ✅ Use environment variables for sensitive config

### Performance
- ✅ Enable queue for email notifications
- ✅ Use Redis for high-volume queues
- ✅ Monitor queue job processing time
- ✅ Use separate database for 100K+ tickets
- ✅ Optimize DataTables queries with indexes

### Workflow
- ✅ Enable auto-assign for high-volume support
- ✅ Set realistic SLA times based on team capacity
- ✅ Use status workflow: Open → In Progress → Resolved → Closed
- ✅ Configure auto-close for resolved tickets (30 days)
- ✅ Train team on priority definitions

### Maintenance
- ✅ Backup config files before changes
- ✅ Test settings in development first
- ✅ Monitor queue failed jobs regularly
- ✅ Review and clean old closed tickets
- ✅ Update extension regularly for security patches

---

## Related Documentation

- **[README.md](../README.md)** - Installation and features
- **[CHANGELOG.md](../CHANGELOG.md)** - Version history
- **[UNINSTALL.md](../UNINSTALL.md)** - Uninstallation guide
- **Main Project Docs:** `.github/copilot-instructions.md`

---

## Support

Need help configuring your settings?

- **Web Interface:** Access settings at `/admin/extensions/tickets/settings`
- **Manage Categories:** `/admin/ticket-categories`
- **User Preferences:** `/settings/notifications`
- **Extension Details:** `/admin/extensions/tickets`

**Configuration Tips:**
1. Start with default settings
2. Test in development environment
3. Monitor performance and user feedback
4. Adjust incrementally based on needs

---

**Last Updated:** 13 de noviembre de 2025  
**Version:** 1.1.0  
**Extension:** Bithoven Tickets

# Bithoven Tickets - Documentation Index

Complete documentation for the Bithoven Tickets extension.

---

## 📚 Documentation Files

### **[SETTINGS.md](SETTINGS.md)** - Configuration Guide ⚙️
Complete guide to configure the extension through the web interface.

**Covers:**
- Database settings
- Ticket behavior (auto-assign, priorities, closing)
- Ticket numbering format
- Email notifications setup
- Queue configuration
- File upload settings
- Status and priority customization
- SLA (Service Level Agreement) tracking
- Environment variables
- Troubleshooting and best practices

**Access:** `http://your-domain/admin/extensions/tickets/settings`

---

## 🚀 Quick Start

### Installation
```bash
composer require bithoven/tickets
php artisan bithoven:extension:install tickets --seed
```

### Basic Configuration
1. **Configure Email** (`.env`):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

2. **Enable Queue** (recommended):
```env
QUEUE_CONNECTION=database
TICKETS_QUEUE_ENABLED=true
```

3. **Run Queue Worker**:
```bash
php artisan queue:work
```

4. **Access Settings**:
`http://your-domain/admin/extensions/tickets/settings`

---

## 📖 Main Documentation

### Root Files

- **[README.md](../README.md)** - Main documentation
  - Features overview
  - Installation instructions
  - Usage examples
  - API documentation
  - Database schema

- **[CHANGELOG.md](../CHANGELOG.md)** - Version history
  - Release notes
  - New features
  - Bug fixes
  - Breaking changes

- **[UNINSTALL.md](../UNINSTALL.md)** - Uninstallation guide
  - Remove extension
  - Clean database
  - Restore system

- **[LICENSE](../LICENSE)** - MIT License

---

## 🔧 Configuration Reference

### Config File
`config/tickets.php` - Main configuration file

**Sections:**
- Database connection
- Ticket settings (auto-assign, priority, close_after_days)
- Ticket numbering format
- Notifications (mail, database, slack)
- Queue settings
- File uploads (disk, path, size, types)
- Pagination
- Status options
- Priority options
- SLA settings

### Environment Variables

**Essential Variables:**
```env
# Database
TICKETS_DB_CONNECTION=mysql

# Behavior
TICKETS_AUTO_ASSIGN=false
TICKETS_DEFAULT_PRIORITY=medium
TICKETS_CLOSE_AFTER_DAYS=30

# Notifications
TICKETS_NOTIFICATIONS_ENABLED=true
TICKETS_EMAIL_NOTIFICATIONS=true
TICKETS_QUEUE_ENABLED=true

# Uploads
TICKETS_UPLOADS_ENABLED=true
TICKETS_MAX_UPLOAD_SIZE=5120
```

**Full List:** See [SETTINGS.md - Environment Variables](SETTINGS.md#environment-variables)

---

## 🎯 Features Documentation

### Core Features

**✅ Ticket Management**
- Create, edit, delete tickets
- Assign to agents
- Status workflow (Open → In Progress → Resolved → Closed)
- Priority levels (Low, Medium, High, Urgent)
- Categories organization

**✅ Comments & Attachments**
- Thread-based discussions
- Internal notes (agent-only)
- File attachments (images, documents, archives)
- Mark comment as solution

**✅ Email Notifications**
- 5 notification types (created, assigned, comment, status, priority)
- User-specific preferences (`/settings/notifications`)
- Queue support for async sending
- Customizable email templates

**✅ Templates & Automation**
- Ticket templates for common issues
- Canned responses for quick replies
- Automation rules for routing
- Automation logs tracking

**✅ DataTables Integration**
- Server-side AJAX processing
- Metronic CSS styling
- Advanced search and filtering
- Responsive design

---

## 🛠️ Developer Documentation

### Extension Structure

```
bithoven-extension-tickets/
├── config/
│   └── tickets.php              # Configuration file
├── database/
│   ├── migrations/              # Database schema
│   └── seeders/                 # Default data
├── resources/
│   ├── views/                   # Blade templates
│   │   ├── tickets/            # Ticket views
│   │   ├── categories/         # Category views
│   │   ├── templates/          # Template views
│   │   ├── responses/          # Canned responses views
│   │   └── emails/             # Email templates
│   └── lang/                    # Translations
├── routes/
│   ├── web.php                  # Web routes
│   └── api.php                  # API routes
├── src/
│   ├── TicketsServiceProvider.php
│   ├── Console/                 # Artisan commands
│   ├── DataTables/              # Yajra DataTables
│   ├── Events/                  # Event classes
│   ├── Http/
│   │   ├── Controllers/        # Controllers
│   │   └── Requests/           # Form requests
│   ├── Listeners/               # Event listeners
│   ├── Mail/                    # Mail classes
│   ├── Models/                  # Eloquent models
│   ├── Policies/                # Authorization policies
│   └── Services/                # Business logic
└── docs/
    ├── README.md                # This file
    └── SETTINGS.md              # Settings guide
```

### Database Tables

1. `tickets` - Main ticket records
2. `ticket_categories` - Categories
3. `ticket_comments` - Comments
4. `ticket_attachments` - File attachments
5. `ticket_notification_preferences` - User notification settings
6. `ticket_templates` - Pre-configured templates
- `ticket_canned_responses` - Quick reply templates
8. `ticket_automation_rules` - Automation rules
9. `ticket_automation_logs` - Automation execution logs

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

### Events

- `TicketCreated` - When ticket is created
- `TicketAssigned` - When ticket is assigned
- `TicketResolved` - When ticket is resolved
- `CommentAdded` - When comment is added
- `StatusChanged` - When status changes
- `PriorityEscalated` - When priority escalates

### Permissions

- `view-tickets` - View tickets list
- `create-tickets` - Create new tickets
- `edit-tickets` - Edit existing tickets
- `delete-tickets` - Delete tickets
- `assign-tickets` - Assign tickets to users
- `manage-ticket-categories` - Manage categories

---

## 📋 User Guides

### For End Users

**Creating a Ticket:**
1. Go to `/tickets`
2. Click "New Ticket" button
3. Fill in subject, description, category
4. Set priority (optional)
5. Attach files (optional)
6. Submit

**Managing Notification Preferences:**
1. Go to `/settings/notifications`
2. Toggle notification types:
   - Ticket created
   - Ticket assigned to me
   - Comment added
   - Status changed
   - Priority escalated
3. Save preferences

### For Support Agents

**Working with Tickets:**
1. View assigned tickets in `/tickets`
2. Filter by status, priority, category
3. Open ticket to see details
4. Add comments (public or internal)
5. Attach files if needed
6. Update status as work progresses
7. Mark as resolved when complete

**Using Canned Responses:**
1. Open ticket
2. Click "Canned Responses" in comment area
3. Select pre-written response
4. Customize if needed
5. Send

**Using Templates:**
1. Go to "Ticket Templates"
2. Click "Use Template"
3. System creates ticket with pre-filled content
4. Adjust as needed
5. Submit

### For Administrators

**Managing Categories:**
1. Go to `/admin/ticket-categories`
2. Create/edit categories
3. Set colors and icons
4. Assign to tickets

**Configuring Settings:**
1. Go to `/admin/extensions/tickets/settings`
2. Configure all options (see [SETTINGS.md](SETTINGS.md))
3. Test changes in development first
4. Save and monitor

**Setting Up Automation:**
1. Go to "Automation Rules"
2. Define conditions (priority, category, keywords)
3. Set actions (assign, status change, notify)
4. Enable rule
5. Monitor logs in "Automation Logs"

---

## 🔍 Troubleshooting

See **[SETTINGS.md - Troubleshooting](SETTINGS.md#troubleshooting)** for common issues and solutions.

**Quick Fixes:**

```bash
# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Restart queue
php artisan queue:restart

# Check failed jobs
php artisan queue:failed

# Storage link
php artisan storage:link

# Permissions
chmod -R 755 storage/app/public/tickets
```

---

## 🎓 Training Resources

### Video Tutorials (Coming Soon)
- Installing the extension
- Configuring settings
- Creating tickets
- Managing automation
- Using templates

### Best Practices
See **[SETTINGS.md - Best Practices](SETTINGS.md#best-practices)**

---

## 📞 Support

- **Documentation:** This folder
- **Issues:** GitHub Issues
- **Email:** support@bithoven.com
- **Discord:** discord.gg/bithoven

---

## 📝 Contributing

Want to improve documentation?

1. Fork repository
2. Edit docs in `/docs` folder
3. Submit pull request

**Documentation Standards:**
- Use Markdown formatting
- Include code examples
- Add screenshots for UI guides
- Keep sections organized
- Update index when adding new docs

---

**Last Updated:** 13 de noviembre de 2025  
**Version:** 1.1.0  
**Extension:** Bithoven Tickets

# Changelog

All notable changes to the Bithoven Tickets extension will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2025-11-13

### Added
- ✅ **DataTables Migration:** All tables migrated to Yajra DataTables v10.x
  - Tickets, Templates, Responses, Automation Rules, Automation Logs
  - Server-side AJAX processing for performance
  - Metronic CSS styling with #009ef7 primary color
  - Advanced search, filtering, and sorting
- ✅ **Ticket Templates:** Pre-configured templates for common issues
- ✅ **Canned Responses:** Quick reply templates for agents
- ✅ **Automation Rules:** Automatic ticket routing and processing
- ✅ **Automation Logs:** Track automation execution and results
- ✅ **Email Notifications:** Complete async notification system
  - 5 notification types (created, assigned, comment, status, priority)
  - User-specific preferences via `/settings/notifications`
  - Queue support for background sending
  - Customizable email templates
- ✅ **Notification Preferences:** User settings for email notifications
- ✅ **Enhanced UI:** Sidebar menu link for notification settings
- ✅ **Active State Detection:** JavaScript support for showcase menu items

### Changed
- 🔄 **Pagination → DataTables:** Removed Laravel Pagination in favor of DataTables
- 🔄 **UI Consistency:** All tables now use uniform Metronic styling
- 🔄 **Performance:** Server-side processing for large datasets

### Technical
- DataTable classes in `src/DataTables/`
- Mail classes in `src/Mail/`
- Email views in `resources/views/emails/`
- Migration scripts in `scripts/migrate-to-datatables.sh`

## [1.0.0] - 2025-10-31

### Added
- ✅ Complete ticket management system
- ✅ Ticket categories with colors and icons
- ✅ Comments system with internal notes
- ✅ File attachments support
- ✅ Ticket assignment to agents
- ✅ Status tracking (Open, In Progress, Pending, Resolved, Closed)
- ✅ Priority levels (Low, Medium, High, Urgent)
- ✅ Service Provider with Laravel auto-discovery
- ✅ Policy-based authorization
- ✅ Form request validation
- ✅ Event system (TicketCreated, TicketAssigned, TicketResolved)
- ✅ Notification service (database, mail, slack channels)
- ✅ Assignment service with auto-assignment
- ✅ CLI command to close stale tickets
- ✅ RESTful API endpoints
- ✅ Metronic-styled Blade views
- ✅ Statistics dashboard
- ✅ Advanced filtering and search
- ✅ Soft deletes
- ✅ Timestamps tracking (created, updated, resolved, closed, first response)
- ✅ SLA configuration support
- ✅ Extensive configuration options
- ✅ Database seeder with default categories
- ✅ English translations
- ✅ Comprehensive documentation

### Features
- **Ticket Management:** Full CRUD with filtering, search, and pagination
- **Categorization:** Custom categories with colors and icons
- **Assignment:** Manual and auto-assignment to agents
- **Comments:** Public and internal comments, mark as solution
- **Attachments:** Upload multiple files with size/type restrictions
- **Notifications:** Multi-channel notification system
- **Statistics:** Real-time ticket statistics and metrics
- **API:** RESTful API for third-party integrations
- **Permissions:** Role-based access control with Spatie Permission
- **Events:** Event-driven architecture for extensibility
- **CLI:** Artisan commands for automation
- **UI:** Modern Metronic-styled interface

### Technical
- Laravel 11 compatible
- PHP 8.1+ required
- Bithoven Core 1.4.0+ required
- Auto-discovery service provider
- PSR-4 autoloading
- Comprehensive test coverage ready
- Database connection configurable (main or separate)
- Extensible via events and service injection

### Documentation
- Complete README with installation instructions
- Configuration guide
- API documentation
- Development guide
- MIT License

---

## [Unreleased]

### Planned for v1.2.0
- Bulk actions (assign, close, delete)
- Export tickets to CSV/PDF
- SLA breach alerts
- Customer satisfaction ratings
- Ticket merge functionality
- Advanced analytics dashboard
- Custom fields support

### Planned for v1.3.0
- Knowledge base integration
- Slack webhook integration
- Multi-language support (ES, FR, DE)
- Time tracking per ticket
- Agent performance metrics

### Planned for v2.0.0
- Multi-tenant support
- Live chat integration
- AI-powered ticket routing
- Customer portal
- Mobile app support

---

[1.1.0]: https://github.com/bithoven/tickets/releases/tag/v1.1.0
[1.0.0]: https://github.com/bithoven/tickets/releases/tag/v1.0.0

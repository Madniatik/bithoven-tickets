<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | The database connection to use for tickets. Set to 'mysql' to use the
    | default Laravel connection, or create a separate connection for tickets.
    |
    */
    'database' => [
        'connection' => env('TICKETS_DB_CONNECTION', 'mysql'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Ticket Settings
    |--------------------------------------------------------------------------
    |
    | Configure default ticket behavior
    |
    */
    'auto_assign' => env('TICKETS_AUTO_ASSIGN', false),
    'default_priority' => env('TICKETS_DEFAULT_PRIORITY', 'medium'),
    'close_after_days' => env('TICKETS_CLOSE_AFTER_DAYS', 30),
    
    /*
    |--------------------------------------------------------------------------
    | Ticket Number Format
    |--------------------------------------------------------------------------
    |
    | Configure how ticket numbers are generated
    | Available placeholders: {year}, {month}, {day}, {sequence}
    |
    */
    'ticket_number_format' => env('TICKETS_NUMBER_FORMAT', 'TKT-{sequence}'),
    'ticket_number_padding' => env('TICKETS_NUMBER_PADDING', 6),
    
    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Configure notification channels for ticket events
    |
    */
    'notifications' => [
        'enabled' => env('TICKETS_NOTIFICATIONS_ENABLED', true),
        'channels' => [
            'mail' => env('TICKETS_EMAIL_NOTIFICATIONS', true),
            'database' => env('TICKETS_DATABASE_NOTIFICATIONS', true),
            'slack' => env('TICKETS_SLACK_NOTIFICATIONS', false),
        ],
        'events' => [
            'created' => true,
            'assigned' => true,
            'updated' => true,
            'commented' => true,
            'resolved' => true,
            'closed' => true,
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | File Uploads
    |--------------------------------------------------------------------------
    |
    | Configure file attachment settings
    |
    */
    'uploads' => [
        'enabled' => env('TICKETS_UPLOADS_ENABLED', true),
        'disk' => env('TICKETS_UPLOADS_DISK', 'public'),
        'path' => env('TICKETS_UPLOADS_PATH', 'tickets/attachments'),
        'max_size' => env('TICKETS_MAX_UPLOAD_SIZE', 5120), // KB
        'allowed_types' => [
            'jpg', 'jpeg', 'png', 'gif', 'webp', // Images
            'pdf', 'doc', 'docx', 'xls', 'xlsx', // Documents
            'txt', 'csv', 'json', 'xml', // Text files
            'zip', 'rar', '7z', // Archives
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Number of tickets per page in listings
    |
    */
    'pagination' => [
        'per_page' => env('TICKETS_PER_PAGE', 20),
        'per_page_options' => [10, 20, 50, 100],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Status Options
    |--------------------------------------------------------------------------
    |
    | Available ticket statuses
    |
    */
    'statuses' => [
        'open' => [
            'label' => 'Open',
            'color' => 'primary',
            'icon' => 'fa-folder-open',
        ],
        'in_progress' => [
            'label' => 'In Progress',
            'color' => 'warning',
            'icon' => 'fa-spinner',
        ],
        'pending' => [
            'label' => 'Pending',
            'color' => 'info',
            'icon' => 'fa-clock',
        ],
        'resolved' => [
            'label' => 'Resolved',
            'color' => 'success',
            'icon' => 'fa-check-circle',
        ],
        'closed' => [
            'label' => 'Closed',
            'color' => 'secondary',
            'icon' => 'fa-times-circle',
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Priority Options
    |--------------------------------------------------------------------------
    |
    | Available ticket priorities
    |
    */
    'priorities' => [
        'low' => [
            'label' => 'Low',
            'color' => 'secondary',
            'icon' => 'fa-arrow-down',
        ],
        'medium' => [
            'label' => 'Medium',
            'color' => 'primary',
            'icon' => 'fa-minus',
        ],
        'high' => [
            'label' => 'High',
            'color' => 'warning',
            'icon' => 'fa-arrow-up',
        ],
        'urgent' => [
            'label' => 'Urgent',
            'color' => 'danger',
            'icon' => 'fa-exclamation-triangle',
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | SLA Settings (Service Level Agreement)
    |--------------------------------------------------------------------------
    |
    | Response and resolution time limits by priority (in hours)
    |
    */
    'sla' => [
        'enabled' => env('TICKETS_SLA_ENABLED', false),
        'response_times' => [
            'urgent' => 1,  // 1 hour
            'high' => 4,    // 4 hours
            'medium' => 24, // 1 day
            'low' => 48,    // 2 days
        ],
        'resolution_times' => [
            'urgent' => 8,   // 8 hours
            'high' => 24,    // 1 day
            'medium' => 72,  // 3 days
            'low' => 168,    // 7 days
        ],
    ],
];

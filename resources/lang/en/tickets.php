<?php

return [
    // Statuses
    'status' => [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'pending' => 'Pending',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ],

    // Priorities
    'priority' => [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ],

    // Labels
    'labels' => [
        'ticket' => 'Ticket',
        'tickets' => 'Tickets',
        'ticket_number' => 'Ticket Number',
        'subject' => 'Subject',
        'description' => 'Description',
        'status' => 'Status',
        'priority' => 'Priority',
        'category' => 'Category',
        'assigned_to' => 'Assigned To',
        'created_by' => 'Created By',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'resolved_at' => 'Resolved At',
        'closed_at' => 'Closed At',
        'comments' => 'Comments',
        'attachments' => 'Attachments',
    ],

    // Actions
    'actions' => [
        'create_ticket' => 'Create Ticket',
        'edit_ticket' => 'Edit Ticket',
        'delete_ticket' => 'Delete Ticket',
        'view_ticket' => 'View Ticket',
        'assign_ticket' => 'Assign Ticket',
        'close_ticket' => 'Close Ticket',
        'reopen_ticket' => 'Reopen Ticket',
        'add_comment' => 'Add Comment',
        'attach_file' => 'Attach File',
    ],

    // Messages
    'messages' => [
        'ticket_created' => 'Ticket created successfully',
        'ticket_updated' => 'Ticket updated successfully',
        'ticket_deleted' => 'Ticket deleted successfully',
        'ticket_assigned' => 'Ticket assigned successfully',
        'ticket_closed' => 'Ticket closed successfully',
        'ticket_reopened' => 'Ticket reopened successfully',
        'comment_added' => 'Comment added successfully',
        'file_attached' => 'File attached successfully',
        'no_tickets' => 'No tickets found',
    ],

    // Validation
    'validation' => [
        'subject_required' => 'Please enter a subject',
        'description_required' => 'Please provide a description',
        'priority_required' => 'Please select a priority',
    ],
];

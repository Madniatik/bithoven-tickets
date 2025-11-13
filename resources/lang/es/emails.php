<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email Language Lines - Spanish
    |--------------------------------------------------------------------------
    |
    | Translations for all ticket system emails
    |
    */

    // Common
    'hello' => 'Hola',
    'regards' => 'Saludos',
    'team_signature' => 'El Equipo de Soporte',
    'view_ticket' => 'Ver Ticket',
    'ticket_number' => 'Ticket #:number',
    'priority' => 'Prioridad',
    'status' => 'Estado',
    'category' => 'Categoría',
    'created_at' => 'Creado',
    'updated_at' => 'Actualizado',

    // Ticket Created
    'ticket_created' => [
        'subject' => 'Nuevo Ticket Creado: #:number',
        'greeting' => 'Hola :name,',
        'intro' => 'Se ha creado un nuevo ticket en el sistema de soporte.',
        'details' => 'Detalles del ticket:',
        'subject_label' => 'Asunto',
        'description_label' => 'Descripción',
        'next_steps' => 'Nuestro equipo revisará tu solicitud y te responderá lo antes posible.',
        'notification' => 'Recibirás notificaciones cuando haya actualizaciones en tu ticket.',
    ],

    // Comment Added
    'comment_added' => [
        'subject' => 'Nuevo Comentario en Ticket #:number',
        'greeting' => 'Hola :name,',
        'intro' => 'Se ha agregado un nuevo comentario a tu ticket.',
        'commented_by' => 'Comentado por',
        'comment_label' => 'Comentario',
        'view_and_reply' => 'Puedes ver el ticket completo y responder haciendo clic en el botón de abajo.',
    ],

    // Status Changed
    'status_changed' => [
        'subject' => 'Estado de Ticket Actualizado: #:number',
        'greeting' => 'Hola :name,',
        'intro' => 'El estado de tu ticket ha sido actualizado.',
        'old_status' => 'Estado Anterior',
        'new_status' => 'Nuevo Estado',
        'status_open' => 'Abierto',
        'status_in_progress' => 'En Progreso',
        'status_pending' => 'Pendiente',
        'status_resolved' => 'Resuelto',
        'status_closed' => 'Cerrado',
        'message_resolved' => 'Tu ticket ha sido marcado como resuelto. Si el problema persiste, puedes reabrir el ticket respondiendo a este email.',
        'message_closed' => 'Tu ticket ha sido cerrado. Si necesitas asistencia adicional, por favor crea un nuevo ticket.',
    ],

    // Ticket Assigned
    'ticket_assigned' => [
        'subject' => 'Ticket Asignado: #:number',
        'greeting' => 'Hola :name,',
        'intro_to_agent' => 'Se te ha asignado un nuevo ticket.',
        'intro_to_user' => 'Tu ticket ha sido asignado a un agente de soporte.',
        'assigned_to' => 'Asignado a',
        'assigned_by' => 'Asignado por',
        'please_review' => 'Por favor revisa los detalles del ticket y responde lo antes posible.',
        'agent_contact' => 'Tu agente de soporte es :agent. Te contactará pronto.',
    ],

    // Priority Escalated
    'priority_escalated' => [
        'subject' => 'Prioridad de Ticket Escalada: #:number',
        'greeting' => 'Hola :name,',
        'intro' => 'La prioridad de tu ticket ha sido escalada.',
        'old_priority' => 'Prioridad Anterior',
        'new_priority' => 'Nueva Prioridad',
        'priority_low' => 'Baja',
        'priority_medium' => 'Media',
        'priority_high' => 'Alta',
        'priority_urgent' => 'Urgente',
        'reason' => 'Razón',
        'faster_response' => 'Tu ticket ahora tiene mayor prioridad y será atendido con mayor rapidez.',
    ],

    // Priority Labels
    'priorities' => [
        'low' => 'Baja',
        'medium' => 'Media',
        'high' => 'Alta',
        'urgent' => 'Urgente',
    ],

    // Status Labels
    'statuses' => [
        'open' => 'Abierto',
        'in_progress' => 'En Progreso',
        'pending' => 'Pendiente',
        'resolved' => 'Resuelto',
        'closed' => 'Cerrado',
    ],

    // Footer
    'footer' => [
        'questions' => '¿Tienes preguntas?',
        'contact_us' => 'Contáctanos en :email',
        'do_not_reply' => 'Por favor no respondas directamente a este email. Usa el sistema de tickets para todas las comunicaciones.',
    ],
];

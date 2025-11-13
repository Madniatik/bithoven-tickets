<x-default-layout>
    @section('title', 'Ticket #' . $ticket->ticket_number)

    @section('breadcrumbs')
        {{ Breadcrumbs::render('tickets.show', $ticket) }}
    @endsection
    <div class="container-fluid">
        {{-- Header with Actions --}}
        <div class="row mb-5">
            <div class="col">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-light mb-3">
                                    <i class="fas fa-arrow-left"></i> Back to Tickets
                                </a>
                                <h1 class="mb-2">
                                    <i class="fas fa-ticket-alt text-primary me-2"></i>
                                    Ticket #{{ $ticket->ticket_number }}
                                </h1>
                                <h3 class="text-gray-700 mb-3">{{ $ticket->subject }}</h3>
                                <div class="d-flex gap-2 flex-wrap">
                                    <span class="badge badge-light-{{ $ticket->status_color }} fs-6 px-3 py-2">
                                        {{ $ticket->status_label }}
                                    </span>
                                    <span class="badge badge-{{ $ticket->priority_color }} fs-6 px-3 py-2">
                                        {{ $ticket->priority_label }}
                                    </span>
                                    @if ($ticket->category)
                                        <span class="badge fs-6 px-3 py-2"
                                            style="background-color: {{ $ticket->category->color }}; color: white;">
                                            <i class="fas {{ $ticket->category->icon }} me-1"></i>
                                            {{ $ticket->category->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                @can('updateStatus', $ticket)
                                    {{-- Status Dropdown --}}
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-light-primary dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-tasks"></i> Status: {{ $ticket->status_label }}
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item {{ $ticket->status === 'open' ? 'active' : '' }}"
                                                    href="#" onclick="changeStatus('open'); return false;">
                                                    <i class="me-2 fas fa-folder-open text-info"></i> Open
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item {{ $ticket->status === 'in_progress' ? 'active' : '' }}"
                                                    href="#" onclick="changeStatus('in_progress'); return false;">
                                                    <i class="me-2 fas fa-spinner text-primary"></i> In Progress
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item {{ $ticket->status === 'pending' ? 'active' : '' }}"
                                                    href="#" onclick="changeStatus('pending'); return false;">
                                                    <i class="me-2 fas fa-clock text-warning"></i> Pending
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item {{ $ticket->status === 'resolved' ? 'active' : '' }}"
                                                    href="#" onclick="changeStatus('resolved'); return false;">
                                                    <i class="me-2 fas fa-check-circle text-success"></i> Resolved
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item {{ $ticket->status === 'closed' ? 'active' : '' }}"
                                                    href="#" onclick="changeStatus('closed'); return false;">
                                                    <i class="me-2 fas fa-times-circle text-danger"></i> Closed
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @endcan
                                @can('assign', \Bithoven\Tickets\Models\Ticket::class)
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#assignModal">
                                        <i class="fas fa-user-plus"></i> Assign
                                    </button>
                                @endcan
                                @can('delete', $ticket)
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-5">
            {{-- Main Content --}}
            <div class="col-lg-8">
                {{-- Ticket Description --}}
                <div class="card shadow-sm mb-5">
                    <div class="card-header">
                        <h3 class="card-title">Description</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-gray-700 fs-6 mb-3" style="white-space: pre-line;">
                            {{ trim($ticket->description) }}
                        </div>

                        {{-- Mostrar archivos adjuntos del ticket (solo los del ticket inicial, no de comentarios) --}}
                        @php
                            $ticketAttachments = $ticket->attachments->whereNull('comment_id');
                        @endphp
                        @if ($ticketAttachments->count() > 0)
                            <div class="mt-4">
                                <div class="text-muted fs-7 mb-3">
                                    <i class="fas fa-paperclip me-1"></i> Attached Files:
                                </div>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach ($ticketAttachments as $attachment)
                                        @php
                                            $extension = strtolower(
                                                pathinfo($attachment->filename, PATHINFO_EXTENSION),
                                            );
                                            $isImage = in_array($extension, [
                                                'jpg',
                                                'jpeg',
                                                'png',
                                                'gif',
                                                'webp',
                                                'svg',
                                            ]);
                                        @endphp

                                        @if ($isImage)
                                            {{-- Mostrar imagen --}}
                                            <div class="position-relative">
                                                <a href="{{ $attachment->url }}" data-fslightbox="ticket-attachments"
                                                    class="d-block">
                                                    <img src="{{ $attachment->url }}"
                                                        alt="{{ $attachment->original_filename }}"
                                                        class="rounded shadow-sm"
                                                        style="max-width: 300px; max-height: 200px; object-fit: cover; cursor: pointer;">
                                                </a>
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <span class="badge badge-light-dark">
                                                        <i class="fas fa-image"></i>
                                                        {{ $attachment->formatted_size }}
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            {{-- Mostrar archivo no-imagen --}}
                                            <div class="d-flex align-items-center bg-light rounded p-3"
                                                style="min-width: 200px;">
                                                <div class="symbol symbol-40px me-3">
                                                    <span class="symbol-label bg-light-primary">
                                                        <i class="fas fa-file text-primary fs-5"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <a href="{{ $attachment->url }}" target="_blank"
                                                        class="text-gray-800 text-hover-primary fw-semibold d-block text-truncate"
                                                        style="max-width: 150px;">
                                                        {{ $attachment->original_filename }}
                                                    </a>
                                                    <span
                                                        class="text-muted fs-7">{{ $attachment->formatted_size }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Comments --}}
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-comments me-2"></i>
                            Comments ({{ $ticket->comments->count() }})
                        </h3>
                    </div>
                    <div class="card-body">
                        @if ($ticket->comments->count() > 0)
                            <div class="timeline">
                                @foreach ($ticket->comments as $comment)
                                    <div class="timeline-item mb-5">
                                        <div class="timeline-line w-40px"></div>
                                        <div class="timeline-icon symbol symbol-circle symbol-40px">
                                            @if ($comment->user->avatar)
                                                <img src="{{ asset('storage/' . $comment->user->avatar) }}"
                                                    alt="{{ $comment->user->name }}">
                                            @else
                                                <span class="symbol-label bg-light-primary text-primary fw-semibold">
                                                    {{ substr($comment->user->name, 0, 1) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="timeline-content mb-10 mt-n1">
                                            <div class="pe-3 mb-5">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="fs-5 fw-semibold mb-2">
                                                            {{ $comment->user->name }}
                                                            @if ($comment->is_internal)
                                                                <span class="badge badge-light-warning ms-2">Internal
                                                                    Note</span>
                                                            @endif
                                                            @if ($comment->is_solution)
                                                                <span class="badge badge-light-success ms-2">
                                                                    <i class="fas fa-check"></i> Solution
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="d-flex align-items-center mt-1 fs-6">
                                                            <div class="text-muted me-2 fs-7">
                                                                {{ $comment->created_at->diffForHumans() }}</div>
                                                        </div>
                                                    </div>
                                                    @if ($comment->user_id === auth()->id())
                                                        <button type="button"
                                                            class="btn btn-sm btn-icon btn-light-danger"
                                                            onclick="deleteComment({{ $comment->id }})"
                                                            title="Delete comment">
                                                            <i class="fas fa-trash fs-7"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="overflow-auto pb-5">
                                                <div class="text-gray-800 mb-3" style="white-space: pre-line;">
                                                    {{ trim($comment->comment) }}</div>

                                                {{-- Mostrar archivos adjuntos --}}
                                                @if ($comment->attachments && $comment->attachments->count() > 0)
                                                    <div class="mt-4">
                                                        <div class="d-flex flex-wrap gap-3">
                                                            @foreach ($comment->attachments as $attachment)
                                                                @php
                                                                    $extension = strtolower(
                                                                        pathinfo(
                                                                            $attachment->filename,
                                                                            PATHINFO_EXTENSION,
                                                                        ),
                                                                    );
                                                                    $isImage = in_array($extension, [
                                                                        'jpg',
                                                                        'jpeg',
                                                                        'png',
                                                                        'gif',
                                                                        'webp',
                                                                        'svg',
                                                                    ]);
                                                                @endphp

                                                                @if ($isImage)
                                                                    {{-- Mostrar imagen --}}
                                                                    <div class="position-relative">
                                                                        <a href="{{ $attachment->url }}"
                                                                            data-fslightbox="comment-{{ $comment->id }}"
                                                                            class="d-block">
                                                                            <img src="{{ $attachment->url }}"
                                                                                alt="{{ $attachment->original_filename }}"
                                                                                class="rounded shadow-sm"
                                                                                style="max-width: 300px; max-height: 200px; object-fit: cover; cursor: pointer;">
                                                                        </a>
                                                                        <div class="position-absolute top-0 end-0 m-2">
                                                                            <span class="badge badge-light-dark">
                                                                                <i class="fas fa-image"></i>
                                                                                {{ $attachment->formatted_size }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    {{-- Mostrar archivo no-imagen --}}
                                                                    <div class="d-flex align-items-center bg-light rounded p-3"
                                                                        style="min-width: 200px;">
                                                                        <div class="symbol symbol-40px me-3">
                                                                            <span
                                                                                class="symbol-label bg-light-primary">
                                                                                <i
                                                                                    class="fas fa-file text-primary fs-5"></i>
                                                                            </span>
                                                                        </div>
                                                                        <div class="flex-grow-1">
                                                                            <a href="{{ $attachment->url }}"
                                                                                target="_blank"
                                                                                class="text-gray-800 text-hover-primary fw-semibold d-block text-truncate"
                                                                                style="max-width: 150px;">
                                                                                {{ $attachment->original_filename }}
                                                                            </a>
                                                                            <span
                                                                                class="text-muted fs-7">{{ $attachment->formatted_size }}</span>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10">
                                <i class="fas fa-comments fs-3x text-muted mb-3"></i>
                                <p class="text-muted">No comments yet</p>
                            </div>
                        @endif

                        {{-- Add Comment Form --}}
                        @can('view', $ticket)
                            <div class="separator my-5"></div>
                            <form action="{{ route('tickets.comments.store', $ticket) }}" method="POST"
                                id="commentForm" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Add Comment</label>
                                    
                                    {{-- Quick Response Hint for Staff --}}
                                    @can('edit-tickets')
                                    <div class="alert alert-light-info d-flex align-items-center p-3 mb-2">
                                        {!! getIcon('information-5', 'fs-3 text-info me-3') !!}
                                        <div class="fs-7">
                                            <strong>Quick Tip:</strong> Type <code>/</code> to see quick response shortcuts
                                        </div>
                                    </div>
                                    @endcan
                                    
                                    <div class="position-relative">
                                        <textarea name="comment" 
                                                  id="comment-textarea"
                                                  class="form-control" 
                                                  rows="4" 
                                                  placeholder="Type your comment... (staff: use / for quick responses)" 
                                                  required></textarea>
                                        
                                        {{-- Canned Responses Dropdown --}}
                                        <div id="canned-responses-dropdown" 
                                             class="position-absolute bg-white border rounded shadow-sm" 
                                             style="display: none; z-index: 1000; max-height: 300px; overflow-y: auto; width: 100%; top: 100%; left: 0;">
                                        </div>
                                    </div>
                                </div>
                                @can('addInternalComment', $ticket)
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="is_internal" id="isInternal"
                                            value="1">
                                        <label class="form-check-label" for="isInternal">
                                            Internal Note (not visible to customer)
                                        </label>
                                    </div>
                                @endcan
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-paperclip me-1"></i> Attach Files (Optional)
                                    </label>
                                    <input type="file" name="attachments[]" class="form-control" multiple
                                        accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip">
                                    <div class="form-text">You can upload images, PDFs, documents, and ZIP files</div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Post Comment
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Ticket Information --}}
                <div class="card shadow-sm mb-5">
                    <div class="card-header">
                        <h3 class="card-title">Ticket Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="text-muted fs-7 mb-1">Created By</div>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-35px symbol-circle me-3">
                                    @if ($ticket->user->avatar)
                                        <img src="{{ asset('storage/' . $ticket->user->avatar) }}"
                                            alt="{{ $ticket->user->name }}">
                                    @else
                                        <span class="symbol-label bg-light-primary text-primary fw-semibold">
                                            {{ substr($ticket->user->name, 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <span class="fw-semibold text-gray-800 d-block">{{ $ticket->user->name }}</span>
                                    <span class="text-muted fs-7">{{ $ticket->user->email }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="separator my-4"></div>

                        <div class="mb-4">
                            <div class="text-muted fs-7 mb-1">Assigned To</div>
                            @if ($ticket->assignedUser)
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px symbol-circle me-3">
                                        @if ($ticket->assignedUser->avatar)
                                            <img src="{{ asset('storage/' . $ticket->assignedUser->avatar) }}"
                                                alt="{{ $ticket->assignedUser->name }}">
                                        @else
                                            <span class="symbol-label bg-light-success text-success fw-semibold">
                                                {{ substr($ticket->assignedUser->name, 0, 1) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <span
                                            class="fw-semibold text-gray-800 d-block">{{ $ticket->assignedUser->name }}</span>
                                        <span class="text-muted fs-7">{{ $ticket->assignedUser->email }}</span>
                                    </div>
                                </div>
                            @else
                                <span class="badge badge-light-warning">Unassigned</span>
                            @endif
                        </div>

                        <div class="separator my-4"></div>

                        <div class="mb-4">
                            <div class="text-muted fs-7 mb-2">Category</div>
                            @can('manage-ticket-categories')
                                <form action="{{ route('tickets.update-category', $ticket) }}" method="POST"
                                    id="categoryForm">
                                    @csrf
                                    @method('PATCH')
                                    <select name="category_id" class="form-select form-select-sm"
                                        onchange="this.form.submit()">
                                        <option value="">No Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ $ticket->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @else
                                @if ($ticket->category)
                                    <span class="badge fs-7"
                                        style="background-color: {{ $ticket->category->color }}; color: white;">
                                        <i class="fas {{ $ticket->category->icon }} me-1"></i>
                                        {{ $ticket->category->name }}
                                    </span>
                                @else
                                    <span class="text-muted">No Category</span>
                                @endif
                            @endcan
                        </div>

                        <div class="separator my-4"></div>

                        <div class="mb-4">
                            <div class="text-muted fs-7 mb-2">Priority</div>
                            @can('manage-ticket-categories')
                                <form action="{{ route('tickets.update-priority', $ticket) }}" method="POST"
                                    id="priorityForm">
                                    @csrf
                                    @method('PATCH')
                                    <select name="priority" class="form-select form-select-sm"
                                        onchange="this.form.submit()">
                                        <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>
                                            Low
                                        </option>
                                        <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>
                                            Medium
                                        </option>
                                        <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>
                                            High
                                        </option>
                                        <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>
                                            Urgent
                                        </option>
                                    </select>
                                </form>
                            @else
                                <span class="badge badge-{{ $ticket->priority_color }} fs-7">
                                    {{ $ticket->priority_label }}
                                </span>
                            @endcan
                        </div>

                        <div class="separator my-4"></div>

                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-muted fs-7 mb-1">Created</div>
                                <div class="fw-semibold text-gray-800">{{ $ticket->created_at->format('M d, Y') }}
                                </div>
                                <div class="text-muted fs-8">{{ $ticket->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted fs-7 mb-1">Updated</div>
                                <div class="fw-semibold text-gray-800">{{ $ticket->updated_at->format('M d, Y') }}
                                </div>
                                <div class="text-muted fs-8">{{ $ticket->updated_at->diffForHumans() }}</div>
                            </div>
                            @if ($ticket->resolved_at)
                                <div class="col-6">
                                    <div class="text-muted fs-7 mb-1">Resolved</div>
                                    <div class="fw-semibold text-gray-800">
                                        {{ $ticket->resolved_at->format('M d, Y') }}</div>
                                </div>
                            @endif
                            @if ($ticket->closed_at)
                                <div class="col-6">
                                    <div class="text-muted fs-7 mb-1">Closed</div>
                                    <div class="fw-semibold text-gray-800">{{ $ticket->closed_at->format('M d, Y') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Attachments --}}
                @if ($ticket->attachments->count() > 0)
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-paperclip me-2"></i>
                                Attachments ({{ $ticket->attachments->count() }})
                            </h3>
                        </div>
                        <div class="card-body">
                            @foreach ($ticket->attachments as $attachment)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="symbol symbol-40px me-3">
                                        <span class="symbol-label bg-light-primary">
                                            <i class="fas fa-file text-primary fs-5"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <a href="{{ $attachment->url }}" target="_blank"
                                            class="text-gray-800 text-hover-primary fw-semibold d-block">
                                            {{ $attachment->original_filename }}
                                        </a>
                                        <span class="text-muted fs-7">{{ $attachment->formatted_size }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Assign Modal --}}
    @can('assign', \Bithoven\Tickets\Models\Ticket::class)
        <div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('tickets.assign', $ticket) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Assign Ticket</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">Assign to Agent</label>
                                <select name="assigned_to" class="form-select" required>
                                    <option value="">Select agent...</option>
                                    @foreach ($agents as $agent)
                                        <option value="{{ $agent->id }}"
                                            {{ $ticket->assigned_to == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Assign</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @push('scripts')
        {{-- Fslightbox para galería de imágenes --}}
        <script src="https://cdn.jsdelivr.net/npm/fslightbox@3.4.1/index.min.js"></script>

        <script>
            function changeStatus(status) {
                // Mapeo de status a mensajes amigables
                const statusLabels = {
                    'open': 'Open',
                    'in_progress': 'In Progress',
                    'pending': 'Pending',
                    'resolved': 'Resolved',
                    'closed': 'Closed'
                };

                Swal.fire({
                    title: 'Change Status?',
                    text: `Change ticket status to "${statusLabels[status]}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route('tickets.update', $ticket) }}', {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    status: status,
                                    subject: '{{ $ticket->subject }}',
                                    description: '{{ $ticket->description }}',
                                    priority: '{{ $ticket->priority }}'
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success || data.message) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Status Updated!',
                                        text: `Ticket status changed to "${statusLabels[status]}"`,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to update ticket status'
                                });
                            });
                    }
                });
            }

            function closeTicket() {
                if (confirm('Are you sure you want to close this ticket?')) {
                    fetch('{{ route('tickets.close', $ticket) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            }
                        });
                }
            }

            function reopenTicket() {
                if (confirm('Are you sure you want to reopen this ticket?')) {
                    fetch('{{ route('tickets.reopen', $ticket) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            }
                        });
                }
            }

            function confirmDelete() {
                Swal.fire({
                    title: 'Delete Ticket?',
                    text: "This action cannot be undone. All ticket data, comments, and attachments will be permanently deleted.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('tickets.destroy', $ticket) }}';

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';

                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';

                        form.appendChild(csrfToken);
                        form.appendChild(methodField);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

            function deleteComment(commentId) {
                Swal.fire({
                    title: 'Delete Comment?',
                    text: "This action cannot be undone. The comment and all its attachments will be permanently deleted.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/tickets/{{ $ticket->id }}/comments/${commentId}`;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';

                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';

                        form.appendChild(csrfToken);
                        form.appendChild(methodField);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
            
            // ========= CANNED RESPONSES AUTOCOMPLETE =========
            @can('edit-tickets')
            (function() {
                const textarea = document.getElementById('comment-textarea');
                const dropdown = document.getElementById('canned-responses-dropdown');
                let responses = [];
                let selectedIndex = -1;
                
                if (!textarea || !dropdown) return;
                
                // Detect "/" keystroke
                textarea.addEventListener('keyup', function(e) {
                    const cursorPos = this.selectionStart;
                    const textBeforeCursor = this.value.substring(0, cursorPos);
                    const lastWord = textBeforeCursor.split(/\s/).pop();
                    
                    // Check if last word starts with /
                    if (lastWord.startsWith('/') && lastWord.length > 1) {
                        const shortcut = lastWord;
                        searchResponses(shortcut);
                    } else {
                        hideDropdown();
                    }
                    
                    // Arrow navigation
                    if (dropdown.style.display === 'block') {
                        if (e.key === 'ArrowDown') {
                            e.preventDefault();
                            selectedIndex = Math.min(selectedIndex + 1, responses.length - 1);
                            highlightItem(selectedIndex);
                        } else if (e.key === 'ArrowUp') {
                            e.preventDefault();
                            selectedIndex = Math.max(selectedIndex - 1, 0);
                            highlightItem(selectedIndex);
                        } else if (e.key === 'Enter' && selectedIndex >= 0) {
                            e.preventDefault();
                            insertResponse(responses[selectedIndex]);
                        } else if (e.key === 'Escape') {
                            hideDropdown();
                        }
                    }
                });
                
                function searchResponses(shortcut) {
                    // Use public endpoint accessible to agents with edit-tickets permission
                    fetch(`{{ url('canned-responses/search') }}?shortcut=${encodeURIComponent(shortcut)}`)
                        .then(res => res.json())
                        .then(data => {
                            responses = data;
                            if (data.length > 0) {
                                showDropdown(data);
                            } else {
                                hideDropdown();
                            }
                        })
                        .catch(err => {
                            console.error('Error fetching responses:', err);
                            hideDropdown();
                        });
                }
                
                function showDropdown(items) {
                    dropdown.innerHTML = '';
                    selectedIndex = -1;
                    
                    items.forEach((item, index) => {
                        const div = document.createElement('div');
                        div.className = 'p-3 border-bottom cursor-pointer hover-bg-light';
                        div.style.cursor = 'pointer';
                        div.dataset.index = index;
                        div.innerHTML = `
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <code class="text-primary fw-bold">${item.shortcut}</code>
                                    <span class="text-muted ms-2">${item.title}</span>
                                </div>
                                <span class="badge badge-light-${item.is_public ? 'success' : 'warning'} fs-8">
                                    ${item.is_public ? 'Public' : 'Internal'}
                                </span>
                            </div>
                            <div class="text-gray-600 fs-7 mt-1" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${item.content.substring(0, 80)}${item.content.length > 80 ? '...' : ''}
                            </div>
                        `;
                        
                        div.addEventListener('click', () => insertResponse(item));
                        div.addEventListener('mouseenter', () => {
                            selectedIndex = index;
                            highlightItem(index);
                        });
                        
                        dropdown.appendChild(div);
                    });
                    
                    dropdown.style.display = 'block';
                }
                
                function hideDropdown() {
                    dropdown.style.display = 'none';
                    dropdown.innerHTML = '';
                    responses = [];
                    selectedIndex = -1;
                }
                
                function highlightItem(index) {
                    const items = dropdown.querySelectorAll('[data-index]');
                    items.forEach((item, i) => {
                        if (i === index) {
                            item.classList.add('bg-light-primary');
                        } else {
                            item.classList.remove('bg-light-primary');
                        }
                    });
                }
                
                function insertResponse(response) {
                    const cursorPos = textarea.selectionStart;
                    const textBefore = textarea.value.substring(0, cursorPos);
                    const textAfter = textarea.value.substring(cursorPos);
                    
                    // Remove the shortcut
                    const lastSlashIndex = textBefore.lastIndexOf('/');
                    const newTextBefore = textBefore.substring(0, lastSlashIndex);
                    
                    // Insert response content
                    textarea.value = newTextBefore + response.content + textAfter;
                    
                    // Set cursor position after inserted text
                    const newCursorPos = newTextBefore.length + response.content.length;
                    textarea.setSelectionRange(newCursorPos, newCursorPos);
                    textarea.focus();
                    
                    hideDropdown();
                    
                    // Show success toast
                    const toast = document.createElement('div');
                    toast.className = 'position-fixed bottom-0 end-0 m-5 alert alert-success alert-dismissible fade show';
                    toast.style.zIndex = '9999';
                    toast.innerHTML = `
                        <strong>Response inserted!</strong> "${response.title}"
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                }
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!textarea.contains(e.target) && !dropdown.contains(e.target)) {
                        hideDropdown();
                    }
                });
            })();
            @endcan
        </script>
    @endpush
</x-default-layout>

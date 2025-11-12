<x-default-layout>
    @section('title', 'Edit Ticket #' . $ticket->ticket_number)

    @section('breadcrumbs')
        {{ Breadcrumbs::render('tickets.edit', $ticket) }}
    @endsection
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            {{-- Header --}}
            <div class="card shadow-sm mb-5">
                <div class="card-body">
                    <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-light mb-3">
                        <i class="fas fa-arrow-left"></i> Back to Ticket
                    </a>
                    <h1 class="mb-2">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Edit Ticket #{{ $ticket->ticket_number }}
                    </h1>
                    <p class="text-muted mb-0">Update ticket details</p>
                </div>
            </div>

            {{-- Form --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('tickets.update', $ticket) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Subject --}}
                        <div class="mb-5">
                            <label class="form-label required">Subject</label>
                            <input type="text" 
                                   name="subject" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   placeholder="Brief description of your issue"
                                   value="{{ old('subject', $ticket->subject) }}"
                                   required>
                            @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-5">
                            <label class="form-label required">Description</label>
                            <textarea name="description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="8"
                                      placeholder="Please provide detailed information about your issue"
                                      required>{{ old('description', $ticket->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-5 mb-5">
                            {{-- Priority --}}
                            <div class="col-md-6">
                                <label class="form-label required">Priority</label>
                                <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                    <option value="">Select priority...</option>
                                    <option value="low" {{ old('priority', $ticket->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', $ticket->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority', $ticket->priority) == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority', $ticket->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Category --}}
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                    <option value="">Select category...</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $ticket->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="row g-5 mb-5">
                            <div class="col-md-6">
                                <label class="form-label required">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="open" {{ old('status', $ticket->status) == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ old('status', $ticket->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="pending" {{ old('status', $ticket->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="resolved" {{ old('status', $ticket->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ old('status', $ticket->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Attachments --}}
                        @if(config('tickets.uploads.enabled'))
                        <div class="mb-5">
                            <label class="form-label">Add Attachments</label>
                            <input type="file" 
                                   name="attachments[]" 
                                   class="form-control @error('attachments.*') is-invalid @enderror" 
                                   multiple
                                   accept=".{{ implode(',.' , config('tickets.uploads.allowed_types')) }}">
                            @error('attachments.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Max {{ config('tickets.uploads.max_size') / 1024 }}MB per file. 
                                Allowed types: {{ implode(', ', config('tickets.uploads.allowed_types')) }}
                            </div>
                        </div>
                        @endif

                        {{-- Assign (staff only) --}}
                        @can('assign-tickets')
                        <div class="mb-5">
                            <label class="form-label">Assign To</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Unassigned</option>
                                @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ old('assigned_to', $ticket->assigned_to) == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endcan

                        {{-- Actions --}}
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-default-layout>

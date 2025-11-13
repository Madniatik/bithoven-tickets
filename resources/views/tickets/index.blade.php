<x-default-layout>
    @section('title', 'Tickets')

    @section('breadcrumbs')
        {{ Breadcrumbs::render('tickets.index') }}
    @endsection

    @push('styles')
        <style>
            .ticket-status-badge {
                padding: 0.5rem 1rem;
                border-radius: 0.475rem;
                font-weight: 600;
                font-size: 0.95rem;
            }

            .ticket-priority-badge {
                padding: 0.35rem 0.75rem;
                border-radius: 0.375rem;
                font-size: 0.85rem;
            }

            /* DataTables Metronic Styling */
            .dataTables_wrapper .dataTables_paginate {
                margin-top: 1rem;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0.5rem 0.75rem;
                margin: 0 0.125rem;
                border: 1px solid #e4e6ef;
                color: #7e8299;
                background: #fff;
                border-radius: 0.475rem;
                font-weight: 600;
                font-size: 0.925rem;
                text-decoration: none;
                cursor: pointer;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background: #f5f8fa;
                border-color: #009ef7;
                color: #009ef7;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background: #009ef7;
                border-color: #009ef7;
                color: #fff;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
                background: #f5f8fa;
                border-color: #e4e6ef;
                color: #b5b5c3;
                cursor: not-allowed;
            }
        </style>
    @endpush
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="row mb-5">
            <div class="col">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="mb-2">
                                    <i class="fas fa-ticket-alt text-primary me-2"></i>
                                    Support Tickets
                                </h1>
                                <p class="text-muted mb-0">Manage and track support tickets</p>
                            </div>
                            @can('create-tickets')
                                <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-plus"></i> New Ticket
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row g-5 mb-5">
            <div class="col-md-3">
                <div class="card card-flush h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center mb-3">
                            <div class="symbol symbol-50px me-3">
                                <span class="symbol-label bg-light-primary">
                                    <i class="fas fa-inbox fs-2x text-primary"></i>
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-700 fw-semibold d-block fs-6">Total</span>
                                <span class="text-gray-400 fw-semibold d-block fs-7">All Tickets</span>
                            </div>
                        </div>
                        <div class="fs-2x fw-bold text-gray-800">{{ $statistics['total'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-flush h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center mb-3">
                            <div class="symbol symbol-50px me-3">
                                <span class="symbol-label bg-light-success">
                                    <i class="fas fa-folder-open fs-2x text-success"></i>
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-700 fw-semibold d-block fs-6">Open</span>
                                <span class="text-gray-400 fw-semibold d-block fs-7">Active Tickets</span>
                            </div>
                        </div>
                        <div class="fs-2x fw-bold text-gray-800">{{ $statistics['open'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-flush h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center mb-3">
                            <div class="symbol symbol-50px me-3">
                                <span class="symbol-label bg-light-warning">
                                    <i class="fas fa-exclamation-triangle fs-2x text-warning"></i>
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-700 fw-semibold d-block fs-6">Urgent</span>
                                <span class="text-gray-400 fw-semibold d-block fs-7">High Priority</span>
                            </div>
                        </div>
                        <div class="fs-2x fw-bold text-gray-800">{{ $statistics['urgent'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-flush h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-center mb-3">
                            <div class="symbol symbol-50px me-3">
                                <span class="symbol-label bg-light-info">
                                    <i class="fas fa-user-slash fs-2x text-info"></i>
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-700 fw-semibold d-block fs-6">Unassigned</span>
                                <span class="text-gray-400 fw-semibold d-block fs-7">Need Assignment</span>
                            </div>
                        </div>
                        <div class="fs-2x fw-bold text-gray-800">{{ $statistics['unassigned'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <form method="GET" action="{{ route('tickets.index') }}" id="filtersForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Ticket #, subject..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>
                                    In Progress</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>
                                    Resolved</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="">All Priorities</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low
                                </option>
                                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium
                                </option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High
                                </option>
                                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Assigned To</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">All Assignments</option>
                                <option value="unassigned"
                                    {{ request('assigned_to') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}"
                                        {{ request('assigned_to') == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                            <a href="{{ route('tickets.index') }}?clear_filters=1" class="btn btn-light">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tickets Table --}}
        <div class="card shadow-sm">
            <div class="card-body py-4">
                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table align-middle table-row-dashed fs-6 gy-5', 'id' => 'tickets-table']) !!}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {!! $dataTable->scripts() !!}
    @endpush
</x-default-layout>

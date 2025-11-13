<x-default-layout>
    @section('title', 'Logs de Automatización')
    @section('breadcrumbs')
        {{ Breadcrumbs::render('tickets.automation.logs', $automation) }}
    @endsection

    <div class="card mb-6">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h2 class="mb-2">{{ $automation->name }}</h2>
                    <div class="text-muted">{{ $automation->description }}</div>
                    <div class="mt-3">
                        <span
                            class="badge badge-light-{{ $automation->type_badge }} me-2">{{ $automation->type_label }}</span>
                        <span class="badge badge-light-{{ $automation->is_active ? 'success' : 'secondary' }}">
                            {{ $automation->is_active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fs-2hx fw-bold text-gray-900">{{ number_format($automation->execution_count) }}</div>
                    <div class="text-muted">Ejecuciones Totales</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title">Historial de Ejecuciones</h3>
            <div class="card-toolbar">
                <a href="{{ route('tickets.automation.edit', $automation->id) }}"
                    class="btn btn-sm btn-light-primary me-2">
                    {!! getIcon('pencil', 'fs-3') !!}
                    Editar Regla
                </a>
                <a href="{{ route('tickets.automation.index') }}" class="btn btn-sm btn-light">
                    Volver
                </a>
            </div>
        </div>

        <div class="card-body py-4">
            <div class="table-responsive">
                {!! $dataTable->table([
                    'class' => 'table align-middle table-row-dashed fs-6 gy-5',
                    'id' => 'automation-logs-table',
                ]) !!}
            </div>
        </div>
    </div>

    @push('scripts')
        {!! $dataTable->scripts() !!}

        <script>
            // Initialize tooltips
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });
        </script>
    @endpush

    @push('styles')
        <style>
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
</x-default-layout>

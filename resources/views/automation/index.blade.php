<x-default-layout>
    @section('title', 'Reglas de Automatización')
    @section('breadcrumbs')
        {{ Breadcrumbs::render('tickets.automation.index') }}
    @endsection

    <!--begin::Stats Row-->
    <div class="row g-6 g-xl-9 mb-6">
        <div class="col-md-3 col-sm-6">
            <div class="card card-flush h-100">
                <div class="card-body p-5">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold fs-6 text-gray-600">Total Reglas</div>
                        <div class="fs-2hx fw-bold text-gray-900">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card card-flush h-100">
                <div class="card-body p-5">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold fs-6 text-success">Activas</div>
                        <div class="fs-2hx fw-bold text-success">{{ $stats['active'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card card-flush h-100">
                <div class="card-body p-5">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold fs-6 text-gray-400">Inactivas</div>
                        <div class="fs-2hx fw-bold text-gray-400">{{ $stats['inactive'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card card-flush h-100">
                <div class="card-body p-5">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold fs-6 text-primary">Ejecuciones</div>
                        <div class="fs-2hx fw-bold text-primary">{{ number_format($stats['total_executions']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Stats Row-->

    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!--begin::Search-->
                    <div class="d-flex align-items-center position-relative">
                        {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                        <input type="text" id="search-input" class="form-control form-control-solid w-250px ps-13" 
                               placeholder="Buscar regla..." value="{{ request('search') }}" />
                    </div>
                    <!--end::Search-->
                    
                    <!--begin::Type Filter-->
                    <select id="type-filter" class="form-select form-select-solid w-auto">
                        <option value="">Todos los tipos</option>
                        <option value="auto_close" {{ request('type') == 'auto_close' ? 'selected' : '' }}>Auto-Close</option>
                        <option value="auto_escalate" {{ request('type') == 'auto_escalate' ? 'selected' : '' }}>Auto-Escalate</option>
                        <option value="auto_assign" {{ request('type') == 'auto_assign' ? 'selected' : '' }}>Auto-Assign</option>
                        <option value="auto_response" {{ request('type') == 'auto_response' ? 'selected' : '' }}>Auto-Response</option>
                    </select>
                    <!--end::Type Filter-->
                    
                    <!--begin::Status Filter-->
                    <select id="status-filter" class="form-select form-select-solid w-auto">
                        <option value="">Todos los estados</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activas</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                    <!--end::Status Filter-->
                </div>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('tickets.automation.create') }}" class="btn btn-primary">
                    {!! getIcon('plus', 'fs-2') !!}
                    Nueva Regla
                </a>
            </div>
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body py-4">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table align-middle table-row-dashed fs-6 gy-5', 'id' => 'automation-rules-table']) !!}
            </div>
        </div>
        <!--end::Card body-->
    </div>

    @push('scripts')
        {!! $dataTable->scripts() !!}
        
        <script>
        // Toggle active status
        $(document).on('change', '.automation-toggle', function() {
            const url = $(this).data('url');
            const isActive = $(this).is(':checked');
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                $(this).prop('checked', !isActive); // Revert on error
                toastr.error('Error al actualizar el estado de la regla');
            });
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

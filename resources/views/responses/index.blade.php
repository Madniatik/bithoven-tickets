<x-default-layout>
    @section('title', 'Respuestas Rápidas')
    @section('breadcrumbs')
        {{ Breadcrumbs::render('tickets.responses.index') }}
    @endsection

    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar respuesta..." />
                </div>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('tickets.responses.create') }}" class="btn btn-primary">
                    {!! getIcon('plus', 'fs-2') !!}
                    Nueva Respuesta Rápida
                </a>
            </div>
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body py-4">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table align-middle table-row-dashed fs-6 gy-5', 'id' => 'responses-table']) !!}
            </div>
        </div>
        <!--end::Card body-->
    </div>

    @push('scripts')
        {!! $dataTable->scripts() !!}
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

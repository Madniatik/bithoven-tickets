<?php

namespace Bithoven\Tickets\DataTables;

use Bithoven\Tickets\Models\TicketAutomationRule;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AutomationRulesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('name', function ($automation) {
                return '<div class="d-flex flex-column">'
                    . '<span class="text-gray-800 fw-bold mb-1">' . e($automation->name) . '</span>'
                    . '<span class="text-muted fs-7">' . e(\Illuminate\Support\Str::limit($automation->description, 50)) . '</span>'
                    . '</div>';
            })
            ->addColumn('type', function ($automation) {
                return '<span class="badge badge-light-' . $automation->type_badge . '">'
                    . $automation->type_label . '</span>';
            })
            ->addColumn('status', function ($automation) {
                $toggleUrl = route('tickets.automation.toggle', $automation);
                $checked = $automation->is_active ? 'checked' : '';
                
                return '<div class="form-check form-switch form-check-custom form-check-solid">'
                    . '<input class="form-check-input automation-toggle" type="checkbox" ' . $checked 
                    . ' data-url="' . $toggleUrl . '" />'
                    . '</div>';
            })
            ->addColumn('executions', function ($automation) {
                return '<div class="text-center">'
                    . '<span class="badge badge-light fs-6">' . number_format($automation->execution_count) . '</span>'
                    . '</div>';
            })
            ->addColumn('last_run', function ($automation) {
                if ($automation->last_executed_at) {
                    return $automation->last_executed_at->diffForHumans();
                }
                return '<span class="text-muted">Never</span>';
            })
            ->addColumn('action', function ($automation) {
                $logsUrl = route('tickets.automation.logs', $automation);
                $editUrl = route('tickets.automation.edit', $automation);
                $deleteUrl = route('tickets.automation.destroy', $automation);
                
                return '<div class="d-flex gap-2 justify-content-end">'
                    . '<a href="' . $logsUrl . '" class="btn btn-sm btn-light btn-active-light-info" title="Ver Logs">'
                    . '<i class="fas fa-history"></i></a>'
                    . '<a href="' . $editUrl . '" class="btn btn-sm btn-light btn-active-light-primary" title="Editar">'
                    . '<i class="fas fa-edit"></i></a>'
                    . '<form action="' . $deleteUrl . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Estás seguro?\')">'
                    . csrf_field() . method_field('DELETE')
                    . '<button type="submit" class="btn btn-sm btn-light btn-active-light-danger" title="Eliminar">'
                    . '<i class="fas fa-trash"></i></button>'
                    . '</form>'
                    . '</div>';
            })
            ->rawColumns(['name', 'type', 'status', 'executions', 'last_run', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(TicketAutomationRule $model): QueryBuilder
    {
        return $model->newQuery()
            ->select('ticket_automation_rules.*');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('automation-rules-table')
            ->columns($this->getColumns())
            ->ajax(route('tickets.automation.index'))
            ->dom('rtip')
            ->orderBy(0, 'desc')
            ->parameters([
                'processing' => true,
                'serverSide' => true,
                'responsive' => true,
                'pageLength' => 15,
                'lengthChange' => false,
                'language' => [
                    'info' => 'Mostrando _START_ a _END_ de _TOTAL_ reglas',
                    'paginate' => [
                        'next' => 'Siguiente',
                        'previous' => 'Anterior'
                    ],
                    'emptyTable' => 'No hay reglas de automatización',
                    'zeroRecords' => 'No se encontraron resultados',
                    'processing' => '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
                ]
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('name')
                ->title('Nombre y Descripción'),
            Column::make('type')
                ->title('Tipo')
                ->width(120),
            Column::make('status')
                ->title('Estado')
                ->width(80)
                ->addClass('text-center')
                ->orderable(false),
            Column::make('executions')
                ->title('Ejecuciones')
                ->width(100)
                ->addClass('text-center'),
            Column::make('last_run')
                ->title('Última Ejecución')
                ->width(150),
            Column::computed('action')
                ->title('Acciones')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-end'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'AutomationRules_' . date('YmdHis');
    }
}

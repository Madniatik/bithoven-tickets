<?php

namespace Bithoven\Tickets\DataTables;

use Bithoven\Tickets\Models\TicketAutomationLog;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AutomationLogsDataTable extends DataTable
{
    protected $automationId;

    public function __construct($automationId = null)
    {
        parent::__construct();
        $this->automationId = $automationId;
    }

    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('ticket', function ($log) {
                if ($log->ticket) {
                    return '<a href="' . route('tickets.show', $log->ticket) . '" class="text-primary fw-bold">'
                        . $log->ticket->ticket_number . '</a>';
                }
                return '<span class="text-muted">—</span>';
            })
            ->addColumn('status', function ($log) {
                if ($log->wasSuccessful()) {
                    return '<span class="badge badge-light-success">Success</span>';
                }
                return '<span class="badge badge-light-danger">Failed</span>';
            })
            ->addColumn('result', function ($log) {
                if ($log->result) {
                    $result = json_decode($log->result, true);
                    if (is_array($result)) {
                        return '<div class="text-muted fs-7">' . e(json_encode($result, JSON_PRETTY_PRINT)) . '</div>';
                    }
                    return '<div class="text-muted fs-7">' . e($log->result) . '</div>';
                }
                return '<span class="text-muted">—</span>';
            })
            ->addColumn('error', function ($log) {
                if ($log->error_message) {
                    return '<div class="text-danger fs-7">' . e($log->error_message) . '</div>';
                }
                return '<span class="text-muted">—</span>';
            })
            ->addColumn('executed_at', function ($log) {
                return $log->executed_at->format('d/m/Y H:i:s');
            })
            ->rawColumns(['ticket', 'status', 'result', 'error'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(TicketAutomationLog $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with('ticket')
            ->select('ticket_automation_logs.*');

        if ($this->automationId) {
            $query->where('rule_id', $this->automationId);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('automation-logs-table')
            ->columns($this->getColumns())
            ->ajax(route('tickets.automation.logs', $this->automationId))
            ->dom('rtip')
            ->orderBy(0, 'desc')
            ->parameters([
                'processing' => true,
                'serverSide' => true,
                'responsive' => true,
                'pageLength' => 20,
                'lengthChange' => false,
                'language' => [
                    'info' => 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    'paginate' => [
                        'next' => 'Siguiente',
                        'previous' => 'Anterior'
                    ],
                    'emptyTable' => 'No hay logs de ejecución',
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
            Column::make('ticket')
                ->title('Ticket')
                ->width(120),
            Column::make('status')
                ->title('Estado')
                ->width(100)
                ->addClass('text-center'),
            Column::make('result')
                ->title('Resultado'),
            Column::make('error')
                ->title('Error'),
            Column::make('executed_at')
                ->title('Ejecutado')
                ->width(150),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'AutomationLogs_' . date('YmdHis');
    }
}

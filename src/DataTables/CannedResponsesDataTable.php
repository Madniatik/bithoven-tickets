<?php

namespace Bithoven\Tickets\DataTables;

use Bithoven\Tickets\Models\CannedResponse;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CannedResponsesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('title', function ($response) {
                return '<div class="d-flex flex-column">'
                    . '<span class="text-gray-800 text-hover-primary mb-1">' . e($response->title) . '</span>'
                    . '<span class="text-muted fs-7">' . e(\Illuminate\Support\Str::limit($response->content, 50)) . '</span>'
                    . '</div>';
            })
            ->addColumn('shortcut', function ($response) {
                return '<code class="text-primary">' . e($response->shortcut) . '</code>';
            })
            ->addColumn('category', function ($response) {
                if ($response->category) {
                    return '<span class="badge" style="background-color: ' . $response->category->color . ';">'
                        . e($response->category->name) . '</span>';
                }
                return '<span class="badge badge-light-secondary">General</span>';
            })
            ->addColumn('type', function ($response) {
                return '<span class="badge badge-light-' . $response->type_badge . '">'
                    . $response->type_label . '</span>';
            })
            ->addColumn('status', function ($response) {
                if ($response->is_active) {
                    return '<span class="badge badge-light-success">Activa</span>';
                }
                return '<span class="badge badge-light-danger">Inactiva</span>';
            })
            ->addColumn('usage', function ($response) {
                return '<span class="badge badge-light fs-7">' . $response->usage_count . '</span>';
            })
            ->addColumn('action', function ($response) {
                return '<div class="d-flex gap-2 justify-content-end">'
                    . '<a href="' . route('tickets.responses.edit', $response) . '" class="btn btn-sm btn-light btn-active-light-primary">Edit</a>'
                    . '<form action="' . route('tickets.responses.destroy', $response) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Estás seguro?\')">'
                    . csrf_field() . method_field('DELETE')
                    . '<button type="submit" class="btn btn-sm btn-light btn-active-light-danger">Delete</button>'
                    . '</form>'
                    . '</div>';
            })
            ->rawColumns(['title', 'shortcut', 'category', 'type', 'status', 'usage', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(CannedResponse $model): QueryBuilder
    {
        return $model->newQuery()
            ->with('category')
            ->select('ticket_canned_responses.*');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('responses-table')
            ->columns($this->getColumns())
            ->ajax(route('tickets.responses.index'))
            ->dom('rtip')
            ->orderBy(0, 'desc')
            ->parameters([
                'processing' => true,
                'serverSide' => true,
                'responsive' => true,
                'pageLength' => 15,
                'lengthChange' => false,
                'language' => [
                    'info' => 'Mostrando _START_ a _END_ de _TOTAL_ respuestas',
                    'paginate' => [
                        'next' => 'Siguiente',
                        'previous' => 'Anterior'
                    ],
                    'emptyTable' => 'No hay respuestas rápidas creadas',
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
            Column::make('title')
                ->title('Título'),
            Column::make('shortcut')
                ->title('Shortcut')
                ->width(120),
            Column::make('category')
                ->title('Categoría')
                ->width(150),
            Column::make('type')
                ->title('Tipo')
                ->width(80),
            Column::make('status')
                ->title('Estado')
                ->width(80),
            Column::make('usage')
                ->title('Usos')
                ->width(80)
                ->addClass('text-center'),
            Column::computed('action')
                ->title('Acciones')
                ->exportable(false)
                ->printable(false)
                ->width(150)
                ->addClass('text-end'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'CannedResponses_' . date('YmdHis');
    }
}

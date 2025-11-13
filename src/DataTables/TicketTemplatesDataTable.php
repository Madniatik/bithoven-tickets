<?php

namespace Bithoven\Tickets\DataTables;

use Bithoven\Tickets\Models\TicketTemplate;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TicketTemplatesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('name', function ($template) {
                return '<div class="d-flex flex-column">'
                    . '<span class="text-gray-800 text-hover-primary mb-1">' . e($template->name) . '</span>'
                    . '<span class="text-muted fs-7">' . e(\Illuminate\Support\Str::limit($template->description, 50)) . '</span>'
                    . '</div>';
            })
            ->addColumn('category', function ($template) {
                if ($template->category) {
                    return '<span class="badge" style="background-color: ' . $template->category->color . ';">'
                        . e($template->category->name) . '</span>';
                }
                return '<span class="text-muted">—</span>';
            })
            ->addColumn('priority', function ($template) {
                return '<span class="badge badge-' . $template->priority_color . '">'
                    . $template->priority_label . '</span>';
            })
            ->addColumn('status', function ($template) {
                if ($template->is_active) {
                    return '<span class="badge badge-light-success">Activa</span>';
                }
                return '<span class="badge badge-light-danger">Inactiva</span>';
            })
            ->addColumn('usage', function ($template) {
                return '<span class="badge badge-light fs-7">' . $template->usage_count . '</span>';
            })
            ->addColumn('action', function ($template) {
                return '<div class="d-flex gap-2 justify-content-end">'
                    . '<a href="' . route('tickets.templates.edit', $template) . '" class="btn btn-sm btn-light btn-active-light-primary">Edit</a>'
                    . '<form action="' . route('tickets.templates.destroy', $template) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Estás seguro?\')">'
                    . csrf_field() . method_field('DELETE')
                    . '<button type="submit" class="btn btn-sm btn-light btn-active-light-danger">Delete</button>'
                    . '</form>'
                    . '</div>';
            })
            ->rawColumns(['name', 'category', 'priority', 'status', 'usage', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(TicketTemplate $model): QueryBuilder
    {
        return $model->newQuery()
            ->with('category')
            ->select('ticket_templates.*');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('templates-table')
            ->columns($this->getColumns())
            ->ajax(route('tickets.templates.index'))
            ->dom('rtip')
            ->orderBy(0, 'desc')
            ->parameters([
                'processing' => true,
                'serverSide' => true,
                'responsive' => true,
                'pageLength' => 15,
                'lengthChange' => false,
                'language' => [
                    'info' => 'Mostrando _START_ a _END_ de _TOTAL_ plantillas',
                    'paginate' => [
                        'next' => 'Siguiente',
                        'previous' => 'Anterior'
                    ],
                    'emptyTable' => 'No hay plantillas creadas',
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
                ->title('Nombre'),
            Column::make('category')
                ->title('Categoría')
                ->width(150),
            Column::make('priority')
                ->title('Prioridad')
                ->width(100),
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
        return 'TicketTemplates_' . date('YmdHis');
    }
}

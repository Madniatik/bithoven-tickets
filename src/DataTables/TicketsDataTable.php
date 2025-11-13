<?php

namespace Bithoven\Tickets\DataTables;

use Bithoven\Tickets\Models\Ticket;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TicketsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('ticket_number', function ($ticket) {
                return '<a href="' . route('tickets.show', $ticket) . '" class="text-gray-800 text-hover-primary fw-bold">' 
                    . $ticket->ticket_number . '</a>';
            })
            ->addColumn('subject', function ($ticket) {
                return '<a href="' . route('tickets.show', $ticket) . '" class="text-gray-800 text-hover-primary">' 
                    . e(\Illuminate\Support\Str::limit($ticket->subject, 50)) . '</a>';
            })
            ->addColumn('status', function ($ticket) {
                return '<span class="badge badge-light-' . $ticket->status_color . ' ticket-status-badge">' 
                    . $ticket->status_label . '</span>';
            })
            ->addColumn('priority', function ($ticket) {
                return '<span class="badge badge-' . $ticket->priority_color . ' ticket-priority-badge">' 
                    . $ticket->priority_label . '</span>';
            })
            ->addColumn('category', function ($ticket) {
                if ($ticket->category) {
                    return '<span class="badge" style="background-color: ' . $ticket->category->color . '; color: white;">'
                        . '<i class="fas ' . $ticket->category->icon . ' me-1"></i>' 
                        . $ticket->category->name . '</span>';
                }
                return '<span class="text-muted">—</span>';
            })
            ->addColumn('user', function ($ticket) {
                $avatar = '';
                if ($ticket->user->avatar) {
                    $avatar = '<img src="' . asset('storage/' . $ticket->user->avatar) . '" alt="' . $ticket->user->name . '">';
                } else {
                    $initial = substr($ticket->user->name, 0, 1);
                    $avatar = '<span class="symbol-label bg-light-primary text-primary fw-semibold">' . $initial . '</span>';
                }
                
                return '<div class="d-flex align-items-center">'
                    . '<div class="symbol symbol-35px symbol-circle me-3">' . $avatar . '</div>'
                    . '<span class="text-gray-800">' . e($ticket->user->name) . '</span>'
                    . '</div>';
            })
            ->addColumn('assigned', function ($ticket) {
                if ($ticket->assignedUser) {
                    $avatar = '';
                    if ($ticket->assignedUser->avatar) {
                        $avatar = '<img src="' . asset('storage/' . $ticket->assignedUser->avatar) . '" alt="' . $ticket->assignedUser->name . '">';
                    } else {
                        $initial = substr($ticket->assignedUser->name, 0, 1);
                        $avatar = '<span class="symbol-label bg-light-success text-success fw-semibold">' . $initial . '</span>';
                    }
                    
                    return '<div class="d-flex align-items-center">'
                        . '<div class="symbol symbol-35px symbol-circle me-3">' . $avatar . '</div>'
                        . '<span class="text-gray-800">' . e($ticket->assignedUser->name) . '</span>'
                        . '</div>';
                }
                return '<span class="badge badge-light-warning">Unassigned</span>';
            })
            ->addColumn('created', function ($ticket) {
                return $ticket->created_at->diffForHumans();
            })
            ->addColumn('action', function ($ticket) {
                return '<a href="' . route('tickets.show', $ticket) . '" class="btn btn-sm btn-light btn-active-light-primary">'
                    . '<i class="fas fa-eye"></i> View</a>';
            })
            ->rawColumns(['ticket_number', 'subject', 'status', 'priority', 'category', 'user', 'assigned', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Ticket $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['user', 'category', 'assignedUser'])
            ->select('tickets.*');

        // Filter by user if not admin
        $isAdmin = auth()->user()->can('edit-tickets') || auth()->user()->can('manage-ticket-categories');
        if (!$isAdmin) {
            $query->where('user_id', auth()->id());
        }

        // Apply filters from request
        if (request()->has('search') && request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('comments', function($query) use ($search) {
                      $query->where('comment', 'like', "%{$search}%");
                  });
            });
        }

        if (request()->has('status') && request('status')) {
            $query->where('status', request('status'));
        }

        if (request()->has('priority') && request('priority')) {
            $query->where('priority', request('priority'));
        }

        if (request()->has('category_id') && request('category_id')) {
            $query->where('category_id', request('category_id'));
        }

        if (request()->has('assigned_to')) {
            if (request('assigned_to') === 'unassigned') {
                $query->whereNull('assigned_to');
            } elseif (request('assigned_to')) {
                $query->where('assigned_to', request('assigned_to'));
            }
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('tickets-table')
            ->columns($this->getColumns())
            ->ajax([
                'url' => route('tickets.index'),
                'type' => 'GET',
                'data' => 'function(d) { 
                    d.status = $("[name=status]").val();
                    d.priority = $("[name=priority]").val();
                    d.category_id = $("[name=category_id]").val();
                    d.assigned_to = $("[name=assigned_to]").val();
                    d.search = $("[name=search]").val();
                }'
            ])
            ->dom('rtip')
            ->orderBy(0, 'desc')
            ->parameters([
                'processing' => true,
                'serverSide' => true,
                'responsive' => true,
                'pageLength' => 15,
                'lengthChange' => false,
                'searching' => false, // We use custom search
                'language' => [
                    'info' => 'Showing _START_ to _END_ of _TOTAL_ tickets',
                    'paginate' => [
                        'next' => 'Next',
                        'previous' => 'Previous'
                    ],
                    'emptyTable' => 'No tickets found',
                    'zeroRecords' => 'No tickets found',
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
            Column::make('ticket_number')
                ->title('Ticket #')
                ->width(120),
            Column::make('subject')
                ->title('Subject'),
            Column::make('status')
                ->title('Status')
                ->width(120)
                ->addClass('text-center'),
            Column::make('priority')
                ->title('Priority')
                ->width(100)
                ->addClass('text-center'),
            Column::make('category')
                ->title('Category')
                ->width(150),
            Column::make('user')
                ->title('Created By')
                ->width(200)
                ->orderable(false),
            Column::make('assigned')
                ->title('Assigned To')
                ->width(200)
                ->orderable(false),
            Column::make('created')
                ->title('Created')
                ->width(120),
            Column::computed('action')
                ->title('Actions')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-end'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Tickets_' . date('YmdHis');
    }
}

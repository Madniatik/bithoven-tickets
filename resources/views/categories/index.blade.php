<x-default-layout>
    @section('title', 'Ticket Categories')

    @section('breadcrumbs')
        {{ Breadcrumbs::render('ticket-categories.index') }}
    @endsection

    <div class="container-fluid">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="mb-0">
                <i class="fas fa-tags text-primary me-2"></i>
                Ticket Categories
            </h1>
            <a href="{{ route('ticket-categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Category
            </a>
        </div>

        {{-- Categories Table --}}
        <div class="card">
            <div class="card-body">
                @if ($categories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th class="w-25px">#</th>
                                    <th class="min-w-150px">Name</th>
                                    <th class="min-w-200px">Description</th>
                                    <th class="min-w-100px">Icon</th>
                                    <th class="min-w-100px">Color</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-100px">Tickets</th>
                                    <th class="min-w-100px text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>{{ $category->sort_order }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge fs-6 px-3 py-2"
                                                    style="background-color: {{ $category->color }}; color: white;">
                                                    <i class="fas {{ $category->icon }} me-1"></i>
                                                    {{ $category->name }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-gray-600">{{ $category->description ?: '—' }}</span>
                                        </td>
                                        <td>
                                            <code>{{ $category->icon }}</code>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="w-30px h-20px rounded"
                                                    style="background-color: {{ $category->color }}"></div>
                                                <code>{{ $category->color }}</code>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($category->is_active)
                                                <span class="badge badge-light-success">Active</span>
                                            @else
                                                <span class="badge badge-light-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light-info">{{ $category->tickets_count ?? $category->tickets()->count() }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('ticket-categories.edit', $category) }}"
                                                class="btn btn-sm btn-icon btn-light-primary"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-light-danger"
                                                onclick="deleteCategory({{ $category->id }}, '{{ $category->name }}')"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10">
                        <i class="fas fa-tags fs-3x text-muted mb-3"></i>
                        <p class="text-muted">No categories found</p>
                        <a href="{{ route('ticket-categories.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Create First Category
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function deleteCategory(id, name) {
                Swal.fire({
                    title: 'Delete Category?',
                    text: `Are you sure you want to delete "${name}"? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/ticket-categories/${id}`;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';

                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';

                        form.appendChild(csrfToken);
                        form.appendChild(methodField);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
    @endpush
</x-default-layout>

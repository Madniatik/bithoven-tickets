<x-default-layout>
    @section('title', 'Edit Category: ' . $category->name)

    @section('breadcrumbs')
        {{ Breadcrumbs::render('ticket-categories.edit', $category) }}
    @endsection

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                {{-- Header --}}
                <div class="card shadow-sm mb-5">
                    <div class="card-body">
                        <a href="{{ route('ticket-categories.index') }}" class="btn btn-sm btn-light mb-3">
                            <i class="fas fa-arrow-left"></i> Back to Categories
                        </a>
                        <h1 class="mb-2">
                            <i class="fas fa-edit text-primary me-2"></i>
                            Edit Category
                        </h1>
                        <p class="text-muted mb-0">Update "{{ $category->name }}" category</p>
                    </div>
                </div>

                {{-- Form --}}
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('ticket-categories.update', $category) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Name --}}
                            <div class="mb-5">
                                <label class="form-label required">Name</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="e.g., Technical Support"
                                    value="{{ old('name', $category->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">The display name for this category</div>
                            </div>

                            {{-- Description --}}
                            <div class="mb-5">
                                <label class="form-label">Description</label>
                                <textarea name="description"
                                    class="form-control @error('description') is-invalid @enderror"
                                    rows="3"
                                    placeholder="Brief description of this category">{{ old('description', $category->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-5 mb-5">
                                {{-- Icon --}}
                                <div class="col-md-6">
                                    <label class="form-label">Icon</label>
                                    <input type="text" name="icon"
                                        class="form-control @error('icon') is-invalid @enderror"
                                        placeholder="fa-wrench"
                                        value="{{ old('icon', $category->icon) }}">
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        FontAwesome icon class (e.g., fa-wrench, fa-dollar-sign)
                                        <a href="https://fontawesome.com/icons" target="_blank">Browse icons</a>
                                    </div>
                                </div>

                                {{-- Color --}}
                                <div class="col-md-6">
                                    <label class="form-label">Color</label>
                                    <div class="input-group">
                                        <input type="color" name="color"
                                            class="form-control form-control-color @error('color') is-invalid @enderror"
                                            value="{{ old('color', $category->color) }}"
                                            style="max-width: 60px;">
                                        <input type="text" name="color_hex"
                                            class="form-control"
                                            placeholder="#3b82f6"
                                            value="{{ old('color', $category->color) }}"
                                            id="colorHex">
                                        @error('color')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">Badge background color (hex format)</div>
                                </div>
                            </div>

                            <div class="row g-5 mb-5">
                                {{-- Sort Order --}}
                                <div class="col-md-6">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" name="sort_order"
                                        class="form-control @error('sort_order') is-invalid @enderror"
                                        placeholder="1"
                                        value="{{ old('sort_order', $category->sort_order) }}"
                                        min="0">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Display order (lower numbers appear first)</div>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch form-check-custom form-check-solid mt-3">
                                        <input class="form-check-input" type="checkbox" name="is_active"
                                            id="isActive" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isActive">
                                            Active (visible to users)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Preview --}}
                            <div class="mb-5">
                                <label class="form-label">Preview</label>
                                <div class="p-5 bg-light rounded">
                                    <span class="badge fs-6 px-3 py-2" id="preview"
                                        style="background-color: {{ $category->color }}; color: white;">
                                        <i class="fas {{ $category->icon }} me-1"></i>
                                        {{ $category->name }}
                                    </span>
                                </div>
                            </div>

                            {{-- Tickets Count --}}
                            @if ($category->tickets()->count() > 0)
                                <div class="alert alert-info mb-5">
                                    <i class="fas fa-info-circle me-2"></i>
                                    This category is currently assigned to <strong>{{ $category->tickets()->count() }}</strong> ticket(s).
                                </div>
                            @endif

                            {{-- Actions --}}
                            <div class="d-flex justify-content-end gap-3">
                                <a href="{{ route('ticket-categories.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Category
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Sync color picker with hex input
            const colorPicker = document.querySelector('input[type="color"]');
            const colorHex = document.getElementById('colorHex');
            const preview = document.getElementById('preview');
            const nameInput = document.querySelector('input[name="name"]');
            const iconInput = document.querySelector('input[name="icon"]');

            colorPicker.addEventListener('input', (e) => {
                colorHex.value = e.target.value;
                updatePreview();
            });

            colorHex.addEventListener('input', (e) => {
                if (/^#[0-9A-F]{6}$/i.test(e.target.value)) {
                    colorPicker.value = e.target.value;
                    updatePreview();
                }
            });

            nameInput.addEventListener('input', updatePreview);
            iconInput.addEventListener('input', updatePreview);

            function updatePreview() {
                preview.style.backgroundColor = colorPicker.value;
                preview.innerHTML = `<i class="fas ${iconInput.value || 'fa-tag'} me-1"></i>${nameInput.value || 'Category Name'}`;
            }
        </script>
    @endpush
</x-default-layout>

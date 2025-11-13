<x-default-layout>
    @section('title', isset($template) ? 'Editar Plantilla' : 'Crear Plantilla')
    @section('breadcrumbs')
        {{ Breadcrumbs::render(isset($template) ? 'tickets.templates.edit' : 'tickets.templates.create', $template ?? null) }}
    @endsection

    <form action="{{ isset($template) ? route('tickets.templates.update', $template) : route('tickets.templates.store') }}" method="POST">
        @csrf
        @if(isset($template))
            @method('PUT')
        @endif

        <div class="card">
            <!--begin::Card header-->
            <div class="card-header">
                <h3 class="card-title">{{ isset($template) ? 'Editar Plantilla' : 'Nueva Plantilla' }}</h3>
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body">
                <!--begin::Name-->
                <div class="mb-10">
                    <label class="required form-label">Nombre de la Plantilla</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                           placeholder="Ej: Solicitud de Soporte Técnico" 
                           value="{{ old('name', $template->name ?? '') }}" required />
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Un nombre descriptivo para identificar la plantilla</div>
                </div>
                <!--end::Name-->

                <!--begin::Subject-->
                <div class="mb-10">
                    <label class="required form-label">Asunto del Ticket</label>
                    <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" 
                           placeholder="Ej: Problema con [Sistema/Función]" 
                           value="{{ old('subject', $template->subject ?? '') }}" required />
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <!--end::Subject-->

                <!--begin::Description-->
                <div class="mb-10">
                    <label class="required form-label">Descripción</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="8" placeholder="Describe el problema o solicitud..." required>{{ old('description', $template->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Esta será la descripción inicial del ticket</div>
                </div>
                <!--end::Description-->

                <div class="row">
                    <!--begin::Category-->
                    <div class="col-md-4 mb-10">
                        <label class="form-label">Categoría</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">Sin categoría</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ old('category_id', $template->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Category-->

                    <!--begin::Priority-->
                    <div class="col-md-4 mb-10">
                        <label class="required form-label">Prioridad</label>
                        <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                            <option value="low" {{ old('priority', $template->priority ?? 'medium') == 'low' ? 'selected' : '' }}>Baja</option>
                            <option value="medium" {{ old('priority', $template->priority ?? 'medium') == 'medium' ? 'selected' : '' }}>Media</option>
                            <option value="high" {{ old('priority', $template->priority ?? 'medium') == 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="urgent" {{ old('priority', $template->priority ?? 'medium') == 'urgent' ? 'selected' : '' }}>Urgente</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Priority-->

                    <!--begin::Status-->
                    <div class="col-md-4 mb-10">
                        <label class="form-label">Estado</label>
                        <div class="form-check form-switch form-check-custom form-check-solid mt-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                   {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }} />
                            <label class="form-check-label" for="is_active">
                                Plantilla Activa
                            </label>
                        </div>
                        <div class="form-text">Solo las plantillas activas aparecen al crear tickets</div>
                    </div>
                    <!--end::Status-->
                </div>
            </div>
            <!--end::Card body-->

            <!--begin::Card footer-->
            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('tickets.templates.index') }}" class="btn btn-light btn-active-light-primary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($template) ? 'Actualizar Plantilla' : 'Crear Plantilla' }}
                </button>
            </div>
            <!--end::Card footer-->
        </div>
    </form>
</x-default-layout>

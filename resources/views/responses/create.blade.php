<x-default-layout>
    @section('title', isset($response) ? 'Editar Respuesta Rápida' : 'Nueva Respuesta Rápida')
    @section('breadcrumbs')
        @if(isset($response))
            {{ Breadcrumbs::render('tickets.responses.edit', $response) }}
        @else
            {{ Breadcrumbs::render('tickets.responses.create') }}
        @endif
    @endsection

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ isset($response) ? 'Editar' : 'Nueva' }} Respuesta Rápida</h3>
        </div>

        <form action="{{ isset($response) ? route('tickets.responses.update', $response) : route('tickets.responses.store') }}" 
              method="POST">
            @csrf
            @if(isset($response))
                @method('PUT')
            @endif

            <div class="card-body">
                <!--begin::Input group - Title-->
                <div class="mb-5">
                    <label class="form-label required">Título</label>
                    <input type="text" 
                           name="title" 
                           class="form-control @error('title') is-invalid @enderror" 
                           placeholder="Saludo Inicial" 
                           value="{{ old('title', $response->title ?? '') }}" 
                           required />
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Nombre descriptivo de la respuesta</div>
                </div>
                <!--end::Input group-->

                <!--begin::Input group - Shortcut-->
                <div class="mb-5">
                    <label class="form-label required">Shortcut</label>
                    <input type="text" 
                           name="shortcut" 
                           class="form-control @error('shortcut') is-invalid @enderror" 
                           placeholder="/greeting" 
                           value="{{ old('shortcut', $response->shortcut ?? '') }}" 
                           pattern="^\/[a-z0-9-]+$"
                           required />
                    @error('shortcut')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Debe comenzar con <code>/</code> y solo contener letras minúsculas, números y guiones. 
                        <br>Ejemplo: <code>/greeting</code>, <code>/resolved</code>, <code>/thanks</code>
                    </div>
                </div>
                <!--end::Input group-->

                <!--begin::Input group - Content-->
                <div class="mb-5">
                    <label class="form-label required">Contenido</label>
                    <textarea name="content" 
                              class="form-control @error('content') is-invalid @enderror" 
                              rows="8" 
                              required>{{ old('content', $response->content ?? '') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Texto completo que se insertará al usar el shortcut</div>
                </div>
                <!--end::Input group-->

                <!--begin::Input group - Category-->
                <div class="mb-5">
                    <label class="form-label">Categoría</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">Sin categoría (General)</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                    {{ old('category_id', $response->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Asociar a una categoría específica (opcional)</div>
                </div>
                <!--end::Input group-->

                <div class="row">
                    <!--begin::Input group - Is Public-->
                    <div class="col-md-6 mb-5">
                        <label class="form-label">Visibilidad</label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="is_public" 
                                   id="is_public"
                                   value="1" 
                                   {{ old('is_public', $response->is_public ?? true) ? 'checked' : '' }} />
                            <label class="form-check-label" for="is_public">
                                Respuesta Pública
                            </label>
                        </div>
                        <div class="form-text">
                            <strong>Pública:</strong> Se envía al usuario<br>
                            <strong>Interna:</strong> Solo visible para agentes (nota privada)
                        </div>
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group - Is Active-->
                    <div class="col-md-6 mb-5">
                        <label class="form-label">Estado</label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="is_active" 
                                   id="is_active"
                                   value="1" 
                                   {{ old('is_active', $response->is_active ?? true) ? 'checked' : '' }} />
                            <label class="form-check-label" for="is_active">
                                Respuesta Activa
                            </label>
                        </div>
                        <div class="form-text">Inactiva = No aparecerá en búsquedas</div>
                    </div>
                    <!--end::Input group-->
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('tickets.responses.index') }}" class="btn btn-light me-3">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    {!! getIcon('check', 'fs-2') !!}
                    {{ isset($response) ? 'Actualizar' : 'Crear' }} Respuesta
                </button>
            </div>
        </form>
    </div>
</x-default-layout>

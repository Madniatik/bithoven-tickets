<x-default-layout>
    @section('title', 'Editar Regla de Automatización')
    @section('breadcrumbs')
        {{ Breadcrumbs::render('tickets.automation.edit', $automation) }}
    @endsection

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Editar: {{ $automation->name }}</h3>
        </div>

        <form action="{{ route('tickets.automation.update', $automation->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card-body">
                <!--begin::Basic Info-->
                <div class="mb-10">
                    <h3 class="mb-5">Información Básica</h3>
                    
                    <div class="row mb-7">
                        <div class="col-md-8">
                            <label class="form-label required">Nombre de la Regla</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $automation->name) }}" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Orden de Ejecución</label>
                            <input type="number" name="execution_order" class="form-control @error('execution_order') is-invalid @enderror" 
                                   value="{{ old('execution_order', $automation->execution_order) }}" min="0" />
                            @error('execution_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Menor número = mayor prioridad</div>
                        </div>
                    </div>

                    <div class="row mb-7">
                        <div class="col-md-12">
                            <label class="form-label required">Tipo de Regla</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="auto_close" {{ old('type', $automation->type) == 'auto_close' ? 'selected' : '' }}>Auto-Close</option>
                                <option value="auto_escalate" {{ old('type', $automation->type) == 'auto_escalate' ? 'selected' : '' }}>Auto-Escalate</option>
                                <option value="auto_assign" {{ old('type', $automation->type) == 'auto_assign' ? 'selected' : '' }}>Auto-Assign</option>
                                <option value="auto_response" {{ old('type', $automation->type) == 'auto_response' ? 'selected' : '' }}>Auto-Response</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-7">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="3">{{ old('description', $automation->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check form-switch form-check-custom form-check-solid mb-5">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                               {{ old('is_active', $automation->is_active) ? 'checked' : '' }} id="is_active" />
                        <label class="form-check-label" for="is_active">
                            Regla Activa
                        </label>
                    </div>

                    <!--begin::Stats-->
                    <div class="bg-light-info rounded border-info border border-dashed p-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fw-bold text-gray-700 mb-2">Ejecuciones Totales:</div>
                                <div class="fs-3 fw-bold text-gray-900">{{ number_format($automation->execution_count) }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold text-gray-700 mb-2">Última Ejecución:</div>
                                <div class="fs-6 text-gray-800">
                                    @if($automation->last_executed_at)
                                        {{ $automation->last_executed_at->format('d/m/Y H:i') }}
                                        <span class="text-muted">({{ $automation->last_executed_at->diffForHumans() }})</span>
                                    @else
                                        <span class="text-muted">Nunca</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Stats-->
                </div>
                <!--end::Basic Info-->

                <div class="separator separator-dashed my-10"></div>

                <!--begin::Conditions-->
                <div class="mb-10">
                    <h3 class="mb-5">Condiciones (JSON)</h3>
                    
                    <div class="mb-5">
                        <label class="form-label required">Conditions JSON</label>
                        <textarea name="conditions" class="form-control form-control-solid font-monospace @error('conditions') is-invalid @enderror" 
                                  rows="6" required>{{ old('conditions', json_encode($automation->conditions, JSON_PRETTY_PRINT)) }}</textarea>
                        @error('conditions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end::Conditions-->

                <div class="separator separator-dashed my-10"></div>

                <!--begin::Actions-->
                <div class="mb-10">
                    <h3 class="mb-5">Acciones (JSON)</h3>
                    
                    <div class="mb-5">
                        <label class="form-label required">Actions JSON</label>
                        <textarea name="actions" class="form-control form-control-solid font-monospace @error('actions') is-invalid @enderror" 
                                  rows="6" required>{{ old('actions', json_encode($automation->actions, JSON_PRETTY_PRINT)) }}</textarea>
                        @error('actions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end::Actions-->

                <div class="separator separator-dashed my-10"></div>

                <!--begin::Config-->
                <div class="mb-10">
                    <h3 class="mb-5">Configuración Adicional (JSON)</h3>
                    
                    <div class="mb-5">
                        <label class="form-label">Config JSON</label>
                        <textarea name="config" class="form-control form-control-solid font-monospace @error('config') is-invalid @enderror" 
                                  rows="4">{{ old('config', json_encode($automation->config ?: [], JSON_PRETTY_PRINT)) }}</textarea>
                        @error('config')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end::Config-->
            </div>

            <div class="card-footer d-flex justify-content-between py-6">
                <a href="{{ route('tickets.automation.logs', $automation->id) }}" class="btn btn-light-info">
                    {!! getIcon('chart-simple', 'fs-2') !!}
                    Ver Logs
                </a>
                <div>
                    <a href="{{ route('tickets.automation.index') }}" class="btn btn-light me-3">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        {!! getIcon('check', 'fs-2') !!}
                        Actualizar Regla
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // JSON validation on submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const conditions = document.querySelector('[name="conditions"]').value;
            const actions = document.querySelector('[name="actions"]').value;
            const config = document.querySelector('[name="config"]').value;
            
            try {
                JSON.parse(conditions);
                JSON.parse(actions);
                if (config.trim()) {
                    JSON.parse(config);
                }
            } catch (error) {
                e.preventDefault();
                toastr.error('JSON inválido: ' + error.message);
                return false;
            }
        });
    </script>
    @endpush
</x-default-layout>

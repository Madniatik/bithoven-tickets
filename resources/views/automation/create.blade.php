<x-default-layout>
    @section('title', 'Crear Regla de Automatización')
    @section('breadcrumbs')
        {{ Breadcrumbs::render('tickets.automation.create') }}
    @endsection

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Nueva Regla de Automatización</h3>
        </div>

        <form action="{{ route('tickets.automation.store') }}" method="POST">
            @csrf
            
            <div class="card-body">
                <!--begin::Alert-->
                <div class="alert alert-info d-flex align-items-center p-5 mb-10">
                    {!! getIcon('information-5', 'fs-2hx text-info me-4') !!}
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-dark">Reglas de Automatización</h4>
                        <span>Las reglas se procesan automáticamente según el orden de ejecución. Usa formato JSON válido para conditions, actions y config.</span>
                    </div>
                </div>
                <!--end::Alert-->

                <!--begin::Basic Info-->
                <div class="mb-10">
                    <h3 class="mb-5">Información Básica</h3>
                    
                    <div class="row mb-7">
                        <div class="col-md-8">
                            <label class="form-label required">Nombre de la Regla</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="Ej: Auto-Close Resolved (7 days)" value="{{ old('name') }}" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Orden de Ejecución</label>
                            <input type="number" name="execution_order" class="form-control @error('execution_order') is-invalid @enderror" 
                                   placeholder="0" value="{{ old('execution_order', 0) }}" min="0" />
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
                                <option value="">Seleccionar tipo...</option>
                                <option value="auto_close" {{ old('type') == 'auto_close' ? 'selected' : '' }}>Auto-Close</option>
                                <option value="auto_escalate" {{ old('type') == 'auto_escalate' ? 'selected' : '' }}>Auto-Escalate</option>
                                <option value="auto_assign" {{ old('type') == 'auto_assign' ? 'selected' : '' }}>Auto-Assign</option>
                                <option value="auto_response" {{ old('type') == 'auto_response' ? 'selected' : '' }}>Auto-Response</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-7">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" placeholder="Descripción de la regla y su propósito...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check form-switch form-check-custom form-check-solid mb-5">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }} id="is_active" />
                        <label class="form-check-label" for="is_active">
                            Regla Activa
                        </label>
                    </div>
                </div>
                <!--end::Basic Info-->

                <div class="separator separator-dashed my-10"></div>

                <!--begin::Conditions-->
                <div class="mb-10">
                    <h3 class="mb-5">Condiciones (JSON)</h3>
                    <p class="text-muted mb-5">Define las condiciones que debe cumplir un ticket para aplicar esta regla.</p>
                    
                    <div class="mb-5">
                        <label class="form-label required">Conditions JSON</label>
                        <textarea name="conditions" class="form-control form-control-solid font-monospace @error('conditions') is-invalid @enderror" 
                                  rows="6" required>{{ old('conditions', '{}') }}</textarea>
                        @error('conditions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Ejemplo: <code>{"status": "resolved", "inactive_hours": 168}</code>
                        </div>
                    </div>
                </div>
                <!--end::Conditions-->

                <div class="separator separator-dashed my-10"></div>

                <!--begin::Actions-->
                <div class="mb-10">
                    <h3 class="mb-5">Acciones (JSON)</h3>
                    <p class="text-muted mb-5">Define las acciones que se ejecutarán cuando se cumplan las condiciones.</p>
                    
                    <div class="mb-5">
                        <label class="form-label required">Actions JSON</label>
                        <textarea name="actions" class="form-control form-control-solid font-monospace @error('actions') is-invalid @enderror" 
                                  rows="6" required>{{ old('actions', '{}') }}</textarea>
                        @error('actions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Ejemplo: <code>{"close": true, "add_comment": "Ticket cerrado automáticamente"}</code>
                        </div>
                    </div>
                </div>
                <!--end::Actions-->

                <div class="separator separator-dashed my-10"></div>

                <!--begin::Config-->
                <div class="mb-10">
                    <h3 class="mb-5">Configuración Adicional (JSON) <span class="text-muted fs-7">(Opcional)</span></h3>
                    
                    <div class="mb-5">
                        <label class="form-label">Config JSON</label>
                        <textarea name="config" class="form-control form-control-solid font-monospace @error('config') is-invalid @enderror" 
                                  rows="4">{{ old('config', '{}') }}</textarea>
                        @error('config')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Ejemplo: <code>{"notify_user": true, "notify_supervisor": false}</code>
                        </div>
                    </div>
                </div>
                <!--end::Config-->
            </div>

            <div class="card-footer d-flex justify-content-end py-6">
                <a href="{{ route('tickets.automation.index') }}" class="btn btn-light me-3">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    {!! getIcon('check', 'fs-2') !!}
                    Crear Regla
                </button>
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

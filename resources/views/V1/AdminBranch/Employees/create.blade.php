@extends('adminlte::page')
@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap">
    <link rel="stylesheet" href="{{ asset('css/employee-form.css') }}?v={{ filemtime(public_path('css/employee-form.css')) }}">
@stop

@section('title', 'Empleados')

@section('content_header')
    <h1>Crear empleado</h1>
@stop

@section('content')

    <div class="emp-form-page">
        <div class="emp-card">
            <div class="emp-card-body">
                <form action="{{ route('admin.sucursal.employees.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($back_url)
                        <input type="hidden" name="back_url" value="{{ $back_url }}">
                    @endif

                    {{-- Foto de perfil --}}
                    <div class="emp-photo-section">
                        <div class="emp-photo-preview" id="photoPreviewBox">
                            <span class="material-symbols-outlined" style="font-size:48px;">person</span>
                        </div>
                        <div class="emp-photo-info">
                            <h3>Foto de perfil</h3>
                            <p>Sube una foto profesional para la ficha del empleado.</p>
                            <label class="emp-btn-outline" for="photo-upload">
                                <span class="material-symbols-outlined" style="font-size:18px;">upload</span>
                                Cargar foto
                            </label>
                            <input type="file" id="photo-upload" name="photo" accept="image/*" class="d-none">
                            @error('photo')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="emp-divider"></div>

                    {{-- Datos básicos --}}
                    <div class="emp-section">
                        <div class="emp-grid emp-grid-3">
                            <x-input-custom name="identification_number" label="Cédula" placeholder="" no-margin-top
                                value="{{ old('identification_number') }}" />

                            <x-input-custom name="first_name" label="Nombre" placeholder="" no-margin-top
                                value="{{ old('first_name') }}" />

                            <x-input-custom name="last_name" label="Apellido" placeholder="" no-margin-top
                                value="{{ old('last_name') }}" />

                            <x-input-custom name="phone_number" label="Teléfono" placeholder="" no-margin-top
                                value="{{ old('phone_number') }}" />

                            <div>
                                <x-label value="Cargo" />
                                {{ Form::select('cargo_id', $cargos, null, ['class' => 'select2 form-control']) }}
                            </div>

                            <div>
                                <x-label value="Proyecto" />
                                {{ Form::select('project_id', $projects, null, ['class' => 'select2 form-control']) }}
                            </div>

                            <x-input-date-custom name="hire_date" label="Fecha de ingreso" placeholder="" no-margin-top
                                value="{{ old('hire_date') }}" />

                            <div>
                                <x-label value="Tipo de contrato" />
                                {{ Form::select('contract_type_id', $contractTypes, null, ['class' => 'select2 form-control']) }}
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="address">Dirección (opcional)</label>
                            <textarea name="address" id="address" class="form-control" style="height:5rem;resize:none;">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <div class="emp-divider"></div>

                    {{-- Ficha de ingreso --}}
                    <div class="emp-section">
                        <div class="emp-grid emp-grid-4">
                            <x-input-date-custom name="birth_date" label="Fecha de nacimiento" placeholder="" no-margin-top
                                value="{{ old('birth_date') }}" />

                            <x-input-custom name="nationality" label="Nacionalidad" placeholder="Venezolana" no-margin-top
                                value="{{ old('nationality') }}" />

                            <div class="emp-span-2">
                                <x-label value="¿Posee certificado ocupacional vigente?" />
                                {{ Form::select('has_occupational_certificate', [0 => 'No', 1 => 'Si'], old('has_occupational_certificate', 0), ['class' => 'form-control']) }}
                            </div>

                            <div>
                                <x-label value="¿Posee licencia de conducir?" />
                                {{ Form::select('has_driver_license', [0 => 'No', 1 => 'Si'], old('has_driver_license', 0), ['class' => 'form-control']) }}
                            </div>

                            <div>
                                <x-label value="Grado de licencia" />
                                {{ Form::select('driver_license_grade', ['' => 'Seleccione...', '2do' => '2do', '3ero' => '3ero', '4to' => '4to', '5to' => '5to'], old('driver_license_grade'), ['class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="emp-grid emp-grid-3 mt-3">
                            <x-input-custom name="shirt_size" label="Talla de camisa" placeholder="" no-margin-top
                                value="{{ old('shirt_size') }}" />

                            <x-input-custom name="coverall_size" label="Talla de braga" placeholder="" no-margin-top
                                value="{{ old('coverall_size') }}" />

                            <x-input-custom name="shoe_size" label="Talla de calzado" placeholder="" no-margin-top
                                value="{{ old('shoe_size') }}" />
                        </div>

                        <div class="emp-grid emp-grid-3 mt-3">
                            <x-input-custom name="account_number" label="Número de cuenta" placeholder="20 dígitos" no-margin-top
                                value="{{ old('account_number') }}" />

                            <div>
                                <x-label value="Tipo de cuenta" />
                                {{ Form::select('account_type', ['' => 'Seleccione...', 'ahorro' => 'Ahorro', 'corriente' => 'Corriente'], old('account_type'), ['class' => 'form-control']) }}
                            </div>

                            <x-input-custom name="bank" label="Banco" placeholder="" no-margin-top
                                value="{{ old('bank') }}" />

                            <div class="emp-span-2">
                                <x-input-custom name="emergency_contact_name" label="En caso de accidente comunicarse con"
                                    placeholder="" no-margin-top value="{{ old('emergency_contact_name') }}" />
                            </div>

                            <x-input-custom name="emergency_contact_phone" label="Teléfono de contacto" placeholder="" no-margin-top
                                value="{{ old('emergency_contact_phone') }}" />
                        </div>
                    </div>

                    <div class="emp-divider"></div>

                    {{-- Usuario de sistema --}}
                    <div class="emp-section">
                        <div class="emp-section-title">
                            <span class="material-symbols-outlined">admin_panel_settings</span>
                            <h4 class="m-0">Usuario de sistema (opcional)</h4>
                        </div>

                        <div class="emp-grid emp-grid-3">
                            <x-input-custom name="email" type="email" id="employee_email" label="Email" placeholder=""
                                no-margin-top value="{{ old('email') }}"
                                help="Si agrega un email, también debe agregar una contraseña y seleccionar un rol." />

                            <div>
                                <x-label value="Rol de sistema" />
                                {{ Form::select('role_id', $roles, null, ['class' => 'form-control', 'id' => 'role_id_select']) }}
                            </div>

                            <x-input-custom name="password" type="password" id="password_input" label="Contraseña"
                                placeholder="" no-margin-top value="" autocomplete="new-password" />
                        </div>
                    </div>

                    <div class="emp-actions">
                        <a href="{{ request()->back_url ?? route('admin.sucursal.employees.index') }}"
                            class="emp-btn-outline">Cancelar</a>
                        <button type="submit" class="emp-btn-primary">
                            <span class="material-symbols-outlined" style="font-size:18px;">save</span>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop

@section('js')
    <script>
        const photoInput = document.getElementById('photo-upload');
        const photoPreviewBox = document.getElementById('photoPreviewBox');

        photoInput?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                photoPreviewBox.innerHTML = '<img src="' + ev.target.result + '" alt="Foto de perfil">';
            };
            reader.readAsDataURL(file);
        });
    </script>
@stop

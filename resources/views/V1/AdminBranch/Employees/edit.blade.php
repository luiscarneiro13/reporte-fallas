@extends('adminlte::page')
@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap">
    <link rel="stylesheet" href="{{ asset('css/employee-form.css') }}?v={{ filemtime(public_path('css/employee-form.css')) }}">
@stop

@section('title', 'Empleados')

@section('content_header')
    <h1>Editar Empleado</h1>
    <small>{{ $employee->first_name }} {{ $employee->last_name }}</small>
@stop

@section('content')

    @php
        $ficha = $employee->fichaIngreso;
    @endphp

    <div class="emp-form-page">
        <div class="emp-card">
            <div class="emp-card-body">
                <form action="{{ route('admin.sucursal.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $employee->id }}">

                    {{-- Foto de perfil --}}
                    <div class="emp-photo-section">
                        <div class="emp-photo-col">
                            <div class="emp-photo-preview" id="photoPreviewBox">
                                @if ($ficha?->photo)
                                    <img src="{{ asset('storage/' . $ficha->photo) }}" alt="Foto de perfil">
                                @else
                                    <span class="material-symbols-outlined" style="font-size:48px;">person</span>
                                @endif
                            </div>
                            <a href="{{ $ficha?->photo ? asset('storage/' . $ficha->photo) : '#' }}" target="_blank"
                                rel="noopener" id="viewPhotoBtn" class="emp-btn-outline emp-btn-block"
                                style="{{ $ficha?->photo ? '' : 'display:none;' }}">
                                <span class="material-symbols-outlined" style="font-size:18px;">visibility</span>
                                Ver foto
                            </a>
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
                        <div class="emp-photo-actions">
                            <a href="{{ route('admin.sucursal.employees.excel', $employee) }}" class="btn btn-success" title="Exportar datos del empleado a Excel">
                                <i class="fas fa-file-excel">&nbsp;</i>
                                Exportar datos
                            </a>
                        </div>
                    </div>

                    <div class="emp-divider"></div>

                    {{-- Datos básicos --}}
                    <div class="emp-section">
                        <div class="emp-grid emp-grid-3">
                            <x-input-custom name="identification_number" label="Cédula" placeholder="" no-margin-top
                                value="{{ $employee->identification_number }}" />

                            <x-input-custom name="first_name" label="Nombre" placeholder="" no-margin-top
                                value="{{ $employee->first_name }}" />

                            <x-input-custom name="last_name" label="Apellido" placeholder="" no-margin-top
                                value="{{ $employee->last_name }}" />

                            <x-input-custom name="phone_number" label="Teléfono" placeholder="" no-margin-top
                                value="{{ $employee->phone_number }}" />

                            <div>
                                <x-label value="Cargo"
                                    btnAddUrl="{{ route('admin.sucursal.cargos.create', ['back_url' => url()->full()]) }}" />
                                {{ Form::select('cargo_id', $cargos, $employee->cargo_id ?? '0', ['class' => 'select2 form-control']) }}
                            </div>

                            <div>
                                <x-label value="Proyecto"
                                    btnAddUrl="{{ route('admin.sucursal.projects.create', ['back_url' => url()->full()]) }}" />
                                {{ Form::select('project_id', $projects, $employee->projects->first()->id ?? '0', ['class' => 'select2 form-control']) }}
                            </div>

                            <x-input-date-custom name="hire_date" label="Fecha de ingreso" placeholder="" no-margin-top
                                value="{{ $employee->hire_date }}" />

                            <div>
                                <x-label value="Tipo de contrato"
                                    btnAddUrl="{{ route('admin.sucursal.contract.types.create', ['back_url' => url()->full()]) }}" />
                                {{ Form::select('contract_type_id', $contractTypes, $employee->contract_type_id ?? '0', ['class' => 'select2 form-control']) }}
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="address">Dirección (opcional)</label>
                            <textarea name="address" id="address" class="form-control" style="height:5rem;resize:none;">{{ $employee->address }}</textarea>
                        </div>
                    </div>

                    <div class="emp-divider"></div>

                    {{-- Ficha de ingreso --}}
                    <div class="emp-section">
                        <div class="emp-grid emp-grid-4">
                            <x-input-date-custom name="birth_date" label="Fecha de nacimiento" placeholder="" no-margin-top
                                value="{{ $ficha?->birth_date }}" />

                            <x-input-custom name="nationality" label="Nacionalidad" placeholder="Venezolana" no-margin-top
                                value="{{ $ficha?->nationality }}" />

                            <div class="emp-span-2">
                                <x-label value="¿Posee certificado ocupacional vigente?" />
                                {{ Form::select('has_occupational_certificate', [0 => 'No', 1 => 'Si'], (int) ($ficha?->has_occupational_certificate ?? 0), ['class' => 'form-control', 'id' => 'has_occupational_certificate_select']) }}
                            </div>

                            <div id="occupationalCertificateExpirationField">
                                <x-input-date-custom name="occupational_certificate_expiration_date"
                                    label="Vencimiento del certificado" placeholder="" no-margin-top
                                    value="{{ old('occupational_certificate_expiration_date', $ficha?->occupational_certificate_expiration_date) }}" />
                            </div>

                            <div>
                                <x-label value="¿Posee licencia de conducir?" />
                                {{ Form::select('has_driver_license', [0 => 'No', 1 => 'Si'], (int) ($ficha?->has_driver_license ?? 0), ['class' => 'form-control', 'id' => 'has_driver_license_select']) }}
                            </div>

                            <div id="driverLicenseGradeField">
                                <x-label value="Grado de licencia" />
                                {{ Form::select('driver_license_grade', ['' => 'Seleccione...', '2do' => '2do', '3ero' => '3ero', '4to' => '4to', '5to' => '5to'], old('driver_license_grade', $ficha?->driver_license_grade), ['class' => 'form-control']) }}
                            </div>

                            <div id="driverLicenseExpirationField">
                                <x-input-date-custom name="driver_license_expiration_date" label="Vencimiento de licencia"
                                    placeholder="" no-margin-top
                                    value="{{ old('driver_license_expiration_date', $ficha?->driver_license_expiration_date) }}" />
                            </div>
                        </div>

                        <div class="emp-grid emp-grid-3 mt-3">
                            <x-input-custom name="shirt_size" label="Talla de camisa" placeholder="" no-margin-top
                                value="{{ $ficha?->shirt_size }}" />

                            <x-input-custom name="coverall_size" label="Talla de braga" placeholder="" no-margin-top
                                value="{{ $ficha?->coverall_size }}" />

                            <x-input-custom name="shoe_size" label="Talla de calzado" placeholder="" no-margin-top
                                value="{{ $ficha?->shoe_size }}" />
                        </div>

                        <div class="emp-grid emp-grid-3 mt-3">
                            <x-input-custom name="account_number" label="Número de cuenta" placeholder="20 dígitos" no-margin-top
                                value="{{ $ficha?->account_number }}" />

                            <div>
                                <x-label value="Tipo de cuenta" />
                                {{ Form::select('account_type', ['' => 'Seleccione...', 'ahorro' => 'Ahorro', 'corriente' => 'Corriente'], $ficha?->account_type, ['class' => 'form-control']) }}
                            </div>

                            <x-input-custom name="bank" label="Banco" placeholder="" no-margin-top
                                value="{{ $ficha?->bank }}" />

                            <div class="emp-span-2">
                                <x-input-custom name="emergency_contact_name" label="En caso de accidente comunicarse con"
                                    placeholder="" no-margin-top value="{{ $ficha?->emergency_contact_name }}" />
                            </div>

                            <x-input-custom name="emergency_contact_phone" label="Teléfono de contacto" placeholder="" no-margin-top
                                value="{{ $ficha?->emergency_contact_phone }}" />
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
                            <x-input-custom name="email" type="email" id="employee_email" label="Email (opcional)"
                                placeholder="" no-margin-top value="{{ $userSystem->email ?? '' }}"
                                help="Si agrega un email, también debe agregar una contraseña y seleccionar un rol." />

                            <div>
                                <x-label value="Rol de sistema" />
                                {{ Form::select('role_id', $roles, $userSystem?->roles->first()?->id ?? '', ['class' => 'form-control']) }}
                            </div>

                            <x-input-custom name="password" type="password" id="password_input" label="Contraseña"
                                placeholder="" no-margin-top value=""
                                help="Si no quiere modificar la clave, entonces la puede dejar vacía" />
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

        <div style="max-width:1024px;margin:1.5rem auto 0;">
            <div class="emp-section-title">
                <span class="material-symbols-outlined">work_history</span>
                <h4 class="m-0">Períodos de empleo</h4>
            </div>

            <p class="text-muted" style="margin-top:-0.5rem;">
                Registra cada ingreso y egreso del empleado en la empresa (permite reingresos).
            </p>

            <div class="emp-incidents-table">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-2">
                            <a href="{{ route('admin.sucursal.employee.periods.create', ['employee_id' => $employee->id, 'back_url' => route('admin.sucursal.employees.edit', $employee)]) }}"
                                class="btn btn-sm btn-primary">
                                <i class="fas fa-plus">&nbsp;</i>
                                Nuevo período
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Ingreso</th>
                                        <th>Egreso</th>
                                        <th>Estado</th>
                                        <th>Cargo</th>
                                        <th>Tipo de contrato</th>
                                        <th>Motivo de egreso</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($employmentPeriods as $item)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($item->start_date)->format('d-m-Y') }}</td>
                                            <td>{{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('d-m-Y') : '-' }}</td>
                                            <td>
                                                @if ($item->end_date)
                                                    <span class="badge badge-secondary">Finalizado</span>
                                                @else
                                                    <span class="badge badge-success">Activo</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->cargo?->name }}</td>
                                            <td>{{ $item->contractType?->name }}</td>
                                            <td>{{ $item->termination_reason }}</td>
                                            <td>
                                                <div class="input-group" style="cursor:pointer;">
                                                    <div>
                                                        <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                                        <div class="dropdown-menu">

                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.sucursal.employee.periods.edit', [$item, 'back_url' => route('admin.sucursal.employees.edit', $employee)]) }}">
                                                                <i class="fa fa-edit">&nbsp;</i>
                                                                Editar
                                                            </a>

                                                            <div class="dropdown-divider"></div>
                                                            <form class="formEliminar"
                                                                action="{{ route('admin.sucursal.employee.periods.destroy', $item) }}"
                                                                method="post">
                                                                @csrf
                                                                @method('delete')
                                                                <input type="hidden" name="back_url" value="{{ route('admin.sucursal.employees.edit', $employee) }}">
                                                                <button class="dropdown-item" type="submit">
                                                                    <i class="fa fa-trash">&nbsp;</i>
                                                                    Eliminar
                                                                </button>
                                                            </form>

                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Sin períodos registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="max-width:1024px;margin:1.5rem auto 0;">
            <div class="emp-section-title">
                <span class="material-symbols-outlined">warning</span>
                <h4 class="m-0">Incidencias</h4>
            </div>

            @php
                $incidentHeaders = [
                    ['label' => 'Fecha', 'field' => null],
                    ['label' => 'Descripción', 'field' => null],
                    ['label' => 'Reportado por', 'field' => null],
                    ['label' => '', 'field' => null],
                ];
            @endphp

            <div class="emp-incidents-table">
            <x-base-data-table-search title="Incidencias" :items="$incidents" :headers="$incidentHeaders"
                :urlBtnAdd="route('admin.sucursal.employee.incidents.create', ['employee_id' => $employee->id, 'back_url' => route('admin.sucursal.employees.edit', $employee)])"
                :urlExcel="route('admin.sucursal.employees.incidents.excel', $employee)" titleExcel="Exportar incidencias a Excel">
                <x-slot name="body">
                    @forelse ($incidents as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->reportedBy?->name }}</td>
                            <td>
                                <div class="input-group" style="cursor:pointer;">
                                    <div>
                                        <a class="dropdown-toggle btn-sm btn-dark" data-toggle="dropdown"></a>
                                        <div class="dropdown-menu">

                                            <a class="dropdown-item"
                                                href="{{ route('admin.sucursal.employee.incidents.edit', [$item, 'back_url' => route('admin.sucursal.employees.edit', $employee)]) }}">
                                                <i class="fa fa-edit">&nbsp;</i>
                                                Editar
                                            </a>

                                            <div class="dropdown-divider"></div>
                                            <form class="formEliminar"
                                                action="{{ route('admin.sucursal.employee.incidents.destroy', $item) }}"
                                                method="post">
                                                @csrf
                                                @method('delete')
                                                <input type="hidden" name="back_url" value="{{ route('admin.sucursal.employees.edit', $employee) }}">
                                                <button class="dropdown-item" type="submit">
                                                    <i class="fa fa-trash">&nbsp;</i>
                                                    Eliminar
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </x-slot>
            </x-base-data-table-search>
            </div>
        </div>
    </div>

    <div id="addCustomer"></div>
    <div id="addDivision"></div>

@stop

@section('js')
    <script>
        const photoInput = document.getElementById('photo-upload');
        const photoPreviewBox = document.getElementById('photoPreviewBox');

        const viewPhotoBtn = document.getElementById('viewPhotoBtn');

        photoInput?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                photoPreviewBox.innerHTML = '<img src="' + ev.target.result + '" alt="Foto de perfil">';
                if (viewPhotoBtn) {
                    viewPhotoBtn.href = ev.target.result;
                    viewPhotoBtn.style.display = '';
                }
            };
            reader.readAsDataURL(file);
        });

        function toggleField(selectEl, fieldEl) {
            if (!selectEl || !fieldEl) return;
            fieldEl.style.display = selectEl.value === '1' ? '' : 'none';
        }

        const hasDriverLicenseSelect = document.getElementById('has_driver_license_select');
        const driverLicenseGradeField = document.getElementById('driverLicenseGradeField');
        const driverLicenseExpirationField = document.getElementById('driverLicenseExpirationField');

        const hasOccupationalCertificateSelect = document.getElementById('has_occupational_certificate_select');
        const occupationalCertificateExpirationField = document.getElementById('occupationalCertificateExpirationField');

        function toggleDriverLicenseFields() {
            toggleField(hasDriverLicenseSelect, driverLicenseGradeField);
            toggleField(hasDriverLicenseSelect, driverLicenseExpirationField);
        }

        function toggleOccupationalCertificateFields() {
            toggleField(hasOccupationalCertificateSelect, occupationalCertificateExpirationField);
        }

        hasDriverLicenseSelect?.addEventListener('change', toggleDriverLicenseFields);
        hasOccupationalCertificateSelect?.addEventListener('change', toggleOccupationalCertificateFields);

        toggleDriverLicenseFields();
        toggleOccupationalCertificateFields();
    </script>
@stop

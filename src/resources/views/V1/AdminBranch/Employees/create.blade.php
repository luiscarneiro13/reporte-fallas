@extends('adminlte::page')

@section('title', 'Empleados')

@section('content_header')
    <h1>Crear empleado</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.employees.store') }}" method="POST">
                @csrf
                @if ($back_url)
                    <input type="hidden" name="back_url" value="{{ $back_url }}">
                @endif

                <h5 class="mb-3">Datos básicos</h5>

                <div class="row">

                    {{-- Cédula, Nombre, Apellido: AHORA REQUERIDOS --}}
                    <x-adminlte-input name="identification_number" label="Cédula" placeholder="" fgroup-class="col-md-4"
                        value="{{ old('identification_number') }}" />

                    <x-adminlte-input name="first_name" label="Nombre" placeholder="" fgroup-class="col-md-4"
                        value="{{ old('first_name') }}" />

                    <x-adminlte-input name="last_name" label="Apellido" placeholder="" fgroup-class="col-md-4"
                        value="{{ old('last_name') }}" />

                    {{-- Teléfono y Dirección: NO REQUERIDOS --}}
                    <x-adminlte-input name="phone_number" label="Teléfono" placeholder="" fgroup-class="col-md-4"
                        value="{{ old('phone_number') }}" />

                    <x-adminlte-input name="address" label="Dirección (opcional)" placeholder="" fgroup-class="col-md-8"
                        value="{{ old('address') }}" />
                </div>

                <hr>

                <h5 class="mb-3">Usuario de sistema (opcional)</h5>

                <div class="row">

                    <x-input-custom name="email" type="email" id="employee_email" label="Email"
                        placeholder="" class="col-md-4" value="{{ old('email') }}"
                        help="Si agrega un email, también debe agregar una contraseña y seleccionar un rol." />

                    <div class="col-md-4">
                        <x-label value="Rol de sistema" />
                        {{ Form::select('role_id', $roles, null, ['class' => 'form-control', 'id' => 'role_id_select']) }}
                    </div>

                    <x-adminlte-input type="password" name="password" id="password_input" label="Contraseña temporal"
                        placeholder="" fgroup-class="col-md-4" autocomplete="new-password" />
                </div>

                <div class="row mt-5">
                    <a href="{{ request()->back_url ?? route('admin.sucursal.employees.index') }}"
                        class="btn-sm mr-3 btn-default" type="submit" icon="fas fa-lg fa-save">Cancelar</a>
                    <x-adminlte-button class="btn-sm" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </div>
            </form>
        </div>
    </div>

@stop

@section('js')
    <script></script>
@stop

@extends('adminlte::page')

@section('title', 'Proyectos')

@section('content_header')
    <h1>Editar Empleado</h1>
    <small>{{ $employee->name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.employees.update', $employee) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $employee->id }}">

                <h5 class="mb-3">Datos básicos</h5>

                <div class="row">

                    {{-- Cédula, Nombre, Apellido: AHORA REQUERIDOS --}}
                    <x-adminlte-input name="identification_number" label="Cédula" placeholder="" fgroup-class="col-md-4"
                        value="{{ $employee->identification_number }}" />

                    <x-adminlte-input name="first_name" label="Nombre" placeholder="" fgroup-class="col-md-4"
                        value="{{ $employee->first_name }}" />

                    <x-adminlte-input name="last_name" label="Apellido" placeholder="" fgroup-class="col-md-4"
                        value="{{ $employee->last_name }}" />

                    {{-- Teléfono y Dirección: NO REQUERIDOS --}}
                    <x-adminlte-input name="phone_number" label="Teléfono" placeholder="" fgroup-class="col-md-4"
                        value="{{ $employee->phone_number }}" />

                    <x-adminlte-input name="address" label="Dirección (opcional)" placeholder="" fgroup-class="col-md-8"
                        value="{{ $employee->address }}" />
                </div>

                <hr>

                <h5 class="mb-3">Usuario de sistema (opcional)</h5>

                <div class="row">

                    <x-input-custom name="email" type="email" id="employee_email" label="Email (opcional)"
                        placeholder="" class="col-md-4" value="{{ old('email') }}"
                        help="Si agrega un email, también debe agregar una contraseña y seleccionar un rol." />

                    <x-select label="Rol de sistema" name="role_id" :items="$roles" class="col-md-4" />

                    <x-adminlte-input name="text" id="password_input" label="Contraseña temporal"
                        placeholder="" fgroup-class="col-md-4" autocomplete="new-password" value="{{ old('password') }}" />
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

    <div id="addCustomer"></div>
    <div id="addDivision"></div>

@stop


@section('js')
    <script></script>
@stop

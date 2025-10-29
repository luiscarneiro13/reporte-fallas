@extends('adminlte::page')

@section('title', 'Administradores')

@section('content_header')
    <h1>Nuevo administrador</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.usuarios.administradores.store') }}" method="POST">
                @csrf
                <div class="row">
                    <x-adminlte-input name="name" label="Nombre completo" placeholder="Aquí el nombre completo" fgroup-class="col-md-4"
                        value="{{ old('name') }}" />
                    <x-adminlte-input type="email" name="email" label="Email" placeholder="Aquí el email"
                        fgroup-class="col-md-4" value="{{ old('email') }}" />
                    <x-adminlte-input name="phone" label="Teléfono" placeholder="Aquí el teléfono"
                        fgroup-class="col-md-4" value="{{ old('phone') }}" />
                    <x-adminlte-input type="password" name="password" label="Contraseña" placeholder="Aquí la contraseña"
                        fgroup-class="col-md-6" value="{{ old('password') }}" />
                    <x-adminlte-input type="password" name="password_confirmation" label="Repita la contraseña"
                        placeholder="Aquí repita la contraseña" fgroup-class="col-md-6"
                        value="{{ old('password_confirmation') }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.usuarios.administradores.index') }}" class="btn-sm mr-3 btn-default" type="submit"
                        icon="fas fa-lg fa-save">Cancelar</a>
                    <x-adminlte-button class="btn-sm" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </div>
            </form>
        </div>
    </div>

@stop


@section('js')
    <script>
        console.log('Hi!');
    </script>
@stop

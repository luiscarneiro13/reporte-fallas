@extends('adminlte::page')

@section('title', 'Operadores')

@section('content_header')
    <h1>Editar operador</h1>
    <small>{{ $operator->name }}</small>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.usuarios.operators.update', $operator) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <input type="hidden" name="id" value="{{ $operator->id }}">
                    <x-adminlte-input name="name" label="Nombre completo" placeholder="Aquí el nombre completo"
                        fgroup-class="col-md-4" value="{{ $operator->name }}" />
                    <x-adminlte-input type="email" name="email" label="Email" placeholder="Aquí el email"
                        fgroup-class="col-md-4" value="{{ $operator->email }}" />
                    <x-adminlte-input name="phone" label="Teléfono" placeholder="Aquí el teléfono" fgroup-class="col-md-4"
                        value="{{ $operator->phone }}" />
                    <div class="col-md-6">
                        <x-adminlte-input type="password" name="password" label="Contraseña"
                            placeholder="Aquí la contraseña" value="{{ old('password') }}" />
                        <small>Deje la contraseña vacía si no quiere cambiarla</small>
                    </div>
                    <div class="col-md-6">
                        <x-adminlte-input type="password" name="password_confirmation" label="Repita la contraseña"
                            placeholder="Aquí repita la contraseña" value="{{ old('password_confirmation') }}" />
                        <small>Deje la contraseña vacía si no quiere cambiarla</small>
                    </div>
                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.usuarios.operators.index') }}" class="btn-sm mr-3 btn-default" type="submit"
                        icon="fas fa-lg fa-save">Cancelar</a>
                    <x-adminlte-button class="btn-sm" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </div>
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

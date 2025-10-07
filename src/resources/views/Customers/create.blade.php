@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>New Customer</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('customer.store') }}" method="POST">
                @csrf
                <div class="row">
                    <x-adminlte-input name="dni" label="DNI" placeholder="DNI" fgroup-class="col-md-4"
                        value="{{ old('dni') }}" />
                    <x-adminlte-input name="nombre" label="Nombre" placeholder="Nombre" fgroup-class="col-md-4"
                        value="{{ old('nombre') }}" />
                    <x-adminlte-input name="apellido" label="Apellido" placeholder="Apellido" fgroup-class="col-md-4"
                        value="{{ old('apellido') }}" />
                    <x-adminlte-input name="email" label="Email" placeholder="Email" fgroup-class="col-md-4"
                        value="{{ old('email') }}" />
                    <x-adminlte-input name="telefono" label="Teléfono" placeholder="Teléfono" fgroup-class="col-md-4"
                        value="{{ old('telefono') }}" />
                    <x-adminlte-select name="estado" label="Estado Civil" fgroup-class="col-md-4">
                        <option>Casado</option>
                        <option>Soltero</option>
                    </x-adminlte-select>
                    <x-adminlte-textarea name="direccion" label="Dirección" placeholder="Dirección"
                        fgroup-class="col-md-4">{{ old('direccion') }}</x-adminlte-textarea>
                </div>

                <div class="row">
                    <a href="{{ route('customer.index') }}" class="btn-sm mr-3 btn-default" type="submit"
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

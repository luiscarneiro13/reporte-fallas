@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Nuevo servicio</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.services.store') }}" method="POST">
                @csrf
                <div class="row">
                    <x-adminlte-input name="name" label="Nombre" placeholder="Nombre del servicio" fgroup-class="col-md-4"
                        value="{{ old('name') }}" />
                    <x-adminlte-input name="price" label="Precio $" placeholder="Precio" fgroup-class="col-md-4"
                        value="{{ old('name') }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.services.index') }}" class="btn-sm mr-3 btn-default" type="submit"
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

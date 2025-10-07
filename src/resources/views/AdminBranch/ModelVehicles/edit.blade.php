@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Editar Modelo de vehículo </h1>
    <small>{{ $modelVehicle->name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.models.vehicles.update', $modelVehicle) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $modelVehicle->id }}">
                <div class="row">
                    <x-adminlte-input name="name" label="Nombre" placeholder="Ford Explorer 2003" fgroup-class="col-md-4"
                        value="{{ $modelVehicle->name }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.models.vehicles.index') }}" class="btn-sm mr-3 btn-default" type="submit"
                        icon="fas fa-lg fa-save">Cancelar</a>
                    <x-adminlte-button class="btn btn-sm" type="submit" label="Guardar" theme="primary"
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

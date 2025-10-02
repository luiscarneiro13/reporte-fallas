@extends('adminlte::page')

@section('title', 'Editar area de servicio')

@section('content_header')
    <h1>Editar area de servicio</h1>
    <small>{{ $serviceArea->name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.service.areas.update', $serviceArea) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $serviceArea->id }}">
                <div class="row">
                    <x-adminlte-input name="name" label="Nombre" placeholder="Nombre" fgroup-class="col-md-4"
                        value="{{ $serviceArea->name }}" />
                    <x-adminlte-input name="description" label="Descripción" placeholder="Descripción" fgroup-class="col-md-8"
                        value="{{ $serviceArea->description }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.service.areas.index') }}" class="btn-sm mr-3 btn-default" type="submit"
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

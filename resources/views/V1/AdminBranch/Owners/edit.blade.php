@extends('adminlte::page')

@section('title', 'Propietarios')

@section('content_header')
    <h1>Editar Propietario</h1>
    <small>{{ $owner->last_name }} {{ $owner->first_name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.owners.update', $owner) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $owner->id }}">
                <div class="row">
                    <x-adminlte-input name="last_name" label="Apellido" placeholder="Apellido" fgroup-class="col-md-4"
                        value="{{ $owner->last_name }}" />
                    <x-adminlte-input name="first_name" label="Nombre" placeholder="Nombre" fgroup-class="col-md-4"
                        value="{{ $owner->first_name }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.owners.index') }}" class="btn-sm mr-3 btn-default" type="submit"
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

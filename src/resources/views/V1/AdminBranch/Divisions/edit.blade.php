@extends('adminlte::page')

@section('title', 'Divisiones')

@section('content_header')
    <h1>Editar División</h1>
    <small>{{ $division->name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.divisions.update', $division) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $division->id }}">
                <div class="row">
                    <x-adminlte-input name="name" label="Nombre" placeholder="Nombre" fgroup-class="col-md-4"
                        value="{{ $division->name }}" />
                    <x-adminlte-input name="description" label="Descripción" placeholder="Descripción" fgroup-class="col-md-8"
                        value="{{ $division->description }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.divisions.index') }}" class="btn-sm mr-3 btn-default" type="submit"
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

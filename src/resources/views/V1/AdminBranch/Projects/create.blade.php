@extends('adminlte::page')

@section('title', 'Proyectos')

@section('content_header')
    <h1>Crear proyecto</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.projects.store') }}" method="POST">
                @csrf
                @if ($back_url)
                    <input type="hidden" name="back_url" value="{{ $back_url }}">
                @endif
                <div class="row">
                    <x-adminlte-input name="name" label="Nombre" placeholder="Nombre" fgroup-class="col-md-4"
                        value="{{ old('name') }}" />
                    <x-adminlte-input name="description" label="Descripción" placeholder="Descripción" fgroup-class="col-md-8"
                        value="{{ old('description') }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.projects.index') }}" class="btn-sm mr-3 btn-default" type="submit"
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

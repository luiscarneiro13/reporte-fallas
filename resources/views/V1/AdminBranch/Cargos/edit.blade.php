@extends('adminlte::page')

@section('title', 'Cargos')

@section('content_header')
    <h1>Editar cargo</h1>
    <small>{{ $cargo->name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.cargos.update', $cargo) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $cargo->id }}">
                <div class="row">
                    <x-adminlte-input name="name" label="Nombre" placeholder="Nombre" fgroup-class="col-md-4"
                        value="{{ $cargo->name }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.cargos.index') }}" class="btn-sm mr-3 btn-default"
                        type="submit" icon="fas fa-lg fa-save">Cancelar</a>
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

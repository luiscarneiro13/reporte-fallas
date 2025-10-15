@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Editar Marca </h1>
    <small>{{ $brand->name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.brands.update', $brand) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $brand->id }}">
                <div class="row">
                    <x-adminlte-input name="name" label="Nombre" placeholder="Nombre de la Marca" fgroup-class="col-md-4"
                        value="{{ $brand->name }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.brands.index') }}" class="btn-sm mr-3 btn-default" type="submit"
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

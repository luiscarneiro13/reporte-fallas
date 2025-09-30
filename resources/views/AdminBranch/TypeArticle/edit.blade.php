@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Editar descripción de artículo</h1>
    <small>{{ $typeArticle->name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.type.articles.update', $typeArticle) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $typeArticle->id }}">
                <div class="row">
                    <x-adminlte-input name="name" label="Nombre" placeholder="Ej: aceite, filtro, etc" fgroup-class="col-md-4"
                        value="{{ $typeArticle->name }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.type.articles.index') }}" class="btn-sm mr-3 btn-default"
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

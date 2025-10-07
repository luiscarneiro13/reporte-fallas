@extends('adminlte::page')

@section('title', 'Propietarios')
@section('content_header')
    <h1>Propietarios</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.owners.store') }}" method="POST">
                @csrf
                @if ($back_url)
                    <input type="hidden" name="back_url" value="{{ $back_url }}">
                @endif
                <div class="row">
                    <x-adminlte-input name="last_name" label="Apellido" placeholder="Apellido" fgroup-class="col-md-4"
                        value="{{ old('name') }}" />
                    <x-adminlte-input name="first_name" label="Nombre" placeholder="Nombre" fgroup-class="col-md-4"
                        value="{{ old('name') }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.owners.index') }}" class="btn-sm mr-3 btn-default" type="submit"
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

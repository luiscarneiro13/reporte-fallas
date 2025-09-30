@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Nueva Sucursal</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('branchs.store') }}" method="POST">
                @csrf
                <div class="row">
                    <x-adminlte-input name="name" label="Nombre" placeholder="Nombre de la Sucursal" fgroup-class="col-md-4"
                        value="{{ old('name') }}" />
                    <x-adminlte-input name="email" label="Email" placeholder="Email de la Sucursal"
                        fgroup-class="col-md-4" value="{{ old('email') }}" />
                    <x-adminlte-input name="phone" label="Teléfono" placeholder="Teléfono de la Sucursal"
                        fgroup-class="col-md-4" value="{{ old('phone') }}" />
                    <x-adminlte-textarea name="description" label="Descripción" placeholder="Descripción de la Sucursal"
                        fgroup-class="col-md-12" value="{{ old('description') }}" />
                </div>

                <div class="row">
                    <a href="{{ route('branchs.index') }}" class="btn-sm mr-3 btn-default" type="submit"
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

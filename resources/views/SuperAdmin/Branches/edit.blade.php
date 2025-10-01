@extends('adminlte::page')

@section('title', 'JJ&V')

@section('content_header')
    <h1>Editar Sucursal {{ $branch->name }}</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('branches.update', $branch) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <x-adminlte-input name="name" label="Nombre" placeholder="Nombre de la Sucursal" fgroup-class="col-md-4"
                        value="{{ $branch->name }}" />
                    <x-adminlte-input name="email" label="Email" placeholder="Email de la Sucursal"
                        fgroup-class="col-md-4" value="{{ $branch->email }}" />
                    <x-adminlte-input name="phone" label="Teléfono" placeholder="Teléfono de la Sucursal"
                        fgroup-class="col-md-4" value="{{ $branch->phone }}" />
                    <x-adminlte-textarea name="description" label="Descripción" placeholder="Descripción de la Sucursal"
                        fgroup-class="col-md-12">{{ $branch->description }}</x-adminlte-textarea>
                </div>

                <div class="row">
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

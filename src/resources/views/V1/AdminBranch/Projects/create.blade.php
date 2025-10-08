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

                    <div class="col-md-5">
                        <label for="customer_id">Cliente</label>
                        {{ Form::select('customer_id', $customers, null, ['class' => 'select2 form-control']) }}
                    </div>

                    <x-adminlte-input name="name" label="Nombre del proyecto" placeholder=""
                        fgroup-class="col-md-7" value="{{ old('name') }}" />

                    <div class="col-md-5">
                        <label for="division_id">Division</label>
                        {{ Form::select('division_id', $divisions, null, ['class' => 'select2 form-control']) }}
                    </div>

                    <x-adminlte-input name="geographic_area" label="Area geográfica" placeholder=""
                        fgroup-class="col-md-7" value="{{ old('geographic_area') }}" />

                    <x-adminlte-textarea name="description" label="Descripción" placeholder=""
                        fgroup-class="col-md-12" value="{{ old('description') }}" />

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

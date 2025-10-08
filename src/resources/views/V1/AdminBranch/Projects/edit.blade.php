@extends('adminlte::page')

@section('title', 'Proyectos')

@section('content_header')
    <h1>Editar Proyecto</h1>
    <small>{{ $project->name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.projects.update', $project) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $project->id }}">

                <div class="row">

                    <div class="col-md-5">
                        <label for="customer_id">Cliente</label>
                        {{ Form::select('customer_id', $customers, $project->customer_id, ['class' => 'select2 form-control']) }}
                    </div>

                    <x-adminlte-input name="name" label="Nombre del proyecto" placeholder="" fgroup-class="col-md-7"
                        value="{{ $project->name }}" />

                    <div class="col-md-5">
                        <label for="division_id">Division</label>
                        {{ Form::select('division_id', $divisions, $project->division_id, ['class' => 'select2 form-control']) }}
                    </div>

                    <x-adminlte-input name="contract_number" label="Nro. de Contrato (opcional)" placeholder=""
                        fgroup-class="col-md-7" value="{{ $project->contract_number }}" />

                    <x-adminlte-input name="geographic_area" label="Area geográfica" placeholder="" fgroup-class="col-md-12"
                        value="{{ $project->geographic_area }}" />

                    <x-adminlte-textarea name="description" label="Descripción (opcional)" placeholder=""
                        fgroup-class="col-md-12">
                        {{ $project->description }}
                    </x-adminlte-textarea>

                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.projects.index') }}" class="btn-sm mr-3 btn-default" type="submit"
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

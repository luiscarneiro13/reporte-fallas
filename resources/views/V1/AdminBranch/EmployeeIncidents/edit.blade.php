@extends('adminlte::page')
@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/daterangepicker/daterangepicker.css') }}">
@stop

@section('title', 'Incidencias de empleados')

@section('content_header')
    <h1>Editar incidencia</h1>
    <small>{{ $incident->employee?->last_name }} {{ $incident->employee?->first_name }}</small>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.employee.incidents.update', $incident) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $incident->id }}">
                <div class="row">
                    <x-select required label="Empleado" name="employee_id" :items="$employees"
                        :selected="$incident->employee_id" class="col-md-4" classControl="select2 form-control" />

                    <x-input-date-custom required name="date" label="Fecha" placeholder="" class="col-md-3"
                        value="{{ $incident->date }}" />
                </div>

                <div class="row">
                    <x-textarea-custom required name="description" label="Descripción" placeholder=""
                        class="col-md-12" value="{{ $incident->description }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ route('admin.sucursal.employee.incidents.index') }}" class="btn-sm mr-3 btn-default"
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

@extends('adminlte::page')
@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/daterangepicker/daterangepicker.css') }}">
@stop

@section('title', 'Incidencias de empleados')

@section('content_header')
    <h1>Reportar incidencia</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.employee.incidents.store') }}" method="POST">
                @csrf
                @if ($back_url)
                    <input type="hidden" name="back_url" value="{{ $back_url }}">
                @endif
                <div class="row">
                    <x-select required label="Empleado" name="employee_id" :items="$employees"
                        :selected="$selectedEmployeeId" class="col-md-4" classControl="select2 form-control" />

                    <x-input-date-custom required name="date" label="Fecha" placeholder="" class="col-md-3" />
                </div>

                <div class="row">
                    <x-textarea-custom required name="description" label="Descripción" placeholder=""
                        class="col-md-12" value="{{ old('description') }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ request()->back_url ?? route('admin.sucursal.employee.incidents.index') }}"
                        class="btn-sm mr-3 btn-default" type="submit" icon="fas fa-lg fa-save">Cancelar</a>
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

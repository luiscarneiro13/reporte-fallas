@extends('adminlte::page')
@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/daterangepicker/daterangepicker.css') }}">
@stop

@section('title', 'Fallas')

@section('content_header')

@stop

@section('content')
    <form action="{{ route('admin.sucursal.faults.store') }}" method="POST">
        @csrf
        @if ($back_url)
            <input type="hidden" name="back_url" value="{{ $back_url }}">
        @endif
        <div class="card">
            <div class="card-body">
                <div class="card card-dark">
                    <div class="card-header">
                        <h3 class="card-title">Reportar falla</h3>
                    </div>
                    <div class="card-body">

                        {{-- <div class="row">
                            <x-input-custom name="internal_id" label="ID interno" placeholder="" class="col-md-2"
                                value="{{ old('internal_id') }}" />
                        </div> --}}

                        <div class="row">

                            <x-select required label="Reportado por" name="employee_reported_id" :items="$employeeReported"
                                class="col-md-4" />

                            <x-select required label="Equipo" name="equipment_id" :items="$equipment" class="col-md-4"
                                classControl="select2 form-control" />

                            <x-select required label="Area de servicio" name="service_area_id" :items="$serviceArea"
                                class="col-md-4" classControl="select2 form-control" />

                            <x-select required label="Status de la falla" name="fault_status_id" :items="$faultStatus"
                                class="col-md-5" />

                            <x-select required label="Status de repuestos" name="spare_part_status_id" :items="$sparePartStatuses"
                                class="col-md-3" classControl="select2 form-control" />

                            <x-textarea-custom required name="description" label="Descripción de la falla" placeholder=""
                                class="col-md-12 mt-3" value="{{ old('description') }}" />


                        </div>
                        <div class="row">

                            <x-input-date-custom required optional name="report_date" label="Fecha del reporte" placeholder=""
                                class="col-md-2" />


                            <div class="col-md-3">
                                {!! Form::label('scheduled_execution', 'Ejecución planificada') !!}
                                {!! Form::text('scheduled_execution', null, [
                                    'class' => 'form-control datepicker datepicker-optional',
                                    'id' => 'report_date',
                                ]) !!}
                            </div>

                            <div class="col-md-3">
                                {!! Form::label('completed_execution', 'Ejecución completada') !!}
                                {!! Form::text('completed_execution', null, [
                                    'class' => 'form-control datepicker datepicker-optional',
                                    'id' => 'report_date',
                                ]) !!}
                            </div>
                        </div>

                        <div class="row mt-3">

                            <x-select label="Actividad realizada por" name="executor_id" :items="$executors"
                                class="col-md-4" classControl="select2 form-control" />

                            <x-adminlte-textarea name="equipment_maintenance_log"
                                label="Actividades realizadas al equipo" placeholder=""
                                fgroup-class="col-md-12 mt-3" value="{{ old('equipment_maintenance_log') }}" />
                        </div>

                    </div>
                </div>

                <div class="row mt-5">

                    <a href="{{ request()->back_url ?? route('admin.sucursal.faults.index') }}"
                        class="btn-sm mr-3 btn-default" type="submit" icon="fas fa-lg fa-save">Cancelar</a>

                    <x-adminlte-button class="btn-sm" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </div>

            </div>
        </div>
    </form>




@stop

@section('js')
    <script>
        $(document).ready(function() {

            $('.select2').select2({
                width: '100%'
            });

        });
    </script>
@stop

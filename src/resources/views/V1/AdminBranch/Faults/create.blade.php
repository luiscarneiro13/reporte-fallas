@extends('adminlte::page')
@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/daterangepicker/daterangepicker.css') }}">
@stop

@section('title', 'Fallas')

@section('content_header')

    <h1>Reportar falla</h1>

    @vite('resources/js/addCustomer.js')
    @vite('resources/js/addDivision.js')
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sucursal.faults.store') }}" method="POST">
                @csrf
                @if ($back_url)
                    <input type="hidden" name="back_url" value="{{ $back_url }}">
                @endif

                <div class="row">
                    <x-adminlte-input name="internal_id" label="ID interno" placeholder="" fgroup-class="col-md-2"
                        value="{{ old('internal_id') }}" />
                </div>

                <div class="row">

                    <x-select label="Reportado por" name="employee_reported_id" :items="$employeeReported" class="col-md-4" />

                    <x-select label="Equipo" name="equipment_id" :items="$equipment" class="col-md-4"
                        classControl="select2 form-control" />

                    <x-select label="Area de servicio" name="service_area_id" :items="$serviceArea" class="col-md-4"
                        classControl="select2 form-control" />

                    <x-adminlte-textarea name="description" label="Descripción de la falla" placeholder=""
                        fgroup-class="col-md-12 mt-3" value="{{ old('description') }}" />

                    <x-select label="Status de la falla" name="fault_status_id" :items="$faultStatus" class="col-md-3" />

                    <x-select label="Status de repuestos" name="spare_part_status_id" :items="$sparePartStatuses" class="col-md-3"
                        classControl="select2 form-control" />


                    <div class="col-md-3">
                        {!! Form::label('report_date', 'Fecha del reporte') !!}
                        {!! Form::text('report_date', null, ['class' => 'form-control datepicker', 'id' => 'report_date']) !!}
                    </div>

                    <div class="col-md-3">
                        {!! Form::label('scheduled_execution', 'Ejecución planificada') !!}
                        {!! Form::text('scheduled_execution', null, ['class' => 'form-control datepicker', 'id' => 'report_date']) !!}
                    </div>

                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        {!! Form::label('completed_execution', 'Ejecución completada') !!}
                        {!! Form::text('completed_execution', null, [
                            'class' => 'form-control datepicker datepicker-optional',
                            'id' => 'report_date',
                        ]) !!}
                    </div>

                    <x-select label="Actividad realizada por" name="executor_id" :items="$executors" class="col-md-4"
                        classControl="select2 form-control" />

                    <x-adminlte-textarea name="equipment_maintenance_log" label="Actividades realizadas al equipo"
                        placeholder="" fgroup-class="col-md-12 mt-3" value="{{ old('equipment_maintenance_log') }}" />
                </div>

                <div class="row mt-5">
                    <a href="{{ request()->back_url ?? route('admin.sucursal.faults.index') }}"
                        class="btn-sm mr-3 btn-default" type="submit" icon="fas fa-lg fa-save">Cancelar</a>
                    <x-adminlte-button class="btn-sm" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </div>
            </form>
        </div>
    </div>

    <div id="addCustomer"></div>
    <div id="addDivision"></div>

@stop

@section('js')
    <script>
        $(document).ready(function() {

            window.branchId = {{ session('branch')->id ?? 'null' }};

            $('.select2').select2({
                width: '100%'
            });

            $("#modalAddCustomer").on('customerAdded', function(event, newCustomer) {
                $(this).modal("hide");
                var customerSelect = $('select[name="customer_id"]');
                var newOption = new Option(newCustomer.name, newCustomer.id, true, true);
                customerSelect.append(newOption);
                customerSelect.val(newCustomer.id).trigger('change');
            });

            $("#modalAddDivision").on('divisionAdded', function(event, newDivision) {
                $(this).modal("hide");
                var divisionSelect = $('select[name="division_id"]');
                var newOption = new Option(newDivision.name, newDivision.id, true, true);
                divisionSelect.append(newOption);
                divisionSelect.val(newDivision.id).trigger('change');
            });

        });
    </script>
@stop

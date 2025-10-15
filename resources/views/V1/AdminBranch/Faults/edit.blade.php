@extends('adminlte::page')

@section('title', 'Proyectos')

@section('content_header')
    <h1>Editar Proyecto</h1>
    <small>{{ $project->name }}</small>

    @vite('resources/js/addCustomer.js')
    @vite('resources/js/addDivision.js')
@stop

@section('content')
    <form action="{{ route('admin.sucursal.projects.update', $project) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" value="{{ $project->id }}">

        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">Falla detectada</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">

                <div class="row">
                    <x-adminlte-input name="internal_id" label="ID interno (opcional)" placeholder=""
                        fgroup-class="col-md-2" value="{{ old('internal_id') }}" />
                </div>

                <div class="row">

                    <x-select label="Reportado por" name="employee_reported_id" :items="$employeeReported" class="col-md-4" />

                    <x-select label="Equipo" name="equipment_id" :items="$equipment" class="col-md-4"
                        classControl="select2 form-control" />

                    <x-select label="Area de servicio" name="service_area_id" :items="$serviceArea" class="col-md-4"
                        classControl="select2 form-control" />

                    <x-adminlte-textarea name="description" label="Descripción de la falla" placeholder=""
                        fgroup-class="col-md-12 mt-3" value="{{ old('description') }}" />

                </div>

            </div>
        </div>
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">Diagnóstico y Solución</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">

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
            </div>
        </div>
    </form>

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

@extends('adminlte::page')

@section('title', 'Editar Falla')

@section('content_header')
    {{-- <h1>Editar Falla</h1> --}}

    @vite('resources/js/addCustomer.js')
    @vite('resources/js/addDivision.js')
@stop

@section('content')
    <form action="{{ route('admin.sucursal.faults.update', $fault) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" value="{{ $fault->id }}">

        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">Editar Falla</h3>
            </div>
            <div class="card-body">

                {{-- <div class="row">
                    <x-adminlte-input name="internal_id" label="ID interno (opcional)" placeholder=""
                        fgroup-class="col-md-2" value="{{ old('internal_id') }}" />
                </div> --}}

                <div class="row">

                    <x-select :selected="$fault->employee_reported_id" label="Reportado por" name="employee_reported_id" :items="$employeeReported"
                        class="col-md-4" />

                    <x-select :selected="$fault->equipment_id" label="Equipo" name="equipment_id" :items="$equipment" class="col-md-4"
                        classControl="select2 form-control" />

                    <x-select :selected="$fault->service_area_id" label="Area de servicio" name="service_area_id" :items="$serviceArea"
                        class="col-md-4" classControl="select2 form-control" />

                    <x-select :selected="$fault->fault_status_id" label="Status de la falla" name="fault_status_id" :items="$faultStatus"
                        class="col-md-5" />

                    <x-select :selected="$fault->spare_part_status_id" label="Status de repuestos" name="spare_part_status_id" :items="$sparePartStatuses"
                        class="col-md-3" classControl="select2 form-control" />

                    <x-textarea-custom name="description" label="Descripción de la falla" placeholder=""
                        class="col-md-12 mt-3" value="{{ $fault->description }}" />


                </div>

                <div class="row">

                    <x-input-date-custom required name="report_date" label="Fecha del reporte" placeholder=""
                        class="col-md-3" :value="$fault->report_date"/>

                    <x-input-date-custom name="scheduled_execution" label="Ejecución planificada" placeholder=""
                        class="col-md-3" :value="$fault->scheduled_execution" />

                    <x-input-date-custom name="completed_execution" label="Ejecución completada" placeholder=""
                        class="col-md-3" :value="$fault->completed_execution" />

                </div>

                <div class="row mt-3">

                    <x-select label="Actividad realizada por" name="executor_id" :items="$executors" class="col-md-4"
                        classControl="select2 form-control" :selected="$fault->executor_id" />

                    <x-textarea-custom name="equipment_maintenance_log" label="Actividades realizadas al equipo"
                        placeholder="" class="col-md-12 mt-3" value="{{ $fault->equipment_maintenance_log }}" />
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

@extends('adminlte::page')

@section('title', 'Editar Falla')

@section('content_header')
    {{-- <h1>Editar Falla</h1> --}}
@stop

@section('content')

    @php
        // Detecta si estamos en modo cierre
        $isClosing = request()->query('action') == 'close';
    @endphp

    <form action="{{ route('admin.sucursal.faults.update', $fault) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" value="{{ $fault->id }}">

        {{-- Campo oculto 'closed' solo en modo cierre --}}
        @if ($isClosing)
            <input type="hidden" name="closed" value="true">
        @endif

        <div class="card card-dark">
            <div class="card-header">
                {{-- Título dinámico --}}
                <h3 class="card-title">
                    @if ($isClosing)
                        Cerrar Falla
                    @else
                        Editar Falla
                    @endif
                </h3>
            </div>
            <div class="card-body">

                <div class="row">
                    {{-- CAMPOS DE REPORTE Y ASIGNACIÓN (NORMALMENTE DEBEN SER DESHABILITADOS AL CERRAR) --}}

                    <x-select required="{{ $isClosing ? 'required' : '' }}" :selected="$fault->employee_reported_id" label="Reportado por"
                        name="employee_reported_id" :items="$employeeReported" class="col-md-4" />

                    <x-select required="{{ $isClosing ? 'required' : '' }}" :selected="$fault->equipment_id" label="Equipo"
                        name="equipment_id" :items="$equipment" class="col-md-4" classControl="select2 form-control" />

                    <x-select required="{{ $isClosing ? 'required' : '' }}" :selected="$fault->service_area_id" label="Area de servicio"
                        name="service_area_id" :items="$serviceArea" class="col-md-4" classControl="select2 form-control" />

                    {{-- CAMPOS DE ESTADO Y DESCRIPCIÓN --}}

                    {{-- El estado de la falla debe ser requerido y no deshabilitado --}}
                    <x-select :selected="$fault->fault_status_id" label="Status de la falla" name="fault_status_id" :items="$faultStatus"
                        class="col-md-5" required="{{ $isClosing ? 'required' : '' }}" />

                    {{-- El estado de repuestos debe ser requerido y no deshabilitado --}}
                    <x-select :selected="$fault->spare_part_status_id" label="Status de repuestos" name="spare_part_status_id" :items="$sparePartStatuses"
                        class="col-md-3" classControl="select2 form-control"
                        required="{{ $isClosing ? 'required' : '' }}" />

                    {{-- La descripción puede ser deshabilitada si no se permite edición --}}
                    <x-textarea-custom required="{{ $isClosing ? 'required' : '' }}" name="description"
                        label="Descripción de la falla" placeholder="" class="col-md-12 mt-3"
                        value="{{ $fault->description }}" />


                </div>

                <div class="row">

                    {{-- FECHAS --}}

                    <x-input-date-custom required name="report_date" label="Fecha del reporte" placeholder=""
                        class="col-md-3" :value="$fault->report_date" />

                    <x-input-date-custom name="scheduled_execution" label="Ejecución planificada" placeholder=""
                        class="col-md-3" :value="$fault->scheduled_execution" required="{{ $isClosing ? 'required' : '' }}" />

                    {{-- Fecha de Ejecución completada debe ser requerida --}}
                    <x-input-date-custom name="completed_execution" label="Ejecución completada" placeholder=""
                        class="col-md-3" :value="$fault->completed_execution" required="{{ $isClosing ? 'required' : '' }}" />

                </div>

                <div class="row mt-3">

                    {{-- LOG DE MANTENIMIENTO --}}

                    {{-- El ejecutor debe ser requerido --}}
                    <x-select label="Actividad realizada por" name="executor_id" :items="$executors" class="col-md-4"
                        classControl="select2 form-control" :selected="$fault->executor_id"
                        required="{{ $isClosing ? 'required' : '' }}" />

                    {{-- El log de mantenimiento debe ser requerido --}}
                    <x-textarea-custom name="equipment_maintenance_log" label="Actividades realizadas al equipo"
                        placeholder="" class="col-md-12 mt-3" value="{{ $fault->equipment_maintenance_log }}"
                        required="{{ $isClosing ? 'required' : '' }}" />
                </div>


                <div class="row mt-5">
                    <a href="{{ request()->back_url ?? route('admin.sucursal.faults.index') }}"
                        class="btn-sm mr-3 btn-default" type="submit" icon="fas fa-lg fa-save">Cancelar</a>

                    {{-- Botón dinámico --}}
                    @if ($isClosing)
                        <x-adminlte-button class="btn-sm" type="submit" label="Cerrar Falla" theme="success"
                            icon="fas fa-lg fa-check" />
                    @else
                        <x-adminlte-button class="btn-sm" type="submit" label="Guardar" theme="primary"
                            icon="fas fa-lg fa-save" />
                    @endif

                </div>
            </div>
        </div>

    </form>
    {{-- ... (El resto del código se mantiene igual) ... --}}
@stop


@section('js')
    <script>
        $(document).ready(function() {
            // ... (Código JavaScript se mantiene igual) ...
            window.branchId = {{ session('branch')->id ?? 'null' }};

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

            // ⭐ TRUCO: Si estás usando select2, un campo deshabilitado requiere forzar el estilo
            @if ($isClosing)
                $('select[disabled]').closest('.form-group').find('.select2-container').css('pointer-events',
                    'none');
                $('select[disabled]').closest('.form-group').find('.select2-container').attr('tabindex', '-1');
            @endif
        });
    </script>
@stop

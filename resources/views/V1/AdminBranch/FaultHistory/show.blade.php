@extends('adminlte::page')

@section('title', 'Histórico de falla')

@section('content_header')
    {{-- <h1>Editar Falla</h1> --}}
@stop

@section('content')

    @php
        // Detecta si estamos en modo cierre
        $isClosing = request()->query('action') == 'close';
    @endphp

    <div class="card card-dark">
        <div class="card-header">
            {{-- Título dinámico --}}
            <h3 class="card-title">Histórico de fallas</h3>
        </div>
        <div class="card-body">

            <div class="row">

                <x-label-value-horizontal class="col-md-12" label="Reportado por" :value="$history->reported_by_name" />
                <x-label-value-horizontal class="col-md-12" label="Equipo" :value="$history->equipment_name" />
                <x-label-value-horizontal class="col-md-12" label="Area de servicio" :value="$history->service_area_name" />
                <x-label-value-horizontal class="col-md-12" label="Estatus de falla" :value="$history->fault_status_name" />
                <x-label-value-horizontal class="col-md-12" label="Estatus de repuestos" :value="$history->spare_part_status_name" />
                <x-label-value-horizontal class="col-md-12" label="Descripción de la falla" :value="$history->description" />
                <x-label-value-horizontal class="col-md-12" label="Fecha del reporte" :value="$history->report_date->format('d-m-Y')" />
                <x-label-value-horizontal class="col-md-12" label="Ejecución planificada" :value="$history->scheduled_execution->format('d-m-Y')" />
                <x-label-value-horizontal class="col-md-12" label="Ejecución completada" :value="$history->completed_execution->format('d-m-Y')" />
                <x-label-value-horizontal class="col-md-12" label="Actividad realizada por" :value="$history->executor_name" />
                <x-label-value-horizontal class="col-md-12" label="Actividades realizadas al equipo" :value="$history->equipment_maintenance_log" />


            </div>


            <div class="row mt-5">
                <a href="{{ request()->back_url ?? route('admin.sucursal.fault.history.index') }}" class="btn-sm mr-3 btn-default"
                    type="submit" icon="fas fa-lg fa-save">Cancelar</a>
            </div>
        </div>
    </div>

    {{-- ... (El resto del código se mantiene igual) ... --}}
@stop

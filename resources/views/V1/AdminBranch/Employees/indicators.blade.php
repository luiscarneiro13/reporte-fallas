@extends('adminlte::page')

@section('title', 'Indicadores RRHH')

@section('content_header')
    <h1>Indicadores RRHH</h1>
@stop

@section('content')

    {{-- Card: Activos Permanentes --}}
    <div class="row metric-grid">
        <div class="col-lg-4 col-md-6 col-12 mb-3">
            <x-metric-card variant="teal" icon="fas fa-id-card" label="Activos Permanentes"
                :value="$totalActivePermanentEmployees" />
        </div>
    </div>

    {{-- Gráficos de distribución --}}
    <div class="row">
        <div class="col-lg-12 col-12 mb-3">
            <x-chart minHeight="500px" title="Distribución por cargo" type="bar" :labels="$employeesByCargo['labels']"
                :values="$employeesByCargo['values']" :show-percentages="false" />
        </div>
        <div class="col-lg-6 col-12 mb-3">
            <x-pie-chart title="Distribución por tipo de contrato" type="doughnut" :labels="$employeesByContractType['labels']"
                :values="$employeesByContractType['values']" :show-percent="false" />
        </div>
        <div class="col-lg-6 col-12 mb-3">
            <x-pie-chart title="Distribución por proyecto" type="doughnut" :labels="$employeesByProject['labels']"
                :values="$employeesByProject['values']" :show-percent="false" />
        </div>
    </div>
@stop

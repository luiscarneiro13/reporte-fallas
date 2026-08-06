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
        <div class="col-lg-6 col-12 mb-3">
            <x-chart title="Distribución por tipo de contrato" type="pie" :labels="$employeesByContractType['labels']"
                :values="$employeesByContractType['values']" :show-percentages="true" />
        </div>
        <div class="col-lg-6 col-12 mb-3">
            <x-chart title="Distribución por proyecto" type="pie" :labels="$employeesByProject['labels']"
                :values="$employeesByProject['values']" :show-percentages="true" />
        </div>
        <div class="col-lg-12 col-12 mb-3">
            <x-chart title="Distribución por cargo" type="pie" :labels="$employeesByCargo['labels']"
                :values="$employeesByCargo['values']" :show-percentages="true" />
        </div>
    </div>
@stop

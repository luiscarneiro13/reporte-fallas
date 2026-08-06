{{--
    Componente reutilizable para gráficos de torta (pie/doughnut) con Chart.js 4
    + chartjs-plugin-datalabels. Solo pasa datos al DOM vía atributos data-*;
    toda la lógica de renderizado vive en resources/js/charts/pieCharts.js.

    Uso:
        <x-pie-chart title="Distribución por cargo" type="pie"
            :labels="$data['labels']" :values="$data['values']"
            :show-percent="true" />
--}}
@props([
    'title' => 'Gráfico',
    'labels' => [],
    'values' => [],
    'type' => 'doughnut', // 'doughnut' | 'pie'
    'showLegend' => true,
    'showValues' => true,
    'showPercent' => false,
    'colors' => null,
    'height' => '320px',
])

@php
    use Illuminate\Support\Str;
    $chartId = 'pie-chart-' . Str::random(8);
@endphp

@once
    @push('js')
        @vite(['resources/js/charts/dashboard.js'])
    @endpush
@endonce

<div class="card">
    <div class="card-header">
        <h3 class="card-title font-weight-bold">{{ $title }}</h3>
    </div>
    <div class="card-body">
        <div style="position: relative; height: {{ $height }};">
            <canvas id="{{ $chartId }}" data-pie-chart data-chart-type="{{ $type }}" data-title="{{ $title }}"
                data-labels="{{ json_encode($labels) }}" data-values="{{ json_encode($values) }}"
                data-show-legend="{{ $showLegend ? '1' : '0' }}" data-show-values="{{ $showValues ? '1' : '0' }}"
                data-show-percent="{{ $showPercent ? '1' : '0' }}"
                @if ($colors) data-colors="{{ json_encode($colors) }}" @endif
                style="width:100%; height:100%; display:block;"></canvas>
        </div>
    </div>
</div>

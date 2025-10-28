@props([
    'title' => 'Gráfico',
    'labels' => [],
    'values' => [],
    'type' => 'bar', // 'bar', 'pie', 'doughnut', 'line', etc.
    'showPercentages' => false, // NUEVO
])

@php
    use Illuminate\Support\Str;
    $chartId = 'chart-' . Str::random(6);
@endphp

<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $title }}</h3>
    </div>
    <div class="card-body">
        <div style="min-height:280px;">
            <canvas id="{{ $chartId }}" style="width:100%; height:100%; display:block;"></canvas>
        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') {
        console.error(
            '❌ Chart.js no está cargado. Activa el plugin Chart.js en AdminLTE o inclúyelo manualmente.'
        );
        return;
    }

    const ctx = document.getElementById('{{ $chartId }}').getContext('2d');
    const chartType = '{{ $type }}';
    const showPercentages = {{ $showPercentages ? 'true' : 'false' }};

    const defaultColors = [
        '#3366CC','#DC3912','#FF9900','#109618','#990099',
        '#0099C6','#DD4477','#66AA00','#B82E2E','#316395'
    ];

    const backgroundColors = [];
    @json($labels).forEach((_, i) => {
        backgroundColors.push(defaultColors[i % defaultColors.length]);
    });

    const chartData = {
        labels: @json($labels),
        datasets: [{
            label: chartType === 'bar' ? '{{ $title }}' : null,
            data: @json($values),
            backgroundColor: backgroundColors,
            borderColor: chartType === 'bar' ? '#fff' : '#fff',
            borderWidth: chartType === 'bar' ? 1 : 2
        }]
    };

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: chartType === 'bar' ? 'top' : 'bottom'
            }
        }
    };

    if (chartType === 'bar' || chartType === 'line') {
        chartOptions.scales = {
            y: { beginAtZero: true }
        };
    }

    // MOSTRAR PORCENTAJES EN PIE O DOUGHNUT
    if (showPercentages && (chartType === 'pie' || chartType === 'doughnut')) {
        chartOptions.plugins.tooltip = {
            callbacks: {
                label: function(context) {
                    const value = context.raw;
                    const dataArray = context.chart.data.datasets[0].data;
                    const total = dataArray.reduce((a,b) => a + b, 0);
                    const percentage = ((value / total) * 100).toFixed(1) + '%';
                    return context.label + ': ' + percentage;
                }
            }
        };
    }

    new Chart(ctx, {
        type: chartType,
        data: chartData,
        options: chartOptions
    });
});
</script>
@endpush

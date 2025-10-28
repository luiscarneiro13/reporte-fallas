@props([
    'title' => 'Gráfico de torta',
    'labels' => [],
    'values' => [],
])

@php
    use Illuminate\Support\Str;
    $chartId = 'pie-chart-' . Str::random(6);
@endphp

<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $title }}</h3>
    </div>
    <div class="card-body">
        <div style="min-height: 280px;">
            <canvas id="{{ $chartId }}" style="width: 100%; height: 100%; display: block;"></canvas>
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

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        data: @json($values),
                        backgroundColor: [
                            'rgba(60,141,188,0.9)',
                            'rgba(0,166,90,0.9)',
                            'rgba(243,156,18,0.9)',
                            'rgba(221,75,57,0.9)',
                            'rgba(0,192,239,0.9)',
                            'rgba(255,193,7,0.9)',
                            'rgba(102,51,153,0.9)',
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });
    </script>
@endpush

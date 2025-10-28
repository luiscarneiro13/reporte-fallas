@props([
    'title' => 'Gráfico de barras',
    'labels' => [],
    'values' => [],
])

@php
    use Illuminate\Support\Str;
    $chartId = 'barchart-' . Str::random(6);
@endphp

<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $title }}</h3>
    </div>
    <div class="card-body">
        <div style="min-height:260px;">
            <canvas id="{{ $chartId }}" style="width:100%; height:100%; display:block;"></canvas>
        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Espera que Chart.js esté disponible
    if (typeof Chart === 'undefined') {
        console.error('Chart.js no está cargado. Asegúrate de incluirlo en AdminLTE o tu layout.');
        return;
    }

    const ctx = document.getElementById('{{ $chartId }}').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [{
                label: '{{ $title }}',
                data: @json($values),
                backgroundColor: [
                    'rgba(60,141,188,0.9)',
                    'rgba(0,166,90,0.9)',
                    'rgba(243,156,18,0.9)',
                    'rgba(221,75,57,0.9)',
                    'rgba(0,192,239,0.9)',
                    'rgba(255,193,7,0.9)'
                ],
                borderColor: 'rgba(60,141,188,1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
@endpush

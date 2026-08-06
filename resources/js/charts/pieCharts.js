/**
 * pieCharts.js
 *
 * Módulo reutilizable para gráficos de tipo pie/doughnut con Chart.js 4.
 * Registra únicamente los componentes que necesita (no importa Chart.js completo)
 * y el plugin chartjs-plugin-datalabels para mostrar valores dentro de cada segmento.
 */

import {
    Chart,
    ArcElement,
    PieController,
    DoughnutController,
    Tooltip,
    Legend,
    Title,
} from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';

Chart.register(ArcElement, PieController, DoughnutController, Tooltip, Legend, Title, ChartDataLabels);

// Paleta por defecto: colores diferenciados y con buen contraste para etiquetas blancas.
const DEFAULT_COLORS = [
    '#3366CC', '#DC3912', '#FF9900', '#109618', '#990099',
    '#0099C6', '#DD4477', '#66AA00', '#B82E2E', '#316395',
];

function resolveColors(labels, colors) {
    if (Array.isArray(colors) && colors.length) {
        return labels.map((_, i) => colors[i % colors.length]);
    }
    return labels.map((_, i) => DEFAULT_COLORS[i % DEFAULT_COLORS.length]);
}

/**
 * Crea un gráfico de torta (pie o doughnut) dentro del <canvas> indicado.
 *
 * @param {string} elementId          ID del elemento <canvas>.
 * @param {string[]} labels           Nombres de cada categoría.
 * @param {number[]} values           Cantidad total de cada categoría.
 * @param {object} [options]
 * @param {'doughnut'|'pie'} [options.type='doughnut']
 * @param {string|null} [options.title=null]        Título mostrado sobre el gráfico.
 * @param {boolean} [options.showLegend=true]        Mostrar/ocultar leyenda.
 * @param {boolean} [options.showValues=true]        Mostrar la cantidad dentro del segmento.
 * @param {boolean} [options.showPercent=false]      Mostrar el porcentaje dentro del segmento.
 * @param {string[]|null} [options.colors=null]      Colores personalizados (uno por categoría).
 * @returns {Chart|null} La instancia de Chart.js, o null si no se encontró el canvas.
 */
export function createDoughnutChart(elementId, labels, values, options = {}) {
    const canvas = document.getElementById(elementId);

    if (!canvas) {
        console.error(`[pieCharts] No se encontró el canvas #${elementId}`);
        return null;
    }

    const {
        type = 'doughnut',
        title = null,
        showLegend = true,
        showValues = true,
        showPercent = false,
        colors = null,
    } = options;

    const backgroundColor = resolveColors(labels, colors);
    const total = values.reduce((sum, value) => sum + value, 0);

    return new Chart(canvas.getContext('2d'), {
        type,
        data: {
            labels,
            datasets: [
                {
                    data: values,
                    backgroundColor,
                    borderColor: '#fff',
                    borderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: type === 'doughnut' ? '55%' : 0,
            plugins: {
                title: {
                    display: !!title,
                    text: title,
                    font: { size: 14, weight: '600' },
                    padding: { bottom: 12 },
                },
                legend: {
                    display: showLegend,
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 16 },
                },
                tooltip: {
                    callbacks: {
                        label(ctx) {
                            const value = ctx.raw;
                            const percent = total ? ((value / total) * 100).toFixed(1) : 0;
                            return `${ctx.label}: ${value} (${percent}%)`;
                        },
                    },
                },
                // chartjs-plugin-datalabels: escribe el valor/porcentaje directamente sobre el segmento.
                datalabels: {
                    color: '#fff',
                    font: { weight: 'bold', size: 11 },
                    textAlign: 'center',
                    clamp: true,
                    formatter(value) {
                        if (!total) return '';
                        const percent = ((value / total) * 100).toFixed(1) + '%';

                        if (showValues && showPercent) return `${value}\n${percent}`;
                        if (showValues) return `${value}`;
                        if (showPercent) return percent;
                        return '';
                    },
                    display() {
                        // Siempre visible: no se oculta en segmentos chicos, para que
                        // ningún gajo de la dona se quede sin su número.
                        return (showValues || showPercent) && total > 0;
                    },
                },
            },
        },
    });
}

/** Atajo para un gráfico tipo pie (sin agujero central). */
export function createPieChart(elementId, labels, values, options = {}) {
    return createDoughnutChart(elementId, labels, values, { ...options, type: 'pie' });
}

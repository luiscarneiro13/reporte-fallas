/**
 * dashboard.js (entry point de Vite)
 *
 * Escanea el DOM en busca de <canvas data-pie-chart> y los inicializa con
 * createDoughnutChart(). El componente Blade <x-pie-chart> es quien marca
 * los canvas y les inyecta los datos vía atributos data-*, por lo que las
 * vistas nunca necesitan escribir JavaScript propio: solo declaran el
 * componente y este archivo hace el resto.
 */

import { createDoughnutChart } from './pieCharts';

function parseJsonAttr(el, attr, fallback) {
    const raw = el.getAttribute(attr);
    if (!raw) return fallback;

    try {
        return JSON.parse(raw);
    } catch (error) {
        console.error(`[dashboard.js] No se pudo parsear ${attr} en #${el.id}`, error);
        return fallback;
    }
}

function initPieCharts() {
    document.querySelectorAll('[data-pie-chart]').forEach((canvas) => {
        const labels = parseJsonAttr(canvas, 'data-labels', []);
        const values = parseJsonAttr(canvas, 'data-values', []);
        const colors = parseJsonAttr(canvas, 'data-colors', null);

        createDoughnutChart(canvas.id, labels, values, {
            type: canvas.dataset.chartType || 'doughnut',
            title: canvas.dataset.title || null,
            showLegend: canvas.dataset.showLegend !== '0',
            showValues: canvas.dataset.showValues !== '0',
            showPercent: canvas.dataset.showPercent === '1',
            colors,
        });
    });
}

document.addEventListener('DOMContentLoaded', initPieCharts);

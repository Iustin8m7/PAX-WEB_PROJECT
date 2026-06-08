(function () {
    'use strict';

    const chartRegistry = new Map();

    function ensureChartJs() {
        if (typeof window.Chart === 'undefined') {
            throw new Error('Chart.js nu este încărcat.');
        }
    }

    function ensureUtils() {
        if (!window.appUtils) {
            throw new Error('appUtils nu este disponibil.');
        }
    }

    function destroyChart(containerId) {
        const existingChart = chartRegistry.get(containerId);

        if (existingChart) {
            existingChart.destroy();
            chartRegistry.delete(containerId);
        }
    }

    function clearContainer(container) {
        if (!container) {
            return null;
        }

        if (window.appUtils && typeof window.appUtils.clearElement === 'function') {
            window.appUtils.clearElement(container);
        } else {
            container.innerHTML = '';
        }

        const canvas = document.createElement('canvas');
        canvas.setAttribute('aria-label', 'Grafic statistic');
        canvas.setAttribute('role', 'img');
        container.appendChild(canvas);

        return canvas;
    }

    function renderEmptyState(container, message) {
        destroyChart(container?.id || '');

        if (window.appUtils && typeof window.appUtils.renderEmptyState === 'function') {
            window.appUtils.renderEmptyState(container, message);
            return;
        }

        if (container) {
            container.innerHTML = `<div class="chart-empty">${message}</div>`;
        }
    }

    function getBaseOptions(title = '') {
        ensureUtils();

        return {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 500,
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#e2e8f0',
                        font: {
                            family: 'Plus Jakarta Sans',
                            size: 12,
                        },
                    },
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    titleColor: '#f8fafc',
                    bodyColor: '#e2e8f0',
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label(context) {
                            const rawValue = context.raw;
                            return `${context.dataset.label || 'Valoare'}: ${window.appUtils.formatNumber(rawValue)}`;
                        },
                    },
                },
                title: {
                    display: title !== '',
                    text: title,
                    color: '#f8fafc',
                    font: {
                        family: 'Plus Jakarta Sans',
                        size: 16,
                        weight: '700',
                    },
                },
            },
            scales: {
                x: {
                    ticks: {
                        color: '#94a3b8',
                        font: {
                            family: 'Plus Jakarta Sans',
                            size: 11,
                        },
                    },
                    grid: {
                        color: 'rgba(255,255,255,0.04)',
                    },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#94a3b8',
                        font: {
                            family: 'Plus Jakarta Sans',
                            size: 11,
                        },
                        callback(value) {
                            return window.appUtils.formatNumber(value);
                        },
                    },
                    grid: {
                        color: 'rgba(255,255,255,0.05)',
                    },
                },
            },
        };
    }

    function createChart(container, config) {
        ensureChartJs();

        if (!container || !container.id) {
            throw new Error('Containerul graficului este invalid sau nu are id.');
        }

        destroyChart(container.id);

        const canvas = clearContainer(container);
        if (!canvas) {
            throw new Error('Nu s-a putut crea canvas-ul pentru grafic.');
        }

        const chart = new window.Chart(canvas.getContext('2d'), config);
        chartRegistry.set(container.id, chart);

        return chart;
    }

    function createYearlyTotalsChart(container, items) {
        if (!container) {
            return;
        }

        if (!Array.isArray(items) || items.length === 0) {
            renderEmptyState(container, 'Nu există date pentru evoluția pe ani.');
            return;
        }

        const labels = items.map((item) => String(item.year));
        const values = items.map((item) => Number(item.total_vehicles || 0));

        createChart(container, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Total vehicule',
                        data: values,
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true,
                    },
                ],
            },
            options: getBaseOptions('Evoluția anuală a parcului auto'),
        });
    }

    function createTopBrandsChart(container, items) {
        if (!container) {
            return;
        }

        if (!Array.isArray(items) || items.length === 0) {
            renderEmptyState(container, 'Nu există date pentru topul mărcilor.');
            return;
        }

        const labels = items.map((item) => item.name || 'Nedefinit');
        const values = items.map((item) => Number(item.total_vehicles || 0));

        createChart(container, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Total vehicule',
                        data: values,
                        borderWidth: 1,
                    },
                ],
            },
            options: getBaseOptions('Top mărci'),
        });
    }

    function createFuelDistributionChart(container, items) {
        if (!container) {
            return;
        }

        if (!Array.isArray(items) || items.length === 0) {
            renderEmptyState(container, 'Nu există date pentru distribuția pe combustibil.');
            return;
        }

        const labels = items.map((item) => item.name || 'Nedefinit');
        const values = items.map((item) => Number(item.total_vehicles || 0));

        createChart(container, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Total vehicule',
                        data: values,
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                ...getBaseOptions('Structură pe combustibil'),
                scales: undefined,
            },
        });
    }

    function createCategoryDistributionChart(container, items) {
        if (!container) {
            return;
        }

        if (!Array.isArray(items) || items.length === 0) {
            renderEmptyState(container, 'Nu există date pentru distribuția pe categorii.');
            return;
        }

        const labels = items.map((item) => item.name || 'Nedefinit');
        const values = items.map((item) => Number(item.total_vehicles || 0));

        createChart(container, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Total vehicule',
                        data: values,
                        borderWidth: 1,
                    },
                ],
            },
            options: getBaseOptions('Distribuție pe categorii naționale'),
        });
    }

    function updateChart(container, createFunction, items) {
        if (typeof createFunction !== 'function') {
            throw new Error('Funcția de creare a graficului este invalidă.');
        }

        createFunction(container, items);
    }

    function destroyAllCharts() {
        for (const [, chart] of chartRegistry.entries()) {
            chart.destroy();
        }

        chartRegistry.clear();
    }

    window.appCharts = {
        createYearlyTotalsChart,
        createTopBrandsChart,
        createFuelDistributionChart,
        createCategoryDistributionChart,
        updateChart,
        destroyChart,
        destroyAllCharts,
    };
})();
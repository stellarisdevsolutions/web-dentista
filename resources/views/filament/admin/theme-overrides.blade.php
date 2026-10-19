<script>
    /* Pre-apply sidebar collapsed class from localStorage to eliminate FOUC */
    (function() {
        try {
            var isDesktop = window.innerWidth >= 1024;
            var stored = localStorage.getItem('_x_isOpenDesktop');
            if (isDesktop && stored === 'false') {
                document.documentElement.classList.add('fi-sidebar-force-collapsed');
            }
        } catch (e) {}
    })();

    /* Minimal inline Chart.js plugin to draw values on top of bar charts */
    (function() {
        const barValuesOnTopPlugin = {
            id: 'barValuesOnTop',
            afterDatasetsDraw(chart) {
                if (chart.config.type !== 'bar') return;
                if (!chart.options.plugins?.barValuesOnTop?.display) return;
                const ctx = chart.ctx;
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#F1F5F9' : '#0F172A';

                chart.data.datasets.forEach((dataset, i) => {
                    const meta = chart.getDatasetMeta(i);
                    if (!meta || !meta.data) return;
                    meta.data.forEach((bar, index) => {
                        const value = dataset.data[index];
                        if (value > 0) {
                            ctx.save();
                            ctx.fillStyle = textColor;
                            ctx.font = '800 12px Manrope, sans-serif';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';
                            const targetY = Math.max((chart.chartArea?.top || 0) + 14, bar.y - 6);
                            ctx.fillText(value, bar.x, targetY);
                            ctx.restore();
                        }
                    });
                });
            }
        };

        window.filamentChartJsGlobalPlugins = window.filamentChartJsGlobalPlugins || [];
        window.filamentChartJsGlobalPlugins.push(barValuesOnTopPlugin);
    })();
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

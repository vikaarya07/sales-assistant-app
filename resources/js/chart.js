document.addEventListener("alpine:init", () => {
    Alpine.data("overviewChart", (initialData) => ({
        chart: null,

        init() {
            this.$nextTick(() => {
                this.renderChart(initialData);
            });
        },

        renderChart(data) {
            const canvas = this.$refs.canvas;

            if (!canvas) {
                return;
            }

            const ctx = canvas.getContext("2d");

            if (!ctx) {
                return;
            }

            /*
             * Destroy existing Chart instance attached
             * to this canvas.
             */
            const existingChart = window.Chart.getChart(canvas);

            if (existingChart) {
                existingChart.destroy();
            }

            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }

            const isDark = document.documentElement.classList.contains("dark");

            const textColor = isDark ? "#a1a1aa" : "#71717a";

            const gridColor = isDark
                ? "rgba(255,255,255,0.08)"
                : "rgba(0,0,0,0.06)";

            this.chart = new window.Chart(ctx, {
                type: "bar",

                data: {
                    labels: data.labels,

                    datasets: [
                        {
                            label: "Bulan Lalu",
                            data: data.previous,

                            backgroundColor: "rgba(161, 161, 170, 0.45)",
                            borderColor: "#a1a1aa",
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false,

                            barPercentage: 0.7,
                            categoryPercentage: 0.7,
                        },

                        {
                            label: "Bulan Dipilih",
                            data: data.current,

                            backgroundColor: "rgba(99, 102, 241, 0.8)",
                            borderColor: "#6366f1",
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false,

                            barPercentage: 0.7,
                            categoryPercentage: 0.7,
                        },
                    ],
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    interaction: {
                        mode: "index",
                        intersect: false,
                    },

                    plugins: {
                        legend: {
                            display: true,
                            position: "bottom",

                            labels: {
                                color: textColor,
                                usePointStyle: true,
                                pointStyle: "rectRounded",
                                padding: 20,
                                boxWidth: 18,
                            },
                        },

                        tooltip: {
                            enabled: true,

                            backgroundColor: isDark ? "#27272a" : "#ffffff",

                            titleColor: isDark ? "#fafafa" : "#18181b",

                            bodyColor: isDark ? "#d4d4d8" : "#3f3f46",

                            borderColor: isDark ? "#3f3f46" : "#e4e4e7",

                            borderWidth: 1,
                            padding: 12,
                            displayColors: true,

                            callbacks: {
                                label(context) {
                                    return ` ${
                                        context.dataset.label
                                    }: ${new Intl.NumberFormat("id-ID").format(
                                        context.parsed.y,
                                    )} customer`;
                                },
                            },
                        },
                    },

                    scales: {
                        x: {
                            stacked: false,

                            ticks: {
                                color: textColor,
                                maxRotation: 45,
                                minRotation: 0,
                                padding: 8,
                            },

                            grid: {
                                display: false,
                            },

                            border: {
                                display: false,
                            },
                        },

                        y: {
                            beginAtZero: true,

                            ticks: {
                                color: textColor,
                                precision: 0,
                                padding: 8,

                                callback(value) {
                                    return new Intl.NumberFormat(
                                        "id-ID",
                                    ).format(value);
                                },
                            },

                            grid: {
                                color: gridColor,
                            },

                            border: {
                                display: false,
                            },
                        },
                    },
                },
            });
        },

        update(data) {
            this.$nextTick(() => {
                this.renderChart(data);
            });
        },

        destroy() {
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }

            const canvas = this.$refs.canvas;

            if (canvas) {
                const existingChart = window.Chart.getChart(canvas);

                if (existingChart) {
                    existingChart.destroy();
                }
            }
        },
    }));
});

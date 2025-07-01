<template>
    <div class="container my-4">
        <h2 class="mb-4">Dashboard</h2>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Sales</h5>
                        <p class="card-text display-4">{{ formatCurrency(totalSales) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Products</h5>
                        <p class="card-text display-4">{{ totalProducts ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Sales Over Time</h5>
                <div class="mb-3 text-end">
                    <select v-model="filterType" @change="fetchDashboardData" class="form-select w-auto d-inline-block">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                <div style="height: 400px; position: relative;">
                    <line-chart :chart-data="chartData" :chart-options="chartOptions" />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import LineChart from "@/components/LineChart.vue";
export default {
    components: { LineChart },
    data() {
        return {
            totalSales: 0,
            totalProducts: 0,
            totalCustomers: 0,
            chartData: {
                labels: [],
                datasets: []
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return (value / 1000).toFixed(0) + 'K';
                                }
                                return value;
                            }
                        }
                    }
                }
            },
            filterType: 'monthly',
        };
    },
    methods: {
        formatCurrency(value) {
            if (value >= 1000000) {
                return "BDT " + (value / 1000000).toFixed(1) + 'M';
            } else if (value >= 1000) {
                return "BDT " + (value / 1000).toFixed(0) + 'K';
            }
            return "BDT " + (value || 0).toLocaleString();
        },
        async fetchDashboardData() {
            try {
                const response = await this.$axios.get("/api/dashboard-stats", {
                    params: { type: this.filterType }
                });
                this.totalSales = response.data.total_sales;
                this.totalProducts = response.data.total_products;
                const chart = response.data.sales_chart;
                if (chart && chart.labels && chart.data) {
                    this.chartData = {
                        labels: chart.labels,
                        datasets: [
                            {
                                label: "Sales",
                                backgroundColor: "rgba(54, 162, 235, 0.2)",
                                borderColor: "rgba(54, 162, 235, 1)",
                                data: chart.data,
                                fill: true,
                                tension: 0.3,
                            },
                        ],
                    };
                }
            } catch (error) {
                console.error("Failed to load dashboard data", error);
                this.chartData = {
                    labels: [],
                    datasets: [{
                        label: "Sales",
                        backgroundColor: "rgba(54, 162, 235, 0.2)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        data: [],
                        fill: true,
                        tension: 0.3,
                    }],
                };
            }
        },
    },
    mounted() {
        this.fetchDashboardData();
    },
};
</script>
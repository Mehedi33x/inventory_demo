<template>
    <div class="container my-4">
        <h2 class="mb-4">Dashboard</h2>

        <!-- Stats cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Sales</h5>
                        <p class="card-text display-4">{{ totalSales }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Products</h5>
                        <p class="card-text display-4">{{ totalProducts }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Customers</h5>
                        <p class="card-text display-4">{{ totalCustomers }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales chart -->
        <!-- <div class="card">
            <div class="card-body">
                <h5 class="card-title">Sales Over Time</h5>
                <line-chart :chart-data="chartData" :chart-options="chartOptions" />
            </div>
        </div> -->
    </div>
</template>

<script>
// import LineChart from "@/components/LineChart.vue";

export default {
    components: { LineChart },
    data() {
        return {
            totalSales: 0,
            totalProducts: 0,
            totalCustomers: 0,
            chartData: null,
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
            },
        };
    },
    methods: {
        async fetchDashboardData() {
            try {
                // Example API call for stats
                const statsResponse = await this.$axios.get("/api/dashboard-stats");
                this.totalSales = statsResponse.data.total_sales;
                this.totalProducts = statsResponse.data.total_products;
                this.totalCustomers = statsResponse.data.total_customers;

                // Example API call for sales data for chart
                const salesResponse = await this.$axios.get("/api/sales-chart-data");

                this.chartData = {
                    labels: salesResponse.data.labels, // e.g., ["Jan", "Feb", "Mar"]
                    datasets: [
                        {
                            label: "Sales",
                            backgroundColor: "rgba(54, 162, 235, 0.2)",
                            borderColor: "rgba(54, 162, 235, 1)",
                            data: salesResponse.data.data, // e.g., [30, 50, 70]
                            fill: true,
                            tension: 0.3,
                        },
                    ],
                };
            } catch (error) {
                console.error("Failed to load dashboard data", error);
            }
        },
    },
    mounted() {
        this.fetchDashboardData();
    },
};

</script>

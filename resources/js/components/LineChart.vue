<template>
  <Line v-if="chartData && chartData.labels" :data="chartData" :options="chartOptions" />
  <div v-else class="text-center py-4">
    <div class="spinner-border" role="status">
      <span class="visually-hidden">Loading chart...</span>
    </div>
  </div>
</template>

<script>
import {
  Chart as ChartJS, Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement, LineController,
} from 'chart.js';
import { Line } from 'vue-chartjs';

ChartJS.register(LineController, LineElement, PointElement, LinearScale, Title, Tooltip, Legend, CategoryScale);
export default {
  name: 'LineChart',
  components: { Line },
  props: {
    chartData: {
      type: Object,
      required: false,
      default: null
    },
    chartOptions: {
      type: Object,
      required: false,
      default: () => ({
        responsive: true,
        maintainAspectRatio: false,
      })
    },
  },
};
</script>
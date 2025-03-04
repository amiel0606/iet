<template>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Welcome to Your Dashboard</h1>

        <div class="flex space-x-4 mb-6">
            <div class="flex flex-col items-center">
                <Card color="#4CAF50" width="250" height="120">
                    <div>Total Income: {{ totalIncome }}</div>
                </Card>
                <a href="/tracker" class="text-blue-500 text-sm mt-2"
                    >View More</a
                >
            </div>
            <div class="flex flex-col items-center">
                <Card color="#F44336" width="250" height="120">
                    <div>Total Expenses: {{ totalExpenses }}</div>
                </Card>
                <a href="/tracker" class="text-blue-500 text-sm mt-2"
                    >View More</a
                >
            </div>
            <Card color="#2196F3" width="250" height="120"
                >Total Revenue: {{ totalRevenue }}</Card
            >
        </div>

        <div class="mb-6">
            <label for="dateRange" class="block font-semibold"
                >Filter by Date Range:</label
            >
            <input type="date" v-model="startDate" class="border p-2 mr-2" />
            <input type="date" v-model="endDate" class="border p-2" />
            <button
                @click="filterData"
                class="ml-2 px-4 py-2 bg-blue-500 text-white"
            >
                Apply
            </button>
        </div>

        <canvas ref="chartCanvas"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Chart from "chart.js/auto";
import Card from "@/Components/Card.vue";

const totalIncome = ref(0);
const totalExpenses = ref(0);
const totalRevenue = ref(0);
const startDate = ref("");
const endDate = ref("");
const chartCanvas = ref(null);
let chartInstance = null;

const fetchData = () => {
    totalIncome.value = 5000;
    totalExpenses.value = 2000;
    totalRevenue.value = totalIncome.value - totalExpenses.value;
    updateChart();
};

const filterData = () => {
    fetchData();
};

const updateChart = () => {
    if (chartInstance) chartInstance.destroy();
    chartInstance = new Chart(chartCanvas.value, {
        type: "bar",
        data: {
            labels: ["Income", "Expenses", "Revenue"],
            datasets: [
                {
                    label: "Financial Overview",
                    data: [
                        totalIncome.value,
                        totalExpenses.value,
                        totalRevenue.value,
                    ],
                    backgroundColor: ["#4CAF50", "#F44336", "#2196F3"],
                },
            ],
        },
    });
};

onMounted(fetchData);
</script>

<style scoped>
.container {
    max-width: 800px;
    margin: auto;
}
</style>

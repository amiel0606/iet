<template>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Welcome to Your Dashboard</h1>

        <!-- Summary Cards -->
        <div class="flex space-x-4 mb-6">
            <div class="flex flex-col items-center">
                <Card color="#4CAF50" :width="250" :height="120">
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

        <!-- Date Filter -->
        <div class="mb-6">
            <label class="block font-semibold">Filter by Date Range:</label>
            <input type="date" v-model="startDate" class="border p-2 mr-2" />
            <input type="date" v-model="endDate" class="border p-2" />
            <button
                @click="filterData"
                class="ml-2 px-4 py-2 bg-blue-500 text-white"
            >
                Apply
            </button>
        </div>

        <!-- Bar Chart -->
        <div>
            <h2 class="text-lg font-semibold mb-2">Financial Overview</h2>
            <canvas ref="barChartCanvas"></canvas>
        </div>

        <!-- Pie Charts -->
        <div class="grid grid-cols-2 gap-6 mt-6">
            <div>
                <h2 class="text-lg font-semibold mb-2">Income Categories</h2>
                <canvas ref="incomePieChartCanvas"></canvas>
            </div>
            <div>
                <h2 class="text-lg font-semibold mb-2">Expense Categories</h2>
                <canvas ref="expensePieChartCanvas"></canvas>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import Chart from "chart.js/auto";
import Card from "@/Components/Card.vue";

const totalIncome = ref(0);
const totalExpenses = ref(0);
const totalRevenue = ref(0);
const startDate = ref("");
const endDate = ref("");

const barChartCanvas = ref(null);
const incomePieChartCanvas = ref(null);
const expensePieChartCanvas = ref(null);

let barChartInstance = null;
let incomePieChartInstance = null;
let expensePieChartInstance = null;

const incomeData = ref([]);
const expenseData = ref([]);

const fetchData = async () => {
    try {
        const response = await axios.get("/all");
        const { incomes, expenses } = response.data;

        totalIncome.value = incomes.reduce((sum, item) => sum + Number(item.amount), 0);
        totalExpenses.value = expenses.reduce(
            (sum, item) => sum + Number(item.amount),
            0
        );
        totalRevenue.value = totalIncome.value - totalExpenses.value;

        processChartData(incomes, expenses);
    } catch (error) {
        console.error("Error fetching data:", error);
    }
};

const filterData = () => {
    fetchData();
};

const processChartData = (incomes, expenses) => {
    const incomeCategories = {};
    incomes.forEach((item) => {
        if (!incomeCategories[item.category]) {
            incomeCategories[item.category] = 0;
        }
        incomeCategories[item.category] += item.amount;
    });

    // Grouping expense data by category
    const expenseCategories = {};
    expenses.forEach((item) => {
        if (!expenseCategories[item.category]) {
            expenseCategories[item.category] = 0;
        }
        expenseCategories[item.category] += item.amount;
    });

    incomeData.value = Object.entries(incomeCategories);
    expenseData.value = Object.entries(expenseCategories);

    updateCharts();
};

const updateCharts = () => {
    if (barChartInstance) barChartInstance.destroy();
    if (incomePieChartInstance) incomePieChartInstance.destroy();
    if (expensePieChartInstance) expensePieChartInstance.destroy();

    // Bar Chart: Total Income, Expenses, Revenue
    barChartInstance = new Chart(barChartCanvas.value, {
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

    // Income Pie Chart: Categories
    incomePieChartInstance = new Chart(incomePieChartCanvas.value, {
        type: "pie",
        data: {
            labels: incomeData.value.map(([category]) => category),
            datasets: [
                {
                    label: "Income Categories",
                    data: incomeData.value.map(([, amount]) => amount),
                    backgroundColor: [
                        "#4CAF50", "#66BB6A", "#81C784", "#A5D6A7", "#C8E6C9",
                    ],
                },
            ],
        },
    });

    // Expense Pie Chart: Categories
    expensePieChartInstance = new Chart(expensePieChartCanvas.value, {
        type: "pie",
        data: {
            labels: expenseData.value.map(([category]) => category),
            datasets: [
                {
                    label: "Expense Categories",
                    data: expenseData.value.map(([, amount]) => amount),
                    backgroundColor: [
                        "#F44336", "#E57373", "#EF9A9A", "#FFCDD2", "#FFEBEE",
                    ],
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

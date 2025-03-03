<script setup>
import { ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";
import Table from "@/Components/Table.vue";
import Modal from "@/Components/Modal.vue";
import Notification from "@/Components/Notification.vue";

const props = usePage().props;
const notification = ref({
    show: false,
    message: "",
    type: "success",
});

const showNotification = (message, type = "success") => {
    notification.value = { show: true, message, type };
    setTimeout(() => {
        notification.value.show = false;
    }, 3000);
};

// Make incomes and expenses reactive
const incomes = ref(props.incomes || []);
const expenses = ref(props.expenses || []);

const incomeColumns = [
    "ID",
    "Date",
    "Amount",
    "Category",
    "Description",
    "Actions",
];
const expenseColumns = [
    "ID",
    "Date",
    "Amount",
    "Category",
    "Description",
    "Actions",
];

const formattedIncomeData = computed(() =>
    incomes.value.map((income) => ({
        id: income.id,
        Date: income.date,
        Amount: income.amount,
        Category: income.category,
        Description: income.description,
        Actions: income.id,
    }))
);

const formattedExpenseData = computed(() =>
    expenses.value.map((expense) => ({
        id: expense.id,
        Date: expense.date,
        Amount: expense.amount,
        Category: expense.category,
        Description: expense.description,
        Actions: expense.id,
    }))
);

const tableWidth = "100%";
const maxHeight = "300px";
const isModalOpen = ref(false);
const modalTitle = ref("");
const transactionType = ref(""); // Track if it's income or expense
const newTransaction = ref({ amount: "", category: "", description: "" });

const openModal = (type) => {
    modalTitle.value = `Add ${type.charAt(0).toUpperCase() + type.slice(1)}`;
    transactionType.value = type;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    newTransaction.value = { amount: "", category: "", description: "" };
};

const saveTransaction = async () => {
    try {
        const url =
            transactionType.value === "income"
                ? "/incomes/create"
                : "/expenses/create";
        const transactionData = {
            date: new Date().toISOString().split("T")[0],
            amount: newTransaction.value.amount,
            category: newTransaction.value.category,
            description: newTransaction.value.description,
        };

        const response = await axios.post(url, transactionData);

        if (response.status === 200) {
            const newRecord = {
                id: response.data.id,
                date: transactionData.date, 
                amount: transactionData.amount,
                category: transactionData.category,
                description: transactionData.description,
            };

            if (transactionType.value === "income") {
                incomes.value = [...incomes.value, newRecord]; 
            } else {
                expenses.value = [...expenses.value, newRecord]; 
            }

            showNotification(
                `${transactionType.value} added successfully!`,
                "success"
            );
        }
        closeModal();
    } catch (error) {
        console.error(
            "Error saving transaction:",
            error.response?.data || error
        );
        showNotification("Error saving transaction.", "danger");
    }
};

const deleteTransaction = async (id, type) => {
    if (!confirm(`Are you sure you want to delete this ${type}?`)) return;

    try {
        const url =
            type === "income"
                ? `/incomes/delete/${id}`
                : `/expenses/delete/${id}`;
        const response = await axios.delete(url);

        if (response.status === 200) {
            if (type === "income") {
                incomes.value = incomes.value.filter(
                    (income) => income.id !== id
                );
            } else {
                expenses.value = expenses.value.filter(
                    (expense) => expense.id !== id
                );
            }
            showNotification(`${type} deleted successfully!`, "success");
        }
    } catch (error) {
        console.error(`Error deleting ${type}:`, error);
        showNotification(`Error deleting ${type}.`, "danger");
    }
};
</script>

<template>
    <Notification
        v-if="notification.show"
        :message="notification.message"
        :type="notification.type"
    />
    <div class="flex flex-col items-center justify-center min-h-screen p-6">
        <h1 class="text-2xl font-bold mb-6">Transaction Tracker</h1>
        <div class="w-full max-w-4xl">
            <!-- Income Section -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-xl font-semibold">Income</h2>
                    <button
                        @click="openModal('income')"
                        class="px-4 py-2 bg-green-500 text-white rounded"
                    >
                        + Add Income
                    </button>
                </div>
                <Table
                    :columns="incomeColumns"
                    :data="formattedIncomeData"
                    :tableWidth="tableWidth"
                    :maxHeight="maxHeight"
                    @delete="(id) => deleteTransaction(id, 'income')"
                />
            </div>

            <!-- Expenses Section -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-xl font-semibold">Expenses</h2>
                    <button
                        @click="openModal('expense')"
                        class="px-4 py-2 bg-red-500 text-white rounded"
                    >
                        + Add Expense
                    </button>
                </div>
                <Table
                    :columns="expenseColumns"
                    :data="formattedExpenseData"
                    :tableWidth="tableWidth"
                    :maxHeight="maxHeight"
                    @delete="(id) => deleteTransaction(id, 'expense')"
                />
            </div>
        </div>

        <!-- Modal Component -->
        <Modal :show="isModalOpen" @close="closeModal">
            <template #header>
                <h2 class="text-lg font-semibold">{{ modalTitle }}</h2>
            </template>

            <template #body>
                <div class="p-4">
                    <label class="block mb-2">Amount:</label>
                    <input
                        v-model="newTransaction.amount"
                        type="number"
                        class="border p-2 w-full"
                        placeholder="Enter amount"
                    />

                    <label class="block mt-2 mb-2">Category:</label>
                    <input
                        v-model="newTransaction.category"
                        type="text"
                        class="border p-2 w-full"
                        placeholder="Enter category"
                    />

                    <label class="block mt-2 mb-2">Description:</label>
                    <input
                        v-model="newTransaction.description"
                        type="text"
                        class="border p-2 w-full"
                        placeholder="Enter description"
                    />

                    <button
                        @click="saveTransaction"
                        class="mt-4 px-4 py-2 bg-blue-500 text-white rounded"
                    >
                        Save
                    </button>
                </div>
            </template>
        </Modal>
    </div>
</template>

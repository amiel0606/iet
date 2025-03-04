<script setup>
import { ref, computed, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";
import Table from "@/Components/Table.vue";
import Modal from "@/Components/Modal.vue";
import Notification from "@/Components/Notification.vue";
import Search from "@/Components/Search.vue";
import DatePicker from "@/Components/DatePicker.vue";
import TagSelector from "@/Components/TagSelector.vue";
import * as XLSX from "xlsx";

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

const searchQuery = ref("");
const selectedDate = ref("");
const selectedTags = ref([]);

// Function to fetch search results
const fetchResults = async () => {
    try {
        const response = await axios.get("/incomes/search", {
            params: {
                search: searchQuery.value,
                date: selectedDate.value,
                tags: selectedTags.value.join(","), // Convert array to string
            },
        });
        incomes.value = response.data;
    } catch (error) {
        console.error("Error fetching search results:", error);
    }
};

// Watch for changes in search inputs
watch([searchQuery, selectedDate, selectedTags], fetchResults);

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
const transactionType = ref("");
const newTransaction = ref({
    amount: "",
    category: "",
    description: "",
    id: null,
});

const openModal = (type, transaction = null) => {
    if (type === "editIncome" || type === "editExpense") {
        modalTitle.value = `Edit ${
            type.charAt(4).toUpperCase() + type.slice(5)
        }`;
        transactionType.value = type;
        if (transaction) {
            newTransaction.value = { ...transaction };
        }
    } else {
        modalTitle.value = `Add ${
            type.charAt(0).toUpperCase() + type.slice(1)
        }`;
        transactionType.value = type;
        newTransaction.value = {
            amount: "",
            category: "",
            description: "",
            id: null,
        };
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    newTransaction.value = {
        amount: "",
        category: "",
        description: "",
        id: null,
    };
    modalTitle.value = "";
};

const saveTransaction = async () => {
    try {
        const url =
            transactionType.value === "income" || transactionType.value === "editIncome"
                ? newTransaction.value.id
                    ? `/incomes/update/${newTransaction.value.id}`
                    : "/incomes/create"
                : newTransaction.value.id
                    ? `/expenses/update/${newTransaction.value.id}`
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

            if (transactionType.value === "income" || transactionType.value === "editIncome") {
                if (newTransaction.value.id) {
                    incomes.value = incomes.value.map((income) =>
                        income.id === newTransaction.value.id ? newRecord : income
                    );
                } else {
                    incomes.value = [...incomes.value, newRecord];
                }
            } else {
                if (newTransaction.value.id) {
                    expenses.value = expenses.value.map((expense) =>
                        expense.id === newTransaction.value.id ? newRecord : expense
                    );
                } else {
                    expenses.value = [...expenses.value, newRecord];
                }
            }

            showNotification(
                `${transactionType.value} saved successfully!`,
                "success"
            );
        }

        closeModal();
    } catch (error) {
        console.error("Error saving transaction:", error.response?.data || error);
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
        const response = await axios.post(url, { _method: "DELETE" });
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

const editTransaction = (id, type) => {
    const url =
        type === "editIncome" ? `/incomes/find/${id}` : `/expenses/find/${id}`;

    axios
        .get(url)
        .then((response) => {
            if (response.status === 200) {
                const transaction = response.data;
                openModal(type, {
                    id: transaction.id,
                    amount: transaction.amount,
                    category: transaction.category,
                    description: transaction.description,
                });
            }
        })
        .catch((error) => {
            console.error("There was an error fetching the data:", error);
        });
};

const isExportDropdownOpen = ref({
    income: false,
    expense: false,
});

const toggleExportDropdown = (type) => {
    isExportDropdownOpen.value[type] = !isExportDropdownOpen.value[type];
};

const exportToExcel = async (type) => {
    let dataToExport = [];
    let filename = "transactions.xlsx";

    if (type === "income") {
        dataToExport = incomes.value;
        filename = "income.xlsx";
    } else if (type === "expense") {
        dataToExport = expenses.value;
        filename = "expenses.xlsx";
    } else {
        dataToExport = [...incomes.value, ...expenses.value];
    }

    const ws = XLSX.utils.json_to_sheet(dataToExport);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Transactions");
    XLSX.writeFile(wb, filename);

    isExportDropdownOpen.value[type] = false;
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

        <!-- Filter Section -->
        <div class="w-full max-w-4xl mb-6 flex gap-4 items-center">
            <Search v-model:search="searchQuery" />
            <DatePicker v-model:date="selectedDate" />
            <TagSelector
                v-model="selectedTags"
                :options="['zxcqwwqq', 'weqz', 'qweqw', 'qwzx', 'asdaww']"
            />
        </div>

        <div class="w-full max-w-4xl">
            <!-- Income Section -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2 w-full">
                    <h2 class="text-xl font-semibold">Incomes</h2>
                    <div class="flex gap-4">
                        <button
                            @click="openModal('income')"
                            class="px-4 py-2 bg-green-500 text-white rounded"
                        >
                            + Add Income
                        </button>
                        <div class="relative">
                            <button
                                @click="toggleExportDropdown('income')"
                                class="px-4 py-2 bg-blue-500 text-white rounded"
                            >
                                Export ▼
                            </button>
                            <div
                                v-if="isExportDropdownOpen.income"
                                class="absolute right-0 bg-white border rounded shadow-md w-48 mt-1 z-10"
                            >
                                <button
                                    @click="exportToExcel('income')"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100"
                                >
                                    Export Income
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <Table
                    :columns="incomeColumns"
                    :data="formattedIncomeData"
                    :tableWidth="tableWidth"
                    :maxHeight="maxHeight"
                    @edit="(id) => editTransaction(id, 'editIncome')"
                    @delete="(id) => deleteTransaction(id, 'income')"
                />
            </div>

            <!-- Expenses Section -->
            <div class="flex justify-between items-center mb-2 w-full">
                <h2 class="text-xl font-semibold">Expenses</h2>
                <div class="flex gap-4">
                    <button
                        @click="openModal('expense')"
                        class="px-4 py-2 bg-red-500 text-white rounded"
                    >
                        + Add Expense
                    </button>
                    <div class="relative">
                        <button
                            @click="toggleExportDropdown('expense')"
                            class="px-4 py-2 bg-blue-500 text-white rounded"
                        >
                            Export ▼
                        </button>
                        <div
                            v-if="isExportDropdownOpen.expense"
                            class="absolute right-0 bg-white border rounded shadow-md w-48 mt-1 z-10"
                        >
                            <button
                                @click="exportToExcel('expense')"
                                class="w-full text-left px-4 py-2 hover:bg-gray-100"
                            >
                                Export Expenses
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <Table
                :columns="expenseColumns"
                :data="formattedExpenseData"
                :tableWidth="tableWidth"
                :maxHeight="maxHeight"
                @edit="(id) => editTransaction(id, 'editExpense')"
                @delete="(id) => deleteTransaction(id, 'expense')"
            />
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

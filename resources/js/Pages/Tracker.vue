<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
// import Modal from "@/Components/Modal.vue";

const props = usePage().props; 


const incomes = computed(() => props.incomes || []); 

const incomeColumns = ["Date", "Amount", "Category", "Description", "Actions"];

const formattedIncomeData = computed(() =>
    incomes.value.map((income) => ({
        Date: income.date,
        Amount: income.amount,
        Category: income.category,
        Description: income.description,
        Actions: "",
    }))
);
</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-6">Transaction Tracker</h1>

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
            <Table :columns="incomeColumns" :data="formattedIncomeData" />

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
            <Table :columns="incomeColumns" :data="formattedIncomeData" />

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

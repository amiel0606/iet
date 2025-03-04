<template>
    <div
        :style="{ width: tableWidth, maxHeight: maxHeight }"
        class="overflow-auto border rounded-lg"
    >
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th
                        v-for="col in columns"
                        :key="col"
                        class="p-2 border bg-gray-200 font-semibold"
                        :class="{ 'w-20 text-center': col === 'Actions' }"
                    >
                        {{ col }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="row in data" :key="row.id">
                    <td
                        v-for="(value, key) in row"
                        :key="key"
                        class="p-2 border text-left"
                        :class="{ 'text-center w-20': key === 'Actions' }"
                    >
                        <template v-if="key === 'Actions'">
                            <button
                                @click.prevent="$emit('edit', row.id)"
                                class="text-blue-500 mx-1"
                            >
                                <font-awesome-icon :icon="['fas', 'edit']" />
                            </button>
                            <button
                                @click.prevent="$emit('delete', row.id)"
                                class="text-red-500 mx-1"
                            >
                                <font-awesome-icon :icon="['fas', 'trash']" />
                            </button>
                        </template>

                        <template v-else>
                            {{ value }}
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
const props = defineProps({
    columns: Array,
    data: Array,
    tableWidth: {
        type: String,
        default: "100%",
    },
    maxHeight: {
        type: String,
        default: "400px",
    },
});

const emit = defineEmits(["edit", "delete"]);

</script>

<style scoped>
th,
td {
    white-space: nowrap;
}

th:not(:last-child),
td:not(:last-child) {
    width: 5%;
}

th:last-child,
td:last-child {
    width: 1%;
}
</style>

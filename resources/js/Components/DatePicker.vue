<template>
    <div class="flex items-center">
        <input
            v-model="selectedDate"
            type="date"
            class="border p-2 w-64"
            @change="$emit('update:date', selectedDate)"
        />
    </div>
</template>

<script setup>
import { ref } from "vue";

const selectedDate = ref("");
</script>

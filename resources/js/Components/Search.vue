<template>
    <div class="flex items-center">
        <input
            v-model="searchQuery"
            @input="$emit('update:search', searchQuery)"
            type="text"
            class="border p-2 w-64"
            placeholder="Search by description"
        />
    </div>
</template>

<script setup>
import { ref } from "vue";

const searchQuery = ref("");
</script>

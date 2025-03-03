<template>
    <div
        :style="{
            backgroundColor: color,
            width: width + 'px',
            height: height + 'px',
        }"
        class="rounded-lg shadow-md flex items-center justify-center p-4"
    >
        <slot></slot>
    </div>
</template>

<script setup>
import { defineProps } from "vue";

defineProps({
    color: { type: String, default: "#f3f4f6" },
    width: { type: Number, default: 200 },
    height: { type: Number, default: 100 },
});
</script>

<style scoped>
div {
    transition: all 0.3s ease;
}
</style>

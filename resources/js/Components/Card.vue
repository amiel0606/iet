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
import { computed } from 'vue';

const props = defineProps({
    color: String,
    width: [Number, String],  // Allow both Number and String
    height: [Number, String]
});

// Convert width and height to numbers (if necessary)
const width = computed(() => Number(props.width));
const height = computed(() => Number(props.height));
</script>
<style scoped>
div {
    transition: all 0.3s ease;
}
</style>

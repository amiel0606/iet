<template>
    <transition name="fade">
        <div
            v-if="visible"
            :class="['notification', typeClass]"
            @click="closeNotification"
        >
            {{ message }}
        </div>
    </transition>
</template>

<script setup>
import { ref, onMounted } from "vue";

const props = defineProps({
    message: String,
    type: {
        type: String,
        default: "success",
    },
    duration: {
        type: Number,
        default: 3000,
    },
});

const visible = ref(true);
const typeClass = ref("");

onMounted(() => {
    typeClass.value = {
        success: "bg-green-500 text-white",
        warning: "bg-yellow-500 text-white",
        danger: "bg-red-500 text-white",
    }[props.type] || "bg-gray-500 text-white";

    setTimeout(() => {
        visible.value = false;
    }, props.duration);
});

const closeNotification = () => {
    visible.value = false;
};
</script>

<style scoped>
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 10px 20px;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    cursor: pointer;
}
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

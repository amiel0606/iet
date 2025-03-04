<script setup>
import { ref, computed, defineProps, defineEmits } from "vue";

const props = defineProps({
    modelValue: Array,
    options: Array,
});

const emit = defineEmits(["update:modelValue"]);

const isOpen = ref(false);
const searchQuery = ref("");

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const selectTag = (tag) => {
    const updatedTags = [...props.modelValue];
    if (updatedTags.includes(tag)) {
        updatedTags.splice(updatedTags.indexOf(tag), 1);
    } else {
        updatedTags.push(tag);
    }
    emit("update:modelValue", updatedTags);
};

const removeTag = (tag) => {
    emit(
        "update:modelValue",
        props.modelValue.filter((t) => t !== tag)
    );
};

const filteredOptions = computed(() => {
    return props.options.filter((tag) =>
        tag.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});
</script>

<template>
    <div class="relative w-full">
        <div class="border p-2 rounded cursor-pointer" @click="toggleDropdown">
            <div class="selected-tags-container">
                <span v-if="modelValue.length === 0" class="text-gray-400">
                    Select tags...
                </span>
                <span
                    v-for="tag in modelValue"
                    :key="tag"
                    class="bg-blue-500 text-white px-2 py-1 rounded flex items-center gap-1"
                >
                    {{ tag }}
                    <button @click.stop="removeTag(tag)" class="ml-1 text-xs">
                        ✕
                    </button>
                </span>
            </div>
        </div>

        <div
            v-if="isOpen"
            class="absolute bg-white border rounded shadow-md w-full mt-1 max-h-40 overflow-auto z-10"
        >
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search tags..."
                class="w-full p-2 border-b"
            />
            <div class="p-2">
                <div
                    v-for="tag in filteredOptions"
                    :key="tag"
                    @click="selectTag(tag)"
                    class="p-2 hover:bg-blue-100 cursor-pointer rounded flex justify-between"
                >
                    {{ tag }}
                    <span v-if="modelValue.includes(tag)">✔</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.selected-tags-container {
    max-height: 50px;
    overflow-y: auto;
    border: 1px solid #ccc;
    padding: 4px;
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
</style>

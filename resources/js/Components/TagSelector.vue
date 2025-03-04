<template>
    <div class="relative w-full">
        <input
            v-model="newTag"
            @focus="toggleDropdown(true)"
            @blur="toggleDropdown(false)"
            @input="onInputChange"
            @keydown.enter.prevent="addTag"
            @keydown.delete="removeTagOnDelete"
            type="text"
            class="border p-2 w-full rounded-lg"
            placeholder="Select or type tags"
        />

        <div
            class="absolute inset-0 flex items-center gap-2 pl-2 pointer-events-none"
        >
            <span
                v-for="(tag, index) in selectedTags"
                :key="index"
                class="bg-blue-200 text-blue-800 px-2 py-1 rounded-full flex items-center gap-1 pointer-events-auto"
            >
                {{ tag }}
                <button
                    @click="removeTag(index)"
                    class="text-blue-500 hover:text-blue-700 text-sm"
                >
                    &times;
                </button>
            </span>
        </div>

        <!-- Dropdown -->
        <div
            v-if="dropdownVisible && filteredTags.length > 0"
            class="absolute z-10 w-full bg-white border border-gray-300 mt-1 rounded-lg shadow-lg"
        >
            <ul class="max-h-48 overflow-auto">
                <li
                    v-for="(tag, index) in filteredTags"
                    :key="index"
                    @click="selectTag(tag)"
                    class="p-2 hover:bg-gray-100 cursor-pointer"
                >
                    {{ tag }}
                </li>
            </ul>
        </div>

        <input
            type="hidden"
            :value="selectedTags.join(',')"
            name="tags"
        />
    </div>
</template>

<script setup>
import { ref, computed } from "vue";

const tagsList = ref([
    "Food",
    "Transport",
    "Entertainment",
    "Health",
    "Education",
    "Shopping",
]); 

const newTag = ref(""); 
const selectedTags = ref([]); 
const dropdownVisible = ref(false);

const filteredTags = computed(() => {
    return tagsList.value.filter(
        (tag) =>
            tag.toLowerCase().includes(newTag.value.toLowerCase()) &&
            !selectedTags.value.includes(tag)
    );
});

const toggleDropdown = (visible) => {
    dropdownVisible.value = visible;
    if (!visible) newTag.value = ""; 
};

const addTag = () => {
    const tag = newTag.value.trim();
    if (
        tag &&
        !selectedTags.value.includes(tag) &&
        tagsList.value.includes(tag)
    ) {
        selectedTags.value.push(tag);
    }
    newTag.value = ""; 
    toggleDropdown(false); 
};

const selectTag = (tag) => {
    if (!selectedTags.value.includes(tag)) {
        selectedTags.value.push(tag);
        newTag.value = ""; 
        toggleDropdown(false);
    }
};

const removeTag = (index) => {
    selectedTags.value.splice(index, 1);
};

const removeTagOnDelete = (event) => {
    if (
        event.key === "Backspace" &&
        !newTag.value &&
        selectedTags.value.length > 0
    ) {
        selectedTags.value.pop(); 
    }
};

const emitTags = () => {
    emit("update:tags", selectedTags.value);
};
</script>

<style scoped>
input {
    padding-left: 120px; 
}

span {
    pointer-events: auto; 
}

ul {
    max-height: 150px;
    overflow-y: auto;
}

li {
    cursor: pointer;
}
</style>

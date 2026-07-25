<script setup>
import { Search } from 'lucide-vue-next';
import { reactive } from 'vue';

const form = reactive({
    search: ''
})

const emit = defineEmits(['emitFilters'])
</script>

<template>
    <div class="flex flex-wrap items-center rounded-xl border bg-white/40 border-gray-400 py-1 px-4 focus-within:border-blue-500 transition-all duration-300">
        <Search class="text-gray-600 dark:text-gray-300"/>
        <input
            name="search-contact"
            v-model="form.search"
            placeholder="Search template"  
            class="flex-1 dark:placeholder-gray-300 border-0 bg-transparent outline-none focus:outline-none focus:ring-0 focus:border-0 shadow-none placeholder-gray-400"
        />
    </div>
</template>
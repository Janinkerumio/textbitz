<script setup>
import OptionsDropdown from '@/Components/Dropdown/OptionsDropdown.vue';
import { SlidersHorizontal } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    statsForSort: {
        type: Object,
        default: {}
    }
})

const isFilterOptionsShown = ref(false)

const emit = defineEmits(['appliedSort'])

const selectedSort = (value) => {
    emit('appliedSort', value)
    isFilterOptionsShown.value = false
}
</script>

<template>
    <div class="flex justify-end mt-6 px-4">
        <div class="relative">
            <button @click="isFilterOptionsShown = !isFilterOptionsShown" class="bg-gray-300 dark:bg-gray-600 text-gray-800  dark:text-gray-300 rounded-xl p-2 flex flex-row gap-3 items-center active:opacity-75">
                <SlidersHorizontal :size="16" :stroke-width="2.5"/>
                <p class="text-sm">Filters</p>
            </button>
            <OptionsDropdown 
                :isOpen="isFilterOptionsShown"
                position-class="top-full"
                width-class="w-32"
            >
                <div class="flex flex-col gap-1 py-2 px-4">
                    <button 
                        v-for="(filter, i) in statsForSort"
                        :key="i"
                        @click="selectedSort(filter.status)"
                        class="flex justify-between items-center text-sm text-gray-800 dark:text-gray-400 active:bg-gray-200 dark:active:bg-gray-600 rounded-lg"
                    >
                        <p>{{ filter.status }}</p>
                        <span class="text-xs">{{ filter.count }}</span>
                    </button>
                </div>
            </OptionsDropdown>
        </div>
    </div>
</template>
<script setup>
import BottomModal from '@/Components/Modal/BottomModal.vue';
import { ref, reactive, capitalize } from 'vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue', 'appliedSort'])

const allOptions = ref([
    'all', 'sent', 'failed', 'queued'
])

const filterForm = reactive({
    sort: 'all'
})

const selectOption = (option) => {
    if(preventIfActivatedRadioSelected(option)) return

    handleSortRadio(option)
    
    selectedSort()
}

const selectedSort = () => {
    emit('appliedSort', { 
        sort: filterForm.sort === 'all' ? null : filterForm.sort
    })
    emit('update:modelValue', false)
}

const preventIfActivatedRadioSelected = (option) => {
    return filterForm.sort === option
}

const handleSortRadio = (option) => {
    filterForm.sort = option
}
</script>

<template>
    <BottomModal
        :model-value="modelValue"
        @update:model-value="emit('update:modelValue', $event)"
    >
        <div class="mb-2">
            <h1 class="font-semibold text-lg mb-2 dark:text-gray-200">Filters</h1>
        </div>
        <div class="p-2 flex flex-row items-center gap-2 bg-white shadow dark:bg-gray-600/60 rounded-xl">
            <p class="text-gray-500 dark:text-gray-400">Status: </p>
            <div class="flex flex-row rounded-lg bg-gray-200">
                <label v-for="(filter, i) in allOptions" :key="i"
                    @click="selectOption(filter)"
                    class="relative rounded-lg flex-1 cursor-pointer px-2 py-1"
                    :class="filterForm.sort === filter
                                ? 'text-blue-100 dark:text-blue-200 bg-blue-500'
                                : 'text-gray-800 dark:text-gray-600'"
                >
                    <div class="flex px-1 items-center text-sm rounded-lg">
                        <p class="text-base">{{ capitalize(filter) }}</p>
                    </div>
                </label>
            </div>
        </div>
    </BottomModal>
</template>
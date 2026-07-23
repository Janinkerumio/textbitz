<script setup>
import { Funnel } from 'lucide-vue-next';
import { ref, reactive, watch, computed } from 'vue';
import capitalize from '@/helpers/capitalize';

const props = defineProps({
    statsForSort: {
        type: Object,
        default: {}
    }
})

const allStatsCount = computed(() => props.statsForSort.reduce((sum, s) => sum + s.count, 0))
const lastRadio = ref('')
const DATE_SORT = '__date__'
const isDatePickerShown = ref(false)

const emit = defineEmits(['appliedSort'])

const filterForm = reactive({
    sort: null,
    date: null
})

const confirmDate = () => {
    if (!filterForm.date) return
    isDatePickerShown.value = false
    selectedSort()
}

const selectOption = (option) => {
    if(preventIfActivatedRadioSelected(option)) return
    if(revertIfNoDateFilled(option.isDate)) return
    if(handleIfRadioIsDate(option.isDate)) return

    handleSortRadio(option.status)
    
    selectedSort()
}

const selectedSort = () => {
    emit('appliedSort', { 
        sort: filterForm.sort === DATE_SORT ? null : filterForm.sort,
        date: filterForm.sort === DATE_SORT ? filterForm.date : null,
    })
}

const allOptions = computed(() => [
    { 
        status: null, 
        label: capitalize('all'), 
        count: allStatsCount.value 
    },
    ...props.statsForSort.map(f => ({ 
        status: f.status, 
        label: capitalize(f.status), 
        count: f.count 
    })),
    {
        status: DATE_SORT,
        label: 'Date',
        count: null,
        isDate: true,
    },
])

const preventIfActivatedRadioSelected = (option) => {
    return filterForm.sort === option.status && !option.isDate
}

const revertIfNoDateFilled = (flag) => {
    if(flag && isDatePickerShown.value) {
        isDatePickerShown.value = false
        filterForm.sort = lastRadio.value

        return true
    }

    return false
}

const handleIfRadioIsDate = (flag) => {
    if (flag) {
        lastRadio.value = filterForm.sort
        filterForm.sort = DATE_SORT
        isDatePickerShown.value = true
        
        return true
    }

    return false
}

const handleSortRadio = (option) => {
    filterForm.sort = option
    isDatePickerShown.value = false
    filterForm.date = null
}

watch(() => props.statsForSort, (payload) => {
    console.log(payload)
})
</script>

<template>
    <div class="flex justify-start items-center mt-6 relative group transition-all duration-100">
        <div class="p-2 flex flex-row items-center gap-2 bg-white shadow-lg dark:bg-gray-600 rounded-xl">
            <Funnel :size="22" :stroke-width="2.5" class="text-gray-500"/>
            <div class="flex flex-row rounded-lg bg-gray-200">
                <label v-for="(filter, i) in allOptions" :key="i"
                    @click.prevent="selectOption(filter)"
                    class="relative rounded-lg flex-1 cursor-pointer px-2 py-1"
                    :class="filterForm.sort === filter.status
                                ? 'text-blue-100 dark:text-blue-300 bg-blue-500'
                                : 'text-gray-800 dark:text-gray-400'"
                >
                    <div 
                        class="flex px-1 items-center text-sm rounded-lg">
                        <p class="text-base">{{ filter.label }}</p>
                        <span 
                            class="absolute top-[-10px] right-[-5px] rounded-full px-[5px] text-xs"
                            :class="filterForm.sort === filter.status
                                ? 'text-blue-600 dark:text-blue-300 bg-blue-200'
                                : 'text-gray-800 dark:text-gray-400 bg-gray-400'"
                        >{{ filter.count }}</span>
                    </div>
                </label>
            </div>
        </div>
        <Transition
            enter-active-class="transition-all duration-150 ease-out"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-100 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-1"
        >
            <div
                v-if="isDatePickerShown"
                class="absolute top-full mt-2 left-0 z-20 p-3 bg-white dark:bg-gray-600 rounded-xl shadow-lg flex flex-col gap-2"
            >
                <input
                    type="date"
                    v-model="filterForm.date"
                    class="border border-gray-200 dark:border-gray-500 rounded-lg px-2 py-1 text-base bg-transparent"
                >
                <button
                    @click="confirmDate"
                    :disabled="!filterForm.date"
                    class="text-base bg-blue-500 text-white rounded-lg px-3 py-1 disabled:opacity-40"
                >
                    Apply
                </button>
            </div>
        </Transition>
    </div>
</template>
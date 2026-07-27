<script setup>
import { reactive, watch } from 'vue'
import { debounce } from 'lodash-es'
import { colorForTag } from '@/Composables/useTagColors'
import SearchBar from '@/Components/Input/SearchBar.vue'

const props = defineProps({
    tags: {
        type: Array,
        default: []
    },
    filters: {
        type: Object,
        default: null
    },
})

const emit = defineEmits(['emitFilters'])

const form = reactive({
    search: props.filters?.search ?? '',
    tags: props.filters?.tags ?? [],
})

const removeTags = () => {
    form.tags = []
}

const emitChanges = debounce(() => {
    emit('emitFilters', {
        search: form.search,
        tags: form.tags,
    })
}, 200)

watch(form, emitChanges, { deep: true })
</script>

<template>
    <div class="sticky flex flex-col gap-2 w-full">
        <SearchBar 
            name="search_contact"
            v-model="form.search"
            placeholder="Search contact"
        />
        <div class="overflow-x-auto snap-x snap-mandatory py-2">
            <div class="flex flex-row items-center gap-2 w-max">
                <div 
                    @click="removeTags"
                    class="px-3 py-[1.5px] rounded-full border transition-colors duration-50" 
                    :class="form.tags.length === 0 ? colorForTag('All', { bordered: true, bold: true }) : 'bg-gray-300 text-gray-600'"
                >
                    All
                </div>
                <label
                    v-for="tag in tags"
                    :key="tag"
                    class="cursor-pointer"
                >
                    <input
                        type="checkbox"
                        :value="tag"
                        v-model="form.tags"
                        class="hidden"
                    >

                    <span
                        class="px-3 py-1 rounded-full border transition-colors duration-200"
                        :class="form.tags.includes(tag) ? colorForTag(tag, { bordered: true, bold: true }) : 'bg-gray-300 text-gray-600'"
                    >
                        {{ tag }}
                    </span>
                </label>
            </div>
        </div>
    </div>
</template>
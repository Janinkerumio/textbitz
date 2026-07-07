 <script setup>
import { reactive, watch } from 'vue'
import { debounce } from 'lodash-es'
import { Search } from 'lucide-vue-next'

const props = defineProps({
    tags: {
        type: Array,
        default: null
    },
    filters: {
        type: Object,
        default: null
    },
})

const emit = defineEmits(['change'])

const form = reactive({
    search: props.filters?.search ?? '',
    tags: props.filters?.tags ?? [],
})

const emitChanges = debounce(() => {
    emit('change', {
        search: form.search,
        tags: form.tags,
    })
}, 300)

watch(form, emitChanges, { deep: true })
</script>

<template>
    <div class="flex flex-col gap-2 w-full">
        <div class="flex flex-wrap items-center rounded-xl border bg-white/40 border-gray-400 py-1 px-4 focus-within:border-blue-500 transition-all duration-300">
            <Search class="text-gray-600"/>
            <input placeholder="Search contacts" name="search-contact" class="flex-1 border-0 bg-transparent outline-none focus:outline-none focus:ring-0 focus:border-0 shadow-none placeholder-gray-400"/>
        </div>
        <div class="flex flex-wrap gap-1">
            <div class="flex flex-wrap gap-2">
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
                        class="px-3 py-1 rounded-full border"
                    >
                        {{ tag }}
                    </span>
                </label>
            </div>
        </div>
    </div>
</template>
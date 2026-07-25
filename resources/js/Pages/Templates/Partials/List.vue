<script setup>
import { fetchTemplates } from '@/data/api/fetchViaAxios';
import { computed, ref, useTemplateRef } from 'vue';
import { createInfiniteScroll } from '@/Composables/createInfiniteScroll';
import { EllipsisVertical } from 'lucide-vue-next';

const props = defineProps({
    searchQuery: {
        type: Object,
        default: {}
    }
})

const expandedTemplateId = ref(new Set())
const isExpanded = (id) => expandedTemplateId.value.has(id)

const container = useTemplateRef('container')
const { items: templates, loading, onScroll } = createInfiniteScroll(
    fetchTemplates,
    computed(() => props.searchQuery),
    { distance: 10 }
)
</script>

<template>
    <div 
        ref="container"
        @scroll="onScroll(container)"
        class="mt-5 max-h-[80dvh] overflow-y-auto"
    >
        <div class="grid grid-cols-1 gap-2 h-full">
            <div v-for="template in templates"
                :key="template.id"
                class="backdrop-blur rounded-xl border bg-white/70 dark:bg-gray-500/40 border-gray-100 dark:border-gray-600 shadow-sm p-4 transition-all duration-300 ease-in-out"
            >
                <header class="w-full flex justify-start items-center gap-4">
                    <h1 class="font-bold text-gray-800">{{ template.title }}</h1>
                    <p class="text-xs p-1 bg-blue-200 text-blue-800 rounded">{{ template.category }}</p>
                </header>
                <article class="flex flex-wrap items-start w-full mt-2">
                    <p class="flex-1 text-sm text-gray-600">
                        {{ isExpanded(template.id) ? template.message : template.message.slice(0, 40) + '...' }}
                    </p>
                    <EllipsisVertical :size="16" class="text-gray-600"/>
                </article>
            </div>
            <div v-if="loading" class="flex justify-center">

            </div>
        </div>
    </div>
</template>
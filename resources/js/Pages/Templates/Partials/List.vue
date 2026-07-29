<script setup>
import { fetchTemplates } from '@/data/api/fetchViaAxios';
import { computed, ref, useTemplateRef } from 'vue';
import { createInfiniteScroll } from '@/helpers/createInfiniteScroll';
import { EllipsisVertical, Copy } from 'lucide-vue-next';
import ActionButton from '@/Components/Button/ActionButton.vue';
import IconButton from '@/Components/Button/IconButton.vue';
import CardColList from '@/Components/Skeleton/CardColList.vue';
import { useClipboard } from '@/Composables/useClipboard';
import { useDataList } from '@/Composables/useDataList';

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

const { prependData, removeData } = useDataList(templates)

const { copy } = useClipboard()

const expand = (id) => {
    if(expandedTemplateId.value.has(id)) {
        expandedTemplateId.value.delete(id)
    } else {
        expandedTemplateId.value = new Set()
        expandedTemplateId.value.add(id)
    }

    expandedTemplateId.value = new Set(expandedTemplateId.value)
}

defineExpose({
    prependData,
    removeData
})
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
                @click="expand(template.id)"
                class="backdrop-blur rounded-xl border bg-white/70 dark:bg-gray-500/40 border-gray-100 dark:border-gray-600 shadow-sm p-4 transition-all duration-300 ease-in-out"
            >
                <header class="w-full flex justify-start items-center gap-4">
                    <h1 class="font-bold text-gray-800 dark:text-gray-200">{{ template.title }}</h1>
                    <p class="text-xs p-1 bg-blue-200 dark:bg-blue-500/90 text-blue-800 dark:text-blue-200 rounded">{{ template.category }}</p>
                </header>
                <article class="flex flex-wrap items-start w-full mt-2">
                    <p class="flex-1 text-sm text-gray-600 dark:text-gray-300">
                        {{ isExpanded(template.id) ? template.message : template.message.slice(0, 40) + '...' }}
                    </p>
                    <EllipsisVertical :size="16" class="text-gray-600 dark:text-gray-400"/>
                </article>
                <Transition
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 max-h-0"
                    enter-to-class="opacity-100 max-h-40"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 max-h-40"
                    leave-to-class="opacity-0 max-h-0"
                >
                    <div v-if="isExpanded(template.id)" class="w-full px-2 py-1 bg-gray-100 dark:bg-gray-500/40 rounded-lg border-[0.5px] border-gray-100 dark:border-gray-500 flex justify-between items-center mt-2">
                        <div class="flex flex-wrap gap-2">
                            <ActionButton color-class="blue"
                                @click.stop="$emit('editTemplate', template.id)"
                            >
                                Edit
                            </ActionButton>
                            <ActionButton color-class="red"
                                @click.stop="$emit('deleteTemplate', template.id)"
                            >
                                Delete
                            </ActionButton>
                        </div>
                        <div class="flex flex-row gap-4 bg-gray-50 dark:bg-gray-800/50 border-[0.5px] border-gray-50 dark:border-gray-500 py-1 px-2 rounded">
                            <ActionButton
                                @click.stop="$emit('useTemplate', template.id)"
                            >
                                Use Template
                            </ActionButton>
                            <IconButton :icon="Copy" @click.stop="copy(template.message)"/>
                        </div>
                    </div>
                </Transition>
            </div>
            <div v-if="loading" class="flex justify-center">
                <CardColList />
            </div>
        </div>
    </div>
</template>
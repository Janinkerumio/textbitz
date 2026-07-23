<script setup>
import { watch, computed, ref, useTemplateRef } from 'vue';
import { fetchHistory } from '@/data/api/fetchViaAxios';
import { createInfiniteScroll } from '@/Composables/createInfiniteScroll';
import { capitalize } from 'vue';
import { blastStatus } from '@/utils/statusIndicator';
import { Ellipsis } from 'lucide-vue-next';
import { friendlyDate } from '@/helpers/date';
import DetailsCardColList from '@/Components/Skeleton/DetailsCardColList.vue';
import ActionButton from '@/Components/Button/ActionButton.vue';

const props = defineProps({
    sortBy: {
        type: Object,
        default: {}
    }
})

const expandedHistoryId = ref(new Set())
const isExpanded = (id) => expandedHistoryId.value.has(id)

const container = useTemplateRef('container')
const { items: histories, loading, onScroll } = createInfiniteScroll(
    fetchHistory,
    computed(() => props.sortBy)
)

const expand = (id) => {
    if (expandedHistoryId.value.has(id)) {
        expandedHistoryId.value.delete(id)
    } else {
        expandedHistoryId.value = new Set()
        expandedHistoryId.value.add(id)
    }
    
    expandedHistoryId.value = new Set(expandedHistoryId.value)
}
</script>

<template>
    <div 
        ref="container"
        @scroll="onScroll(container)"
        class="mt-5 max-h-[80dvh] overflow-y-auto"
    >
        <div class="grid grid-cols-1 gap-2 h-full">
            <div v-for="history in histories"
                :key="history.id"
                @click="expand(history.id)"
                class="backdrop-blur rounded-xl border bg-white/70 dark:bg-gray-500/40 border-gray-100 dark:border-gray-600 shadow-sm p-2 transition-all duration-300 ease-in-out"
            >
                <header class="w-full flex justify-start mt-2">
                    <p class="text-sm flex flex-row gap-2 items-center" :class="blastStatus[history.status]">
                        <i class="fa-solid fa-circle text-[8px]"></i>
                        {{ capitalize(history.status) }}
                    </p>
                </header>
                <article class="px-2">
                    <p class="text-gray-800 font-bold">{{ history.template.title }}</p>
                </article>
                <section class="flex flex-row justify-between gap-5 p-2">
                    <p class="flex-1 text-gray-800 text-sm">
                        {{ isExpanded(history.id) ? history.blast : history.blast.slice(0, 100) + '...' }}
                    </p>
                    <Ellipsis :size="24" class="text-gray-600"/>
                </section>
                <section class="flex flex-wrap gap-4 px-4 py-2 border-t-[0.25px] border-gray-200 w-full">
                    <p class="text-sm text-gray-600">{{ friendlyDate('shortDate', history.last_sent_at) }}</p>
                    <p class="text-sm text-gray-600">{{ history.recipients.length }} recipients</p>
                </section>
                <Transition
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 max-h-0"
                    enter-to-class="opacity-100 max-h-40"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 max-h-40"
                    leave-to-class="opacity-0 max-h-0"
                >
                    <section v-if="isExpanded(history.id)" class="flex gap-3 px-2 pb-2 overflow-hidden">
                        <ActionButton
                            @click.stop="$emit('viewRecipients', history.id)"
                        >
                            Recipients
                        </ActionButton>
                        <ActionButton v-if="history.status !== 'queued'" color-class="blue"
                            @click.stop="$emit('resend', history.id)"
                        >
                            Resend
                        </ActionButton>
                        <ActionButton v-if="history.status !== 'queued'"
                            @click.stop="$emit('duplicate', history.id)"
                        >
                            Duplicate
                        </ActionButton>
                        <ActionButton v-if="history.status !== 'queued'" color-class="red"
                            @click.stop="$emit('delete', history.id)"
                        >
                            Delete
                        </ActionButton>
                    </section>
                </Transition>
            </div>
            <div v-if="loading" class="flex justify-center">
                <DetailsCardColList />
            </div>
        </div>
    </div>
</template>
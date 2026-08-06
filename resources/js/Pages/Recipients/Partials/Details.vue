<script setup>
import { capitalize } from 'vue';
import { blastStatus } from '@/utils/statusIndicator';
import { friendlyDate } from '@/helpers/date';
import ActionButton from '@/Components/Button/ActionButton.vue';

const props = defineProps({
    history: {
        type: Object,
        default: {}
    }
})
</script>

<template>
    <div class="p-4 sm:rounded-lg sm:p-8">
        <div class="backdrop-blur rounded-xl border bg-white/70 dark:bg-gray-500/40 border-gray-100 dark:border-gray-600 shadow-sm p-2 transition-all duration-300 ease-in-out">
            <header class="w-full flex items-center justify-between pr-4 mt-2">
                <p class="text-sm flex flex-row gap-2 items-center" :class="blastStatus[history.status]">
                    <i class="fa-solid fa-circle text-[8px]"></i>
                    {{ capitalize(history.status) }}
                </p>
                <ActionButton v-if="(history.sent_count - history.failed_count) > 0 && history.status !== 'queued'" color-class="blue"
                    @click="$emit('resendToFailed', history.id)"
                >
                    Resend To Failed
                </ActionButton>
            </header>
            <article class="px-2">
                <p class="text-gray-800 dark:text-gray-200 font-bold">{{ (history.template?.title ?? history?.title) ?? 'No title' }}</p>
            </article>
            <section class="flex flex-row justify-between gap-5 p-2">
                <p class="flex-1 text-gray-800 dark:text-gray-300 text-sm">
                    {{ history.blast }}
                </p>
                <Ellipsis :size="24" class="text-gray-600 dark:text-gray-300"/>
            </section>
            <section class="flex flex-wrap gap-4 px-4 pt-2 border-t-[0.25px] border-gray-200 dark:border-gray-500 text-gray-600 dark:text-gray-400 w-full">
                <p class="text-sm">{{ friendlyDate('shortDate', history.last_sent_at) }}</p>
                <p class="text-sm">{{ history.recipients }} recipients</p>
            </section>
            <section class="flex flex-wrap gap-4 px-4 pb-2">
                <p class="text-xs text-emerald-500">{{ history.sent_count }} recipients</p>
                <p class="text-xs text-red-500">{{ history.failed_count }} recipients</p>
            </section>
            <section class="flex gap-3 px-2 pb-2 overflow-hidden">
                <ActionButton v-if="history.status !== 'queued'" color-class="blue"
                    @click.stop="$emit('resend', history.id)"
                >
                    Resend
                </ActionButton>
                <ActionButton v-if="history.status !== 'queued' && history.template"
                    @click.stop="$emit('duplicate', history.id)"
                >
                    Duplicate
                </ActionButton>
                <ActionButton color-class="red"
                    @click.stop="$emit('delete', history.id)"
                >
                    Delete
                </ActionButton>
            </section>
        </div>
    </div>
</template>
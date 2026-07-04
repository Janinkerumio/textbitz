<script setup>
import { InfiniteScroll } from '@inertiajs/vue3';
import { randomColor } from '@/Composables/useTagColors';

const props = defineProps({
    contacts: Object
})
</script>

<template>
    <InfiniteScroll data="contacts" class="mt-10">
        <div class="grid grid-cols-1 gap-2">
            <div v-for="contact in contacts.data" :key="contact.id" class="bg-white/70 dark:bg-gray-500/40 backdrop-blur rounded-xl border border-gray-100 dark:border-gray-600 shadow-sm p-4">
                <div class="flex flex-col">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-300">{{ contact.contact_name }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ contact.phone_num }}</p>
                    <div class="flex flex-wrap gap-2 mt-1">
                        <span v-for="tag in contact.tags" :key="tag" class="text-xs px-2 py-0.5 rounded-full" :class="randomColor()">
                            {{ tag }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <template #next="{ loading }">
            <div v-if="loading" class="flex justify-center py-4">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 dark:border-blue-400"></div>
            </div>
        </template>
    </InfiniteScroll>
</template>
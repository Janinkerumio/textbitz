<script setup>
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import EmptyHistory from '@/Components/Placeholders/EmptyHistory.vue';
import historyMockData from '@/data/history';
import List from './Partials/List.vue';
import Filters from './Partials/Filters.vue';

const stats = computed(() => Object.entries(
    historyMockData.reduce((acc, item) => {
        acc[item.status] = (acc[item.status] || 0) + 1
        return acc
    }, {})
).map(([status, count]) => ({ status, count })))
</script>

<template>
    <Head title="History" />

    <AppLayout page-title="History">
        <template #content>
            <Filters 
                :stats-for-sort="stats"
            />
            <List v-if="historyMockData.length !== 0"
                :history-data="historyMockData"
            />
            <div v-else class="flex min-h-screen items-center justify-center">
                <EmptyHistory/>
            </div>
        </template>
    </AppLayout>
</template>
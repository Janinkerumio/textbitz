<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Plus } from 'lucide-vue-next';
import EmptyTemplates from '@/Components/Placeholders/EmptyTemplates.vue';
import List from './Partials/List.vue';
import SearchAndFilters from './Partials/SearchAndFilters.vue';

const props = defineProps({
    hasData: {
        type: Number,
        default: 0
    }
})
</script>

<template>
    <Head title="Templates" />

    <AppLayout page-title="Templates" :additional-text="`${hasData ?? 0} reusable templates`" :head-button-icon="Plus">
        <template #content>
            <div v-if="hasData" class="mt-8">
                <SearchAndFilters />
                <List />
            </div>
            <div v-else class="flex min-h-screen items-center justify-center">
                <EmptyTemplates />
            </div>
        </template>
        <template #modal>

        </template>
    </AppLayout>
</template>
<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import EmptyHistory from '@/Components/Placeholders/EmptyHistory.vue';
import List from './Partials/List.vue';
import Filters from './Partials/Filters.vue';
import crossPlatformToast from '@/helpers/crossPlatformToast.js';

const props = defineProps({
    stats: {
        type: Object,
        default: {}
    },
    hasData: {
        type: Boolean,
        default: false
    }
})

const toast = crossPlatformToast()

const appliedSortFilter = ref({})

const handleSorting = (payload) => {
    appliedSortFilter.value = payload
}

const handleEmitsFromList = (id) => {
    toast.show('This function is under development')
}
</script>

<template>
    <Head title="History" />

    <AppLayout page-title="History">
        <template #content>
            <div v-if="hasData">
                <Filters 
                    :stats-for-sort="stats"
                    @applied-sort="(value) => handleSorting(value)"
                />
                <List
                    :sort-by="appliedSortFilter"
                    @view-recipients="(id) => handleEmitsFromList(id)"
                    @resend="(id) => handleEmitsFromList(id)"
                    @duplicate="(id) => handleEmitsFromList(id)"
                    @delete="(id) => handleEmitsFromList(id)"
                />
            </div>
            <div v-else class="flex min-h-[100dvh] items-center justify-center">
                <EmptyHistory/>
            </div>
        </template>
        <template #modal>

        </template>
    </AppLayout>
</template>
<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import EmptyHistory from '@/Components/Placeholders/EmptyHistory.vue';
import List from './Partials/List.vue';
import Filters from './Partials/Filters.vue';
import { dialog } from '#nativephp'

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

const appliedSortFilter = ref({})

const handleSorting = (payload) => {
    appliedSortFilter.value = payload
}

const handleEmitsFromList = async (id) => {
    await dialog.toast('This feature is under development')
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
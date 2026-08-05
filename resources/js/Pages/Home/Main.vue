<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Banner from './Partials/Banner.vue';
import Metrics from './Partials/Metrics.vue';
import History from './Partials/History.vue';

defineProps({
    recipients: {
        type: Number,
        default: 0
    },
    history: {
        type: Number,
        default: 0
    },
    contacts: {
        type: Number,
        default: 0
    },
    templates: {
        type: Number,
        default: 0
    },
})
</script>

<template>
    <Head title="Home" />

    <AppLayout page-title="Textbitz" additional-text="Manage bulk SMS campaigns">
        <template #content>
            <div class="flex flex-col min-h-[80dvh] gap-4">
                <Banner />

                <Metrics 
                    :recipients="recipients"
                    :history="history"
                    :contacts="contacts"
                    :templates="templates"
                />

                <History />
            </div>
        </template>
    </AppLayout>
</template>
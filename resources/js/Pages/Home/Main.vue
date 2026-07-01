<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Banner from './Partials/Banner.vue';
import Metrics from './Partials/Metrics.vue';


</script>

<template>
    <Head title="Home" />

    <AppLayout page-title="Textbitz" additional-text="Manage bulk SMS campaigns">
        <template #content>
            <div class="flex flex-col min-h-screen gap-4">
                <Banner />

                <Metrics />
            </div>
        </template>
    </AppLayout>
</template>
<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Compose from './Partials/Compose.vue';
import Selections from './Partials/Selections.vue';

</script>

<template>
    <Head title="Create Blast" />

    <AppLayout page-title="New Blast" additional-text="Send SMS message to contacts">
        <template #content>
            <Selections />
            <Compose />
        </template>
        <template #modal>

        </template>
    </AppLayout>
</template>
<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

</script>

<template>
    <Head title="Create Blast" />

    <AppLayout page-title="New Blast" additional-text="Send SMS message to contacts">
        <template #content>
            <div class="flex min-h-screen items-center justify-center">
                <h1>Hello World</h1>
            </div>
        </template>
    </AppLayout>
</template>
<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import EmptyHistory from '@/Components/Placeholders/EmptyHistory.vue';
</script>

<template>
    <Head title="History" />

    <AppLayout page-title="History">
        <template #content>
            <div class="flex min-h-screen items-center justify-center">
                <EmptyHistory />
            </div>
        </template>
    </AppLayout>
</template>
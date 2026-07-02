<script setup>
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeft } from 'lucide-vue-next';
</script>

<template>
    <Head title="History" />

    <AppLayout page-title="History" :head-button-icon="ArrowLeft" :head-button-action="() => { router.get(route('app.dashboard')) }">
        <template #content>
            <div class="flex min-h-screen items-center justify-center">
                <h1>Hello World</h1>
            </div>
        </template>
    </AppLayout>
</template>
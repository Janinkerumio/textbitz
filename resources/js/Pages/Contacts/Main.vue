<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <Head title="Contacts" />

    <AppLayout>
        <template #content>
            <div class="flex min-h-screen items-center justify-center">
                <h1>Hello World</h1>
            </div>
        </template>
    </AppLayout>
</template>
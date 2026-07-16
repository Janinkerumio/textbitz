<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Plus } from 'lucide-vue-next';
import EmptyTemplates from '@/Components/Placeholders/EmptyTemplates.vue';
import templatesDefault from '@/data/templates';
</script>

<template>
    <Head title="Templates" />

    <AppLayout page-title="Templates" :additional-text="`${templatesDefault.length} reusable templates`" :head-button-icon="Plus">
        <template #content>
            <div class="flex min-h-screen items-center justify-center">
                <EmptyTemplates />
            </div>
        </template>
    </AppLayout>
</template>
<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import contactsMockData from '@/data/contacts';
import { UserPlus } from 'lucide-vue-next';
</script>

<template>
    <Head title="Contacts" />

    <AppLayout pageTitle="Contacts" :additionalText="`${contactsMockData.length} total contacts`" :headButtonIcon="UserPlus">
        <template #content>
            <div class="flex min-h-screen items-center justify-center">
                <h1>Hello World</h1>
            </div>
        </template>
    </AppLayout>
</template>
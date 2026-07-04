<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { UserPlus } from 'lucide-vue-next';
import EmptyContacts from '@/Components/Placeholders/EmptyContacts.vue';
import List from './Partials/List.vue';
import contactsMockData from '@/data/contacts.js';

const props = defineProps({
    contacts: Object
})
</script>

<template>
    <Head title="Contacts" />

    <AppLayout 
        pageTitle="Contacts" 
        :additionalText="contacts.data?.length ? `${contacts?.total} total contacts` : contactsMockData.length" 
        :headButtonIcon="UserPlus"
    >
        <template #content>
            <div v-if="contacts.data?.length">
                <List :contacts="contacts ?? contactsMockData" />
            </div>
            <div v-else class="flex items-center justify-center min-h-screen">
                <EmptyContacts />
            </div>
        </template>
    </AppLayout>
</template>

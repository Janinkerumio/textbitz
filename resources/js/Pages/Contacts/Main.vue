<script setup>
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { UserPlus } from 'lucide-vue-next';
import EmptyContacts from '@/Components/Placeholders/EmptyContacts.vue';
import List from './Partials/List.vue';
import SearchAndCategorize from './Partials/SearchAndCategorize.vue';

const props = defineProps({
    contacts: Object,
    filters: Object,
    tags: Array
})

const applyFilters = (filters) => {
    router.get(route('app.contacts'), filters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['contacts', 'filters', 'tags'],
        reset: ['contacts']
    })
}
</script>

<template>
    <Head title="Contacts" />

    <AppLayout 
        pageTitle="Contacts" 
        :additionalText="`${contacts.data?.length ? contacts?.total : 0} total contacts`" 
        :headButtonIcon="UserPlus"
    >
        <template #content>
            <div class="flex flex-col w-full">
                <SearchAndCategorize 
                    class="mt-10"
                    :tags="tags",
                    :filters="filters"
                    @change="applyFilters"
                />
            </div>    
            <List v-if="contacts.total || tags" 
                :contacts="contacts"
                :key="`${filters.search}-${JSON.stringify(filters.tags)}`"
            />
            <div v-else class="flex items-center justify-center min-h-screen">
                <EmptyContacts />
            </div>
        </template>
    </AppLayout>
</template>

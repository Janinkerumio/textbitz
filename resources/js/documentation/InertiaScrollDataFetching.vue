<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { UserPlus } from 'lucide-vue-next';
import EmptyContacts from '@/Components/Placeholders/EmptyContacts.vue';
import List from './Partials/List.vue';
import SearchAndCategorize from './Partials/SearchAndFilter.vue/index.js';

const props = defineProps({
    contacts: Object,
    filters: Object,
    tags: Array
})

const cancelToken = ref(null)

const applyFilters = (filters) => {
    if(cancelToken.value) {
        cancelToken.value.cancel()
    }

    router.visit(route('app.contacts'), {
        data: filters,
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['contacts', 'filters', 'tags'],
        reset: ['contacts'],
        onCancelToken: (token) => {
            cancelToken.value = token
        },
        onFinish: () => {
            cancelToken.value = null
        }
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
                    @emitFilters="applyFilters"
                />
            </div>    
            <ListVueUse v-if="contacts.total || tags" 
                :contacts="contacts"
            />
            <div v-else class="flex items-center justify-center min-h-screen">
                <EmptyContacts />
            </div>
        </template>
    </AppLayout>
</template>

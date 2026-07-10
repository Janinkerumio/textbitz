<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { UserPlus } from 'lucide-vue-next';
import EmptyContacts from '@/Components/Placeholders/EmptyContacts.vue';
import List from './Partials/List.vue';
import SearchAndFilter from './Partials/SearchAndFilter.vue';

const props = defineProps({
    tags: Array,
    hasData: String|Number
})

const useFilters = ref({})

const applyFilters = (filters) => {
    useFilters.value = {...filters}
}
</script>

<template>
    <Head title="Contacts" />

    <AppLayout 
        pageTitle="Contacts" 
        :additionalText="`${hasData ?? 0} total contacts`" 
        :headButtonIcon="UserPlus"
    >
        <template #content>
            <div class="flex flex-col w-full">
                <SearchAndFilter 
                    class="mt-10"
                    :tags="tags",
                    @emitFilters="applyFilters"
                />
            </div>    
            <List v-if="hasData" 
                :filters="useFilters"
            />
            <div v-else class="flex items-center justify-center min-h-screen">
                <EmptyContacts />
            </div>
        </template>
        <template #modal>

        </template>
    </AppLayout>
</template>

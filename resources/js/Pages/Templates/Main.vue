<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { Plus } from 'lucide-vue-next';
import EmptyTemplates from '@/Components/Placeholders/EmptyTemplates.vue';
import List from './Partials/List.vue';
import SearchAndFilters from './Partials/SearchAndFilters.vue';
import { usePage, router } from '@inertiajs/vue3';
import crossPlatformToast from '@/helpers/crossPlatformToast.js';
import Create from './Modals/Create.vue';

const props = defineProps({
    hasData: {
        type: Number,
        default: 0
    },
    categories: {
        type: Array,
        default: []
    }
})

const page = usePage()
const toast = crossPlatformToast()

const filters = ref({})
const isCreateTemplatesShown = ref(false)

const showCreateTemplatesModal = () => {
    isCreateTemplatesShown.value = true
}

const handleFilters = (payload) => {
    filters.value = payload
}

const handleEmits = (id) => {
    toast.show('This function is under development')
}

const handleUseTemplate = (id) => {
    router.get(route('app.templates.use', id))
}
</script>

<template>
    <Head title="Templates" />

    <AppLayout 
        page-title="Templates" 
        :additional-text="`${hasData ?? 0} reusable templates`" 
        :head-button-icon="Plus"
        :head-button-action="() => showCreateTemplatesModal()"
    >
        <template #content>
            <div v-if="hasData" class="mt-8">
                <SearchAndFilters 
                    @search-emit="(payload) => handleFilters(payload)"
                />

                <List 
                    :search-query="filters"
                    @edit-template="(id) => handleEmits(id)"
                    @delete-template="(id) => handleEmits(id)"
                    @use-template="(id) => handleUseTemplate(id)"
                />
            </div>
            <div v-else class="flex min-h-screen items-center justify-center">
                <EmptyTemplates />
            </div>
        </template>
        <template #modal>
            <Create 
                v-model="isCreateTemplatesShown"
                :categories="categories"
            />
        </template>
    </AppLayout>
</template>
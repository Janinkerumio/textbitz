<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { Plus } from 'lucide-vue-next';
import EmptyTemplates from '@/Components/Placeholders/EmptyTemplates.vue';
import List from './Partials/List.vue';
import SearchAndFilters from './Partials/SearchAndFilters.vue';
import { usePage, router } from '@inertiajs/vue3';
import crossPlatformToast from '@/helpers/crossPlatformToast.js';
import Create from './Modals/Create.vue';
import Edit from './Modals/Edit.vue';
import DeletionConfirm from './Modals/DeletionConfirm.vue';

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
const isEditTemplateModalShown = ref(false)
const showDeletionModal = ref(false)
const passedId = ref(null)
const listRef = ref(null)

const showCreateTemplatesModal = () => {
    isCreateTemplatesShown.value = true
}

const handleFilters = (payload) => {
    filters.value = payload
}

const handleEmits = (id) => {
    toast.show('This function is under development')
}

const handleEdit = (id) => {
    isEditTemplateModalShown.value = id ? true : false
    passedId.value = id
}

const handleDelete = (id) => {
    showDeletionModal.value = true
    passedId.value = id
}

const handleRemoval = (id) => {
    if(id) {
        listRef.value?.removeTemplate?.(id)
    }
}

const handleUseTemplate = (id) => {
    router.get(route('app.templates.use', id))
}

watch(() => page.props.flash.newTemplate, (data) => {
    if (data) {
        listRef.value?.prependTemplate?.(data)
    }
})

watch(() => page.props.flash.templateUpdated, (data) => {
    if(data) {
        listRef.value?.prependTemplate?.(data, true)
    }
})
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

                <List ref="listRef"
                    :search-query="filters"
                    @edit-template="(id) => handleEdit(id)"
                    @delete-template="(id) => handleDelete(id)"
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
            <Edit
                v-model="isEditTemplateModalShown"
                :template-id="passedId"
                :categories="categories"
            />
            <DeletionConfirm 
                v-model="showDeletionModal"
                :template-id="passedId"
                @deleted-template="(id) => handleRemoval(id)"
            />
        </template>
    </AppLayout>
</template>
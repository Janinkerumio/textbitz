<script setup>
import { ref, computed, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { UserPlus } from 'lucide-vue-next';
import EmptyContacts from '@/Components/Placeholders/EmptyContacts.vue';
import List from './Partials/List.vue';
import SearchAndFilter from './Partials/SearchAndFilter.vue';
import LongPressActions from './Modals/LongPressActions.vue';
import Select from './Modals/Select.vue';
import Create from './Modals/Create.vue';

const props = defineProps({
    tags: Array,
    hasData: String|Number,
    newContact: { 
        type: Object,
        default: null
    }
})

const useFilters = ref({})
const showLongPressActions = ref(false)
const isSelectModalShown = ref(false)
const isCreateModalShown = ref(false)
const listRef = ref(null)
const passedId = ref(null)
const selectedIds = computed(() => listRef.value?.selectedIds ?? [])

const showCreateModal = () => {
    isCreateModalShown.value = true
}

const applyFilters = (filters) => {
    useFilters.value = {...filters}
}

const onLongPressActionsUpdate = (value) => {
    if (!value) listRef.value?.exitSelectMode()
}

const passId = (id) => {
    passedId.value = id
    isSelectModalShown.value = id ? true : false
}

const resetId = () => {
    passedId.value = null
}

const handleAddToBlast = () => {
    const ids = listRef.value?.selectedIds

    console.log('Add to Blast IDS: ' + ids)
    //Actions later
}

const handleDeleteContacts = () => {
    const ids = listRef.value?.selectedIds

    console.log('Delete contacts IDS: ' + ids)
    //Actions later
}

watch(() => props.newContact, (contact) => {
    if (contact) {
        listRef.value?.prependContact?.(contact)
    }
})
</script>

<template>
    <Head title="Contacts" />

    <AppLayout 
        pageTitle="Contacts" 
        :additionalText="`${hasData ?? 0} total contacts`" 
        :headButtonIcon="UserPlus"
        :headButtonAction="() => showCreateModal()"
    >
        <template #content>
            <div v-if="hasData" class="flex flex-col w-full">
                <SearchAndFilter 
                    class="mt-10"
                    :tags="tags",
                    @emitFilters="applyFilters"
                />
            </div>    
            <List v-if="hasData" 
                ref="listRef"
                :filters="useFilters"
                @openActionsModal="showLongPressActions = true"
                @openModalwithId="passId"
            />
            <div v-else class="flex items-center justify-center min-h-[100dvh]">
                <EmptyContacts />
            </div>
        </template>
        <template #modal>
            <LongPressActions 
                v-model="showLongPressActions"
                :selectedIds="selectedIds"
                @update:modelValue="onLongPressActionsUpdate"
                @addToBlastHandler="handleAddToBlast"
                @deleteContactsHandler="handleDeleteContacts"
            />
            <Select
                v-model="isSelectModalShown"
                :ID="passedId"
                @update:modelValue="resetId"
            />
            <Create
                v-model="isCreateModalShown"
            />
        </template>
    </AppLayout>
</template>

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

const props = defineProps({
    tags: Array,
    hasData: String|Number
})

const useFilters = ref({})
const showLongPressActions = ref(false)
const showSelectModal = ref(false)
const listRef = ref(null)
const passedId = ref(null)
const selectedIds = computed(() => listRef.value?.selectedIds ?? [])

const applyFilters = (filters) => {
    useFilters.value = {...filters}
}

const onLongPressActionsUpdate = (value) => {
    if (!value) listRef.value?.exitSelectMode()
}

const passId = (id) => {
    passedId.value = id
    showSelectModal.value = id ? true : false
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
                ref="listRef"
                :filters="useFilters"
                @openActionsModal="showLongPressActions = true"
                @openModalwithId="passId"
            />
            <div v-else class="flex items-center justify-center min-h-screen">
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
                v-model="showSelectModal"
                :ID="passedId"
                @update:modelValue="resetId"
            >

            </Select>
        </template>
    </AppLayout>
</template>

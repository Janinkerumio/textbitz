<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Compose from './Partials/Compose.vue';
import Selections from './Partials/Selections.vue';
import SelectContacts from './Modals/SelectContacts.vue';

const props = defineProps({
    messageTemplate: {
        type: Object,
        default: {}
    },
    templates: {
        type: Array,
        default: () => []
    }
})

const preMadeMessage = ref(props.messageTemplate ?? '')
const isContactsModalOpen = ref(false)
const selectedContacts = ref({})
const selectedContactsLength = ref(0)

const fillPreMadeMessage = (payload) => {
    preMadeMessage.value = payload
}

const handleSelectedContacts = (payload) => {
    selectedContacts.value = payload
    selectedContactsLength.value = payload.selectedCount
}
</script>

<template>
    <Head title="Create Blast" />

    <AppLayout page-title="New Blast" additional-text="Send SMS message to contacts">
        <template #content>
            <Selections 
                @emitTemplate="(value) => fillPreMadeMessage(value)"
                @openContactsModal="isContactsModalOpen = true"
                :selected-count="selectedContactsLength"
                :templates="templates"
            />

            <Compose 
                :pre-made-message="preMadeMessage"
                :selectedRecipients="selectedContacts"
            />
        </template>
        <template #modal>
            <SelectContacts 
                v-model="isContactsModalOpen"
                @selectedContacts="(payload) => handleSelectedContacts(payload)"
            />
        </template>
    </AppLayout>
</template>
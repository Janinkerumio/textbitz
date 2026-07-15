<script setup>
import { useTemplateRef, computed, ref, watch } from 'vue';
import { colorForTag } from '@/Composables/useTagColors';
import onlyInitials from '@/utils/onlyInitials';
import randomAvatarColor from '@/utils/avatarColors';
import { fetchContact } from '@/data/api/fetchViaAxios';
import AvatarCardColList from '@/Components/Skeleton/AvatarCardColList.vue';
import { createInfiniteScroll } from '@/Composables/createInfiniteScroll';
import { Ellipsis  } from 'lucide-vue-next'
import { formatPhoneDisplay } from '@/Composables/usePHPhoneFormatter';

const props = defineProps({
    filters: {
        type: Object,
        default: () => ({
            search: '',
            tags: []
        })
    }
})

const isSelectMode = ref(false);
const selectedContacts = ref(new Set())
const selectedIds = computed(() => Array.from(selectedContacts.value))

const emit = defineEmits([
    'openActionsModal',
    'openModalwithId'
])

const container = useTemplateRef('container')

const { items: contacts, loading, onScroll } = createInfiniteScroll(
    fetchContact,
    computed(() => props.filters),
    { distance: 20 }
)

const toggleSelection = (id) => {
    const selectedIds = new Set(selectedContacts.value)

    if (selectedIds.has(id)) {
        selectedIds.delete(id)
    } else {
        selectedIds.add(id)
    }

    selectedContacts.value = selectedIds
}

const openContact = (id) => {
    emit('openModalwithId', id)
}

const longPressEvent = (id) => {
    isSelectMode.value = true
    toggleSelection(id)
    emit('openActionsModal')
}

const exitSelectMode = () => {
    isSelectMode.value = false
    selectedContacts.value = new Set()
}

const prependContact = (contact) => {
    contacts.value.unshift(contact)
}

watch(selectedContacts, 
    () => {
        if(Array.from(selectedContacts.value).length === 0 && isSelectMode.value) {
            exitSelectMode()
        }
    }
)

defineExpose({
    exitSelectMode,
    selectedIds,
    prependContact
})
</script>

<template>
    <div 
        ref="container" 
        @scroll="onScroll(container)" 
        class="mt-5 overflow-y-auto max-h-full"
    >
        <div class="grid grid-cols-1 gap-2 h-full">
            <div v-for="contact in contacts" 
                :key="contact.id" 
                @click="isSelectMode ? toggleSelection(contact.id) : openContact(contact.id)"
                v-long-press="() => longPressEvent(contact.id)"
                class="backdrop-blur rounded-xl border shadow-sm p-4 transition-colors duration-300 ease-in-out"
                :class="selectedContacts.has(contact.id)
                    ? 'bg-blue-300/70 dark:bg-blue-500/40 border-blue-300' 
                    : 'bg-white/70 dark:bg-gray-500/40 border-gray-100 dark:border-gray-600'
                "
            >
                <div class="flex flex-wrap gap-2 max-w-full">
                    <div class="rounded-full text-xl flex items-center justify-center p-1 w-12 h-12 font-semibold" :class="randomAvatarColor(contact.contact_name)">
                        {{ onlyInitials(contact.contact_name) }}
                    </div>
                    <div class="flex-1 flex flex-col">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-300">{{ contact.contact_name }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ formatPhoneDisplay(contact.phone_num) }}</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <span v-for="tag in contact.tags" :key="tag" class="text-xs px-2 py-0.5 rounded-full" :class="colorForTag(tag)">
                                {{ tag }}
                            </span>
                        </div>
                    </div>
                    <Ellipsis class="text-gray-500"/>
                </div>
            </div>
            <div v-if="loading" class="flex justify-center">
                <AvatarCardColList />
            </div>
        </div>
    </div>
</template>
<script setup>
import { useTemplateRef, computed, ref, watch } from 'vue';
import { colorForTag } from '@/Composables/useTagColors';
import onlyInitials from '@/utils/onlyInitials';
import avatarColors from '@/utils/avatarColors';
import { fetchContact } from '@/data/api/fetchViaAxios';
import MediumSpinner from '@/Components/Spinners/MediumSpinner.vue';
import { createInfiniteScroll } from '@/Composables/createInfiniteScroll';
import { Ellipsis, Menu, X, Trash, SendHorizontal  } from 'lucide-vue-next'
import { Transition } from 'vue';
import { useIsVisible } from '@/Composables/useIsVisible';
import OptionsDropdown from '@/Components/Dropdown/OptionsDropdown.vue';
import BottomPopUp from '@/Components/PopUps/BottomPopUp.vue';

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
const isOptionsWithinOpen = ref(false)
const isOptionsBottomOpen = ref(false)

const toolbarRef = ref(null)
const { isVisible } = useIsVisible(toolbarRef)

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
    
}

const longPressEvent = (id) => {
    isSelectMode.value = true
    toggleSelection(id)
    
}

const exitSelectMode = () => {
    isSelectMode.value = false
    selectedContacts.value = new Set()
}

const avatarColor = (name) => {
    let hash = 0
    for (const n of name) {
        hash += n.charCodeAt(0)
    }
    return avatarColors[hash % avatarColors.length]
}

watch(selectedContacts, 
    () => {
        if(Array.from(selectedContacts.value).length === 0 && isSelectMode.value) {
            exitSelectMode()
        }
    }
)

watch((isOptionsBottomOpen, isOptionsWithinOpen, isVisible),
    () => {
        if(isOptionsBottomOpen.value && isVisible.value) {
            isOptionsBottomOpen.value = false
        }

        if(isOptionsWithinOpen && !isVisible.value) {
            isOptionsWithinOpen.value = false
        }
    }
)
</script>

<template>
    <div ref="toolbarRef" v-show="isSelectMode" class="w-full flex flex-wrap items-center justify-end mt-2 bg-white dark:bg-gray-700 rounded-xl shadow transition-all duration-400 ease-in-out">
        <p class="text-gray-600 dark:text-gray-400 px-6">{{ Array.from(selectedContacts).length }} selected</p>
        <div class="relative">
            <button @click="isOptionsWithinOpen = !isOptionsWithinOpen" class="text-blue-500 p-2 active:opacity-70">
                <Menu />
            </button>
            <OptionsDropdown :isOpen="isOptionsWithinOpen" width="w-72">
                <div class="flex flex-col gap-2 py-2">
                    <button class="flex justify-between text-gray-700 py-1 px-2">
                        <Trash class="text-red-500 dark:text-red-300 font-semibold" />
                        <p class="text-red-500 dark:text-red-300">Delete</p>
                        <div></div>
                    </button>
                    <button class="flex justify-between text-gray-700 py-1 px-2">
                        <SendHorizontal class="text-gray-500 dark:text-gray-400 font-semibold" />
                        <p class="text-gray-500 dark:text-gray-400">Add to Blast</p>
                        <div></div>
                    </button>
                </div>
            </OptionsDropdown>
        </div>
        <button @click="exitSelectMode" class="text-gray-600 dark:text-gray-400 p-2 active:opacity-70">
            <X />
        </button>
    </div>
    <div 
        ref="container" 
        @scroll="onScroll(container)" 
        class="mt-5 overflow-y-auto h-full"
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
                    <div class="rounded-full text-xl flex items-center justify-center p-1 w-12 h-12 font-semibold" :class="avatarColor(contact.contact_name)">
                        {{ onlyInitials(contact.contact_name) }}
                    </div>
                    <div class="flex-1 flex flex-col">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-300">{{ contact.contact_name }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ contact.phone_num }}</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <span v-for="tag in contact.tags" :key="tag" class="text-xs px-2 py-0.5 rounded-full" :class="colorForTag(tag)">
                                {{ tag }}
                            </span>
                        </div>
                    </div>
                    <Ellipsis class="text-gray-500"/>
                </div>
            </div>
            <div v-if="loading" class="flex justify-center py-4">
                <MediumSpinner />
            </div>
        </div>
    </div>
    <BottomPopUp :isOpen="isSelectMode && !isVisible" bottom-pos="bottom-[10%]">
        <p class="text-gray-600 dark:text-gray-400 px-6">{{ Array.from(selectedContacts).length }} selected</p>
        <div class="relative">
            <button @click="isOptionsBottomOpen = !isOptionsBottomOpen" class="text-blue-500 p-2 active:opacity-70">
                <Menu />
            </button>
            <OptionsDropdown :isOpen="isOptionsBottomOpen" width="w-72">
                <div class="flex flex-col gap-2 py-2">
                    <button class="flex justify-between text-gray-700 py-1 px-2">
                        <Trash class="text-red-500 dark:text-red-300 font-semibold" />
                        <p class="text-red-500 dark:text-red-300">Delete</p>
                        <div></div>
                    </button>
                    <button class="flex justify-between text-gray-700 py-1 px-2">
                        <SendHorizontal class="text-gray-500 dark:text-gray-400 font-semibold" />
                        <p class="text-gray-500 dark:text-gray-400">Add to Blast</p>
                        <div></div>
                    </button>
                </div>
            </OptionsDropdown>
        </div>
        <button @click="exitSelectMode" class="text-gray-600 dark:text-gray-400 p-2 active:opacity-70">
            <X />
        </button>
    </BottomPopUp>
</template>
<script setup>
import BottomModal from '@/Components/Modal/BottomModal.vue';
import { watch, reactive, ref, useTemplateRef, onMounted, computed } from 'vue';
import { debounce } from 'lodash-es'
import { fetchContact } from '@/data/api/fetchViaAxios';
import { Search } from 'lucide-vue-next'
import { useInfiniteScroll } from '@vueuse/core';
import { formatPhoneDisplay } from '@/Composables/usePHPhoneFormatter';
import { Users } from 'lucide-vue-next'
import { dialog } from '#nativephp'
import MediumSpinner from '@/Components/Spinners/MediumSpinner.vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    }
})

const contacts = ref([])
const totalContacts = ref(0)
const contactContainer = useTemplateRef('contact-container')
const page = ref(1)
const hasMore = ref(true)
const loading = ref(false)

const selectedContacts = ref(new Set())
const excludedContacts = ref(new Set())
const selectAllMode = ref(false)
const selectedContactsLength = computed(() => {
    return selectAllMode.value
        ? totalContacts.value - excludedContacts.value.size
        : selectedContacts.value.size
})

const emit = defineEmits([
        'update:modelValue',
        'selectedContacts'
    ])

const loadContacts = async (reset = false) => {
    if (loading.value) return
    if (!reset && !hasMore.value) return

    loading.value = true

    try {
        const params = new URLSearchParams()
        params.append('page', page.value)

        if(searchField.search !== '') {
            params.append('search', searchField.search)
        }

        const data = await fetchContact(params)

        if (reset || page.value === 1) {
            contacts.value = data.data
            totalContacts.value = data.total
        } else {
            contacts.value.push(...data.data)
        }

        page.value = data.current_page + 1
        hasMore.value = data.current_page < data.last_page
    } catch (error) {
        console.error(error)
        dialog.alert('Loading failed. Something went wrong')
    } finally {
        loading.value = false
        
    }
}

useInfiniteScroll(
    contactContainer,
    () => {
        loadContacts()
    },
    {
        distance: 10,
        canLoadMore: () => {
            return hasMore.value && !loading.value
        }
    }
)

const searchField = reactive({
    search: ''
})

const search = debounce(() => {
    page.value = 1
    hasMore.value = true
    loadContacts(true)
}, 200)
 
const selection = (id) => {
    if (selectAllMode.value) {
        const excluded = new Set(excludedContacts.value)
        excluded.has(id) ? excluded.delete(id) : excluded.add(id)
        excludedContacts.value = excluded
    } else {
        const selectedIds = new Set(selectedContacts.value)
        selectedIds.has(id) ? selectedIds.delete(id) : selectedIds.add(id)
        selectedContacts.value = selectedIds
    }
}

const isSelected = (id) => {
    return selectAllMode.value
        ? !excludedContacts.value.has(id)
        : selectedContacts.value.has(id)
}

const selectAll = () => {
    selectAllMode.value = true
    excludedContacts.value = new Set()
}

const exitSelection = () => {
    selectAllMode.value = false
    excludedContacts.value = new Set()
    selectedContacts.value = new Set()
}

watch(() => searchField.search, (value) => {
    if(value) {
        search()
    }
}, { deep: true })

watch([selectedContacts, excludedContacts, selectAllMode], () => {
    emit('selectedContacts', {
        selectAll: selectAllMode.value,
        contactIds: selectAllMode.value ? [] : Array.from(selectedContacts.value),
        excludedIds: selectAllMode.value ? Array.from(excludedContacts.value) : [],
        selectedCount: selectAllMode.value
                        ? totalContacts.value - excludedContacts.value.size
                        : selectedContacts.value.size
    })
}, { deep: true })

onMounted(() => {
    loadContacts()
})
</script>

<template>
    <BottomModal
        :model-value="modelValue"
        @update:model-value="emit('update:modelValue', $event)"
    >
        <header class="w-full pb-4">
            <h1 class="font-semibold text-lg dark:text-gray-300">Select Recipients</h1>
        </header>
        <section class="flex flex-col gap-2 w-full h-screen">
            <div class="flex flex-wrap items-center rounded-xl border bg-white/40 border-gray-400 py-1 px-4 focus-within:border-blue-500 transition-all duration-300">
                <Search class="text-gray-600 dark:text-gray-300"/>
                <input
                    name="search-contact"
                    v-model="searchField.search"
                    placeholder="Search contacts"  
                    class="flex-1 dark:placeholder-gray-300 border-0 bg-transparent outline-none focus:outline-none focus:ring-0 focus:border-0 shadow-none placeholder-gray-400"
                />
            </div>
            <div class="flex justify-between">
                <p v-if="selectedContactsLength >= 1" @click="exitSelection" class="text-sm dark:text-gray-300">Clear all <span class="text-blue-500 font-semibold text-base">({{ selectedContactsLength }})</span></p>
                <div v-else></div>
                <button @click="selectAll" class="py-1 px-2 text-blue-500 font-bold">
                    Select All
                </button>
            </div>
            <div ref="contact-container" class="overflow-y-auto snap-y snap-mandatory max-h-full">
                <div class="grid grid-cols-1 gap-2 h-full">
                    <div v-for="contact in contacts" 
                        :key="contact.id" 
                        @click="selection(contact.id)"
                        class="backdrop-blur rounded-2xl border py-2 px-4 transition-colors duration-300 ease-in-out"
                        :class="isSelected(contact.id)
                            ? 'bg-blue-300/70 dark:bg-blue-500/40 border-blue-300' 
                            : 'bg-gray-200/40 dark:bg-gray-500/40 border-gray-50 dark:border-gray-600'
                        "
                    >
                        <div class="flex flex-wrap items-center gap-2 max-w-full">
                            <div 
                                :class="isSelected(contact.id)
                                    ? 'bg-blue-500 text-blue-100'
                                    : 'bg-gray-300/50'"
                                class="rounded-full text-xl flex items-center justify-center p-1 w-10 h-10 font-semibold transition-all duration-100"
                            >
                                <Users :size="16"/>
                            </div>
                            <div class="flex-1 flex flex-col">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-300">{{ contact.contact_name }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ formatPhoneDisplay(contact.phone_num) }}</p>
                            </div>
                            <i class="transition-all duration-100"
                                :class="isSelected(contact.id)
                                    ? 'fa-solid fa-circle-dot text-blue-500'
                                    : 'fa-regular fa-circle text-gray-400'"
                            ></i>
                        </div>
                    </div>
                    <div v-if="loading" class="flex justify-center">
                        <MediumSpinner />
                    </div>
                </div>
            </div>
        </section>
    </BottomModal>
</template>
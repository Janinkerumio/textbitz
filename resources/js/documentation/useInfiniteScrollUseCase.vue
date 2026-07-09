<script setup>
import { ref, useTemplateRef, watch, onMounted } from 'vue';
import { colorForTag } from '@/Composables/useTagColors';
import onlyInitials from '@/utils/onlyInitials';
import avatarColors from '@/utils/avatarColors';
import { useInfiniteScroll } from '@vueuse/core';
import { fetchContact } from '@/data/api/fetchViaAxios';
import MediumSpinner from '@/Components/Spinners/MediumSpinner.vue';

const props = defineProps({
    filters: {
        type: Object,
        default: () => ({
            search: '',
            tags: []
        })
    }
})

const contacts = ref([])
const container = useTemplateRef('container')
const page = ref(1)
const hasMore = ref(true)
const loading = ref(false)

const avatarColor = (name) => {
    let hash = 0
    for (const n of name) {
        hash += n.charCodeAt(0)
    }
    return avatarColors[hash % avatarColors.length]
}

const loadContacts = async (reset = false) => {
    if (loading.value) return
    if (!reset && !hasMore.value) return

    loading.value = true

    try {
        const params = new URLSearchParams()
        params.append('page', page.value)

        if (props.filters?.search) {
            params.append('search', props.filters.search)
        }

        props.filters?.tags?.forEach(tag => {
            params.append('tags[]', tag)
        })

        const data = await fetchContact(params)

        if (reset) {
            contacts.value = data.data
        } else {
            contacts.value.push(...data.data)
        }

        page.value = data.current_page + 1
        hasMore.value = data.current_page < data.last_page
    }
    finally {
        loading.value = false
    }
}

useInfiniteScroll(
    container,
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

const clickEvent = (event) => {
    console.log(event);
}

watch(
    () => props.filters,
    () => {
        page.value = 1
        hasMore.value = true

        loadContacts(true)
    },
    {
        deep: true,
    }
)

onMounted(() => {
    loadContacts()
})
</script>

<template>
    <div ref="container" class="mt-5">
        <div class="grid grid-cols-1 gap-2 h-max">
            <div v-for="contact in contacts" :key="contact.id" @click="clickEvent(contact)" class="bg-white/70 dark:bg-gray-500/40 backdrop-blur rounded-xl border border-gray-100 dark:border-gray-600 shadow-sm p-4">
                <div class="flex flex-row gap-2 max-w-full">
                    <div class="rounded-full p-3 w-12 h-12 font-semibold" :class="avatarColor(contact.contact_name)">
                        {{ onlyInitials(contact.contact_name) }}
                    </div>
                    <div class="flex flex-col">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-300">{{ contact.contact_name }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ contact.phone_num }}</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <span v-for="tag in contact.tags" :key="tag" class="text-xs px-2 py-0.5 rounded-full" :class="colorForTag(tag)">
                                {{ tag }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="loading" class="flex justify-center py-4">
                <MediumSpinner />
            </div>
        </div>
    </div>
</template>
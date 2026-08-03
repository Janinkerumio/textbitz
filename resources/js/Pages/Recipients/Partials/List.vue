<script setup>
import { fetchRecipientsByHistory } from '@/data/api/fetchViaAxios';
import { useInfiniteScroll } from '@vueuse/core';
import { useTemplateRef, ref, computed, onMounted } from 'vue';
import { formatPhoneDisplay } from '@/Composables/usePHPhoneFormatter';
import { Dot } from 'lucide-vue-next';
import { blastStatus } from '@/utils/statusIndicator';
import crossPlatformToast from '@/helpers/crossPlatformToast';
import SmallSpinner from '@/Components/Spinners/SmallSpinner.vue';

const props = defineProps({
    historyId: {
        type: String
    },
    sortBy: {
        type: Object,
        default: {}
    }
})

const toast = crossPlatformToast()

const expandedRecipientId = ref(new Set())
const isExpanded = (id) => expandedRecipientId.value.has(id)

const recipients = ref([])
const container = useTemplateRef('container')
const page = ref(1)
const hasMore = ref(true)
const loading = ref(false)

const loadRecipients = async (reset = false) => {
    if (loading.value) return
    if (!reset && !hasMore.value) return

    loading.value = true

    try {
        const params = new URLSearchParams()
        params.append('page', page.value)

        if(props.sortBy.sort) {
            params.append('search', props.sortBy.sort)
        }

        const data = await fetchRecipientsByHistory(props.historyId, params)

        if (reset || page.value === 1) {
            recipients.value = data.data
        } else {
            recipients.value.push(...data.data)
        }

        page.value = data.meta.current_page + 1
        hasMore.value = data.meta.current_page < data.meta.last_page
    } catch (error) {
        console.error(error)
        toast.error('Loading failed. Something went wrong')
    } finally {
        loading.value = false
        
    }
}

useInfiniteScroll(
    container,
    () => {
        loadRecipients()
    },
    {
        distance: 10,
        canLoadMore: () => {
            return hasMore.value && !loading.value
        }
    }
)

const expand = (id) => {
    if (expandedRecipientId.value.has(id)) {
        expandedRecipientId.value.delete(id)
    } else {
        expandedRecipientId.value = new Set()
        expandedRecipientId.value.add(id)
    }
    
    expandedRecipientId.value = new Set(expandedRecipientId.value)
}

onMounted(() => {
    loadRecipients()
})
</script>

<template>
    <div 
        ref="container"
        class="mt-2 max-h-[80dvh] overflow-y-auto px-4"
    >
        <div class="grid grid-cols-1 gap-2 h-full">
            <div v-for="recipient in recipients"
                :key="recipient.id"
                @click="expand(recipient.id)"
                class="backdrop-blur rounded-xl border bg-white/70 dark:bg-gray-500/40 border-gray-100 dark:border-gray-600 shadow-sm p-2 transition-all duration-300 ease-in-out"
            >
                <div class="flex flex-row items-center">
                    <div class="flex-1 max-w-8 justify-center items-center">
                        <Dot :size="42" :class="blastStatus[recipient.status]"/>
                    </div>
                    <div class="flex-2 flex flex-col">
                        <p class="text-sm text-gray-800 dark:text-gray-200 truncate">
                            {{ recipient.name }}
                        </p>
                        <small class="text-gray-600 dark:text-gray-300">{{ formatPhoneDisplay(recipient.mobile_num) }}</small>
                    </div>
                </div>
                <p v-if="recipient.error_message" class="text-xs px-4 pb-2 text-gray-600 dark:text-gray-400">
                    Error:
                    {{ recipient.error_message }}
                </p>
            </div>
            <div v-if="loading" class="flex justify-center">
                <SmallSpinner />
            </div>
        </div>
    </div>
</template>
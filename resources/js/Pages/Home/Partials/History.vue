<script setup>
import { router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { fetchHistoryDashboard } from '@/data/api/fetchViaAxios';
import { Dot } from 'lucide-vue-next';
import { blastStatus } from '@/utils/statusIndicator';
import crossPlatformToast from '@/helpers/crossPlatformToast';
import SmallCardColList from '@/Components/Skeleton/SmallCardColList.vue';

const latestThree = ref(null)
const loading = ref(false)
const error = ref(null)

const toast = crossPlatformToast()

const loadData = async () => {
    loading.value = true
    try {
        const response = await fetchHistoryDashboard()
        latestThree.value = response.data
    } catch (err) {
        error.value = err
        toast.error('Server Error. Failed to load data')
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    loadData()
})
</script>

<template>
    <div class="flex flex-col gap-4 mt-3">
        <div class="flex items-center justify-between">
            <p class="uppercase font-semibold text-gray-500 dark:text-gray-400">Recent Activity</p>
            <button @click="router.get(route('app.blast.history'))" class="bg-blue-500 text-white py-1 px-2 text-xs rounded-full shadow">
                See all
            </button>
        </div>
        <div v-if="!loading" class="flex flex-col gap-2">
            <div v-for="history in latestThree" :key="history" class="flex flex-row items-center py-2 rounded-2xl shadow border border-gray-200 dark:border-gray-700 bg-white/40 dark:bg-white/10 backdrop-blur">
                <div class="flex-1 max-w-8 justify-center items-center">
                    <Dot :size="42" :class="blastStatus[history.status]"/>
                </div>
                <div class="flex-2 flex flex-col">
                    <p class="text-sm text-gray-800 dark:text-gray-200 truncate">
                        {{ history.blast.slice(0, 40) }}...
                    </p>
                    <small class="text-gray-600 dark:text-gray-400">{{ history.recipients }} recipients</small>
                </div>
            </div>
        </div>
        <div v-else class="flex flex-col gap-2">
            <SmallCardColList />
        </div>
    </div>
</template>
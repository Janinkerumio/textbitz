<script setup>
import { router } from '@inertiajs/vue3';
import historyMockData from '@/data/history';
import { Dot } from 'lucide-vue-next';
import { blastStatus } from '@/utils/statusIndicator';

const latestThree = historyMockData.slice(0, 3)

</script>

<template>
    <div class="flex flex-col gap-4 mt-3">
        <div class="flex items-center justify-between">
            <p class="uppercase font-semibold text-gray-500 dark:text-gray-400">Recent Activity</p>
            <button @click="router.get(route('app.blast.history'))" class="bg-blue-500 text-white py-1 px-2 text-xs rounded-full shadow">
                See all
            </button>
        </div>
        <div class="flex flex-col gap-2">
            <div v-for="history in latestThree" :key="history" class="flex flex-row items-center py-2 rounded-2xl shadow border border-gray-200 dark:border-gray-700 bg-white/40 dark:bg-white/10 backdrop-blur">
                <div class="flex-1 max-w-8 justify-center items-center">
                    <Dot :size="42" :class="blastStatus[history.status]"/>
                </div>
                <div class="flex-2 flex flex-col">
                    <p class="text-sm text-gray-800 dark:text-gray-200 truncate">
                        {{ history.blast.slice(0, 40) }}...
                    </p>
                    <small class="text-gray-600 dark:text-gray-400">{{ history.recepients.length }} recipients</small>
                </div>
            </div>
        </div>
    </div>
</template>
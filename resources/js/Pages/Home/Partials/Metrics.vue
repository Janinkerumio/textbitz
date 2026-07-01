<script setup>
import BoxIcon from '@/Components/Icon/BoxIcon.vue';
import { Send, UsersRound, MessageSquare, FileText } from 'lucide-vue-next';
import metricsData from '@/data/metrics';
</script>

<template>
    <div class="bg-gradient-to-b from-white dark:from-blue-500/20 via-white dark:via-blue-400/20 to-blue-500/10 rounded-xl shadow-lg p-2">
        <div class="grid grid-cols-2 gap-3">
            <div class="mt-5 flex flex-row gap-2">
                <Send :size="50" class="text-amber-500"/>
                <div class="flex flex-col">
                    <h1 class="font-bold text-blue-600 dark:text-blue-300">Reached Messages</h1>
                    <p class="mr-5 text-amber-500 font-extrabold text-3xl">{{ metricsData.reachedMessages }}</p>
                </div>
            </div>
            <div class="mt-5 flex flex-row gap-2">
                <MessageSquare :size="50" class="text-blue-600 dark:text-blue-300"/>
                <div class="flex flex-col">
                    <h1 class="font-bold text-amber-500">Sent Blasts</h1>
                    <p class="mr-5 text-blue-600 dark:text-blue-300 font-extrabold text-3xl">{{ metricsData.sentBlasts }}</p>
                </div>
            </div>
            <div class="col-span-2 flex flex-row justify-between max-w-full gap-2">
                <div class="flex-1 flex flex-row max-w-full gap-1">
                    <BoxIcon :icon="UsersRound" boxColor="bg-blue-300/50" iconColor="text-blue-600 dark:text-blue-300" class="flex-1 min-w-14"/>
                    <div class="flex-2 flex items-center justify-between w-full rounded-xl py-1 px-2 bg-gray-500/20 dark:bg-gray-500">
                        <small class="text-gray-600 dark:text-gray-300">Contacts</small>
                        <p class="text-end font-semibold text-gray-600 dark:text-gray-300 mr-2">{{ metricsData.contacts }}</p>
                    </div>
                </div>
                <div class="flex-1 flex flex-row max-w-full gap-1">
                    <BoxIcon :icon="FileText" boxColor="bg-blue-300/50" iconColor="text-blue-600 dark:text-blue-300" class="flex-1 min-w-14"/>
                    <div class="flex-2 flex items-center justify-between w-full rounded-xl py-1 px-2 bg-gray-500/20 dark:bg-gray-500">
                        <small class="text-gray-600 dark:text-gray-300">Templates</small>
                        <p class="text-end font-semibold text-gray-600 dark:text-gray-300 mr-2">{{ metricsData.templates }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
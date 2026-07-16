<script setup>
import { UsersRound } from 'lucide-vue-next';
import templatesDefault from '@/data/templates';
</script>

<template>
    <div class="mt-8 flex flex-col gap-2 bg-white/20 dark:bg-gray-600/50 backdrop-blur-xl rounded-2xl border border-gray-200 dark:border-gray-600 shadow">
        <div class="flex items-center justify-between">
            <div class="flex flex-col gap-2 mt-2 p-4">
                <p class="uppercase text-xs ml-8 text-gray-600 dark:text-gray-300">recepients</p>
                <p class="font-semibold text-gray-400">Select recepients</p>
            </div>
            <button class="bg-blue-200 p-3 mr-4 rounded-full active:opacity-75">
                <UsersRound :size="20" class="text-blue-500" />
            </button>
        </div>
        <div class="flex flex-col rounded-b-2xl bg-gray-300/20 border-t border-gray-300 p-4">
            <p class="text-gray-500 dark:text-gray-300 text-xs">Quick Template</p>
            <div class="overflow-x-auto snap-x snap-mandatory py-2">
                <div class="flex flex-row items-center gap-2 w-max">
                    <button 
                        v-for="template in templatesDefault"
                        :key="template.id"
                        class="text-sm text-gray-800 border-[0.25px] border-gray-300 bg-white rounded-xl py-1 px-3"
                    >
                        {{ template.title }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
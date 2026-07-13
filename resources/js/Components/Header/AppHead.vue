<script setup>
defineProps({
    headerName: {
        type: String
    },
    additionalText: {
        type: String,
        default: ''
    },
    headButtonAction: {
        type: Function,
        default: () => {}
    },
    headButtonIcon: {
        type: Object,
        dafault: null
    },
    headButtonText: {
        type: String,
        deafault: ''
    }
})
</script>

<template>
    <header class="fixed w-full flex items-center justify-between backdrop-blur-sm bg-blue-600/90 border-b border-gray-200 dark:border-blue-600 p-4 pt-12 z-50 sm:mt-0">
        <div class="flex flex-col">
            <h1 class="text-xl font-bold text-gray-100">{{ headerName }}</h1>
            <p v-if="additionalText !== ''" class="text-xs text-gray-200">{{ additionalText }}</p>
        </div>
        <button v-if="headButtonAction || headButtonIcon" @click="headButtonAction" class="rounded-xl p-2 bg-gray-200/90">
            <component :is="headButtonIcon" :size="24" class="text-blue-500"/>
            <p v-if="headButtonText !== ''">{{ headButtonText }}</p>
        </button>
    </header>
</template>
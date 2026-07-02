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
    },
    headButtonIcon: {
        type: Object
    },
    headButtonText: {
        type: String,
        deafault: ''
    }
})
</script>

<template>
    <Transition name="header" appear>
        <header class="fixed w-full flex items-center justify-between backdrop-blur-sm bg-blue-600/90 border-b border-gray-200 dark:border-blue-600 p-4 pt-12 z-50 sm:mt-0">
            <div class="flex flex-col">
                <h1 class="text-xl font-bold text-gray-100">{{ headerName }}</h1>
                <p v-if="additionalText !== ''" class="text-xs text-gray-200">{{ additionalText }}</p>
            </div>
            <button v-if="headButtonAction || headButtonIcon" @click="headButtonAction" class="rounded-xl p-2 bg-gray-200/90">
                <component :is="headButtonIcon" :size="24" class="text-gray-500"/>
                <p v-if="headButtonText !== ''">{{ headButtonText }}</p>
            </button>
        </header>
    </Transition>
</template>

<style scoped>
.header-enter-active,
.header-appear-active {
    transition: all 0.4s ease;
}

.header-enter-from,
.header-appear-from {
    opacity: 0;
    transform: translateY(-40px);
}

.header-enter-to,
.header-appear-to {
    opacity: 1;
    transform: translateY(0);
}
</style>
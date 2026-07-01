<script setup>
defineProps({
    headerName: {
        type: String
    },
    additionalText: {
        type: String,
        default: ''
    }
})
</script>

<template>
    <Transition name="header" appear>
        <header class="fixed w-full flex items-center justify-between backdrop-blur-sm bg-blue-600/90 border-b border-gray-200 dark:border-blue-600 p-4 z-50 sm:mt-0">
            <div class="flex flex-col gap-1 mt-8">
                <h1 class="text-xl font-bold text-gray-100">{{ headerName }}</h1>
                <p class="text-sm text-gray-200">{{ additionalText }}</p>
            </div>
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
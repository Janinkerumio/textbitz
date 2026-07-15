<script setup>
defineProps({
    isOpen: {
        type: Boolean,
        required: true,
    },
    width: {
        type: String,
        default: 'w-auto',
    },
});
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-200"
        enter-from-class="opacity-0 max-h-0"
        enter-to-class="opacity-100 max-h-40"
        leave-active-class="transition-all duration-200"
        leave-from-class="opacity-100 max-h-40"
        leave-to-class="opacity-0 max-h-0"
    >
        <div
            v-if="isOpen"
            :class="width"
            class="absolute right-0 bottom-full mt-2 z-60 rounded-lg border border-gray-200 dark:border-gray-500 dark:text-gray-200 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg shadow-lg overflow-hidden"
        >
            <slot />
        </div>
    </Transition>
</template>
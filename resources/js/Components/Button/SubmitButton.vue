<script setup>
defineProps({
    color: {
        default: 'bg-blue-500'
    }
})
</script>

<template>
    <button
        class="flex flex-row items-center gap-2 px-4 py-2 rounded-xl text-gray-100 active:opacity-50 disabled:opacity-25 transtion-all duration-100"
        :class="color"
    >
        <slot />
    </button>
</template>
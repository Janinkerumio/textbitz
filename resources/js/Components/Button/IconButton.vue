<script setup>
defineProps({
    colorClass: {
        default: 'text-blue-500'
    },
    icon: {
        type: Object,
        default: null
    },
    iconClass: {
        default: ''
    }
})
</script>

<template>
    <button :class="colorClass">
        <component v-if="icon" :is="icon" :size="22" />
        <div v-else>
            <slot />
        </div>
    </button>
</template>
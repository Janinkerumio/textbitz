<script setup>
import { FileText } from 'lucide-vue-next';
import Placeholder from '../Placeholder.vue';
</script>

<template>
    <Placeholder 
        :icon="FileText" 
        title="No saved templates yet" 
        message="Tap on the top right plus icon to create one"
    />
</template>
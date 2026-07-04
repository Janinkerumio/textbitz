<script setup>
import { UserMinus } from 'lucide-vue-next';
import Placeholder from '../Placeholder.vue';
</script>

<template>
    <Placeholder 
        :icon="UserMinus" 
        title="No saved contacts yet" 
        message="Tap on the top right plus icon to create one"
    />
</template>
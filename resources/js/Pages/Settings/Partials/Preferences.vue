<script setup>
import { ref } from 'vue';
import SettingsCard from '@/Components/Card/SettingsCard.vue';
import BoxIcon from '@/Components/Icon/BoxIcon.vue';
import LabeledName from '@/Components/Details/LabeledName.vue';
import { Palette, ChartBarStacked, Bell, Moon } from 'lucide-vue-next';

const props = defineProps({
    preferencesSettings: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['open'])
</script>

<template>
    <SettingsCard label="Prefrences" :icon="Palette" :action="() => emit('open')">
        <div class="flex flex-row items-center gap-4 px-4 border-b border-gray-200 dark:border-gray-500 pb-4">
            <BoxIcon :icon="ChartBarStacked"/>
            <LabeledName 
                label="Default Category"
                :context="preferencesSettings.defaultCategory"
            />
        </div>
        <div class="flex flex-row items-center gap-4 px-4 border-b border-gray-200 dark:border-gray-500 pb-4">
            <BoxIcon :icon="Bell"/>
            <LabeledName 
                label="Notifications"
                :context="preferencesSettings.isNotify ? 'On' : 'Off'"
            />
        </div>
        <div class="flex flex-row items-center gap-4 px-4">
            <BoxIcon :icon="Moon"/>
            <LabeledName 
                label="Dark Mode"
                :context="preferencesSettings.darkMode"
            />
        </div>
    </SettingsCard>
</template>
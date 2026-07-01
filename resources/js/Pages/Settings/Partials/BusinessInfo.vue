<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import SettingsCard from '@/Components/Card/SettingsCard.vue';
import BoxIcon from '@/Components/Icon/BoxIcon.vue';
import LabeledName from '@/Components/Details/LabeledName.vue';
import { Building2, Signature } from 'lucide-vue-next';

const page = usePage()

const corporate = computed(() => page.props.corporate) 
</script>

<template>
    <SettingsCard label="Business" :icon="Building2">
        <div class="flex flex-row items-center gap-4 px-4 border-b border-gray-200 dark:border-gray-500 pb-4">
            <BoxIcon :icon="Building2"/>
            <LabeledName 
                label="Business Name"
                :context="corporate?.business_name ?? 'My Business'"
            />
        </div>
        <div class="flex flex-row items-center gap-4 px-4">
            <BoxIcon :icon="Signature"/>
            <LabeledName 
                label="SMS Signature"
                :context="corporate?.sms_signature ?? 'Textbitz User'"
            />
        </div>
    </SettingsCard>
</template>
<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Logout from './Partials/Logout.vue';
import PersonalInfo from './Partials/PersonalInfo.vue';
import BusinessInfo from './Partials/BusinessInfo.vue';
import SettingsCard from '@/Components/Card/SettingsCard.vue';
import { ShieldUser } from 'lucide-vue-next';
import Preferences from './Partials/Preferences.vue';
import PreferencesModal from './Modals/PreferencesModal.vue';
import settings from '@/data/settings';

const securityAction = () => {
    router.get('profile')
}

const showPreferencesModal = ref(false)

const preferencesSettings = ref({
    defaultCategory: settings.preferences.defaultCategory,
    isNotify: JSON.parse(localStorage.getItem('isNotify')) ?? false,
    darkMode: localStorage.getItem('darkMode') ?? settings.preferences.darkMode
})

const hasPreferencesChanged = (data) => {
    preferencesSettings.value = data
}
</script>

<template>
    <Head title="Settings" />

    <AppLayout pageTitle="Settings">
        <template #content>
            <div class="flex min-h-screen items-center justify-center">
                <div class="flex flex-col gap-5 w-full">
                    <PersonalInfo />

                    <SettingsCard label="Security" :icon="ShieldUser" :action="() => securityAction()"/>

                    <BusinessInfo />

                    <Preferences 
                        @open="showPreferencesModal = true"
                        :preferencesSettings="preferencesSettings"
                    />

                    <Logout />
                </div>
            </div>
        </template>
        <template #modal>
            <PreferencesModal 
                v-model="showPreferencesModal"
                @changed="hasPreferencesChanged"
            />
        </template>
    </AppLayout>
</template>
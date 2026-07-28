<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Logout from './Partials/Logout.vue';
import PersonalInfo from './Partials/PersonalInfo.vue';
import BusinessInfo from './Partials/BusinessInfo.vue';
import SettingsCard from '@/Components/Card/SettingsCard.vue';
import { ShieldUser, Bug } from 'lucide-vue-next';
import Preferences from './Partials/Preferences.vue';
import PreferencesModal from './Modals/PreferencesModal.vue';
import settings from '@/data/settings';
import LabeledName from '@/Components/Details/LabeledName.vue';

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
            <div class="flex min-h-screen items-center justify-center mt-5">
                <div class="flex flex-col gap-5 w-full">
                    <PersonalInfo />

                    <SettingsCard label="Security" :icon="ShieldUser" :action="() => securityAction()"/>

                    <BusinessInfo />

                    <Preferences 
                        @open="showPreferencesModal = true"
                        :preferencesSettings="preferencesSettings"
                    />

                    <Logout />

                    <SettingsCard label="Debug Logs" :icon="Bug" :action="() => { router.get('/debug/logs') }"/>

                    <SettingsCard label="Debug Extensions" :icon="Bug" :action="() => { router.get('/debug-extensions') }"/>

                    <LabeledName label="Android platform" :context="$page.props.platform.isAndroid ? 'Yes' : 'No'"/>
                    <LabeledName label="IOS platform" :context="$page.props.platform.isIos ? 'Yes' : 'No'"/>

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
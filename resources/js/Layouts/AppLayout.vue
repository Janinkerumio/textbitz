<script setup>
import FooterNav from '@/Components/Footer/FooterNav.vue';
import AppHead from '@/Components/Header/AppHead.vue';
defineProps({
    pageTitle: String,
    additionalText: String
})
</script>

<template>
    <main class="bg-gray-100 dark:bg-gray-900/90 min-h-screen">
        <div class="flex flex-col h-screen overflow-hidden">
            <AppHead :header-name="pageTitle" :additional-text="additionalText"/>
            <section class="flex-1 flex-col gap-2 overflow-y-auto px-4">
                <div class="py-12"></div>
                <slot name="content" />
                <div class="py-10"></div>
            </section>
            <slot name="modal" />
            <FooterNav />
        </div>
    </main>
</template>
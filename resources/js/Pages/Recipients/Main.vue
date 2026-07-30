<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue';
import { ArrowLeft, SlidersHorizontal } from 'lucide-vue-next';
import Details from './Partials/Details.vue';
import PrimaryButton from '@/Components/Breeze/PrimaryButton.vue';

const props = defineProps({
    history: Object
})
</script>

<template>
    <Head :title="history.data?.template?.title && history.data?.template 
                ? `Recipients of ${history.data.template.title}`
                : 'Recipients'" 
    />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="flex flex-row gap-3 items-center text-base sm:text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                <Link :href="route('app.blast.history')">
                    <ArrowLeft />
                </Link>
                Recipients
            </h2>
        </template>
        <div class="py-2 pb-10">
            <div class="flex items-center justify-between px-6 pt-2">
                <PrimaryButton>
                    <SlidersHorizontal :size="18" :stroke-width="2.5"/><p class="ml-2">Filters</p>
                </PrimaryButton>
            </div>
            <div class="max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <Details 
                    :history="history.data"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
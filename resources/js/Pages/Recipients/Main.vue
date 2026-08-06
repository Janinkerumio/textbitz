<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue';
import { ArrowLeft, SlidersHorizontal, Dot } from 'lucide-vue-next';
import Details from './Partials/Details.vue';
import PrimaryButton from '@/Components/Breeze/PrimaryButton.vue';
import List from './Partials/List.vue';
import FilterOptions from './Modals/FilterOptions.vue';
import DeletionConfirm from './Modals/DeletionConfirm.vue';

const props = defineProps({
    history: Object
})

const showFiltersModal = ref(false)
const sortFilters = ref({})
const showDeletionModal = ref(false)
const passedId = ref(null)

const handleAppliedSorts = (payload) => {
    sortFilters.value = payload
}

const handleDuplicateBlast = (id) => {
    router.visit(route('app.blast.duplicate', id))
}

const handleDelete = (id) => {
    showDeletionModal.value = true
    passedId.value = id
}
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
        <div class="max-w-7xl py-2 pb-10">
            <div class="flex items-center justify-between px-6 pt-2">
                <PrimaryButton @click="showFiltersModal = true">
                    <SlidersHorizontal :size="18" :stroke-width="2.5"/><p class="ml-2">Filters</p>
                </PrimaryButton>
            </div>
            <div class="sm:px-6 lg:px-8">
                <Details 
                    :history="history.data"
                    @duplicate="(id) => handleDuplicateBlast(id)"
                    @delete="(id) => handleDelete(id)"
                />
                <p class="pl-8 flex gap-4">
                    <span class="text-emerald-500 text-xs">
                        <i class="fa-solid fa-circle text-[8px]"></i>
                        Sent
                    </span>
                    <span class="text-blue-500 text-xs">
                        <i class="fa-solid fa-circle text-[8px]"></i>
                        Queued
                    </span>
                    <span class="text-red-500 text-xs">
                        <i class="fa-solid fa-circle text-[8px]"></i>
                        Failed
                    </span>
                </p>
                <List
                    :history-id="history.data.id"
                    :sort-by="sortFilters"
                />

                <!-- modals -->
                <FilterOptions
                    v-model="showFiltersModal"
                    @applied-sort="(payload) => handleAppliedSorts(payload)"
                />
                <DeletionConfirm
                    v-model="showDeletionModal"
                    :history-id="passedId"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
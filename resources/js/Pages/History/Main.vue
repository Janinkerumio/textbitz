<script setup>
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import EmptyHistory from '@/Components/Placeholders/EmptyHistory.vue';
import List from './Partials/List.vue';
import Filters from './Partials/Filters.vue';
import crossPlatformToast from '@/helpers/crossPlatformToast.js';
import DeletionConfirm from './Modals/DeletionConfirm.vue';

const props = defineProps({
    stats: {
        type: Object,
        default: {}
    },
    hasData: {
        type: Boolean,
        default: false
    }
})

const toast = crossPlatformToast()
const page = usePage()

const appliedSortFilter = ref({})
const showDeletionModal = ref(false)
const passedId = ref(null)
const listRef = ref(null)

const handleSorting = (payload) => {
    appliedSortFilter.value = payload
}

const handleResend = (id) => {
    router.post(route('api.blast.resend', id))
}

const handleViewRecipients = (id) => {
    router.visit(route('app.recipients', id))
}

const handleDuplicateBlast = (id) => {
    router.visit(route('app.blast.duplicate', id))
}

const handleDelete = (id) => {
    showDeletionModal.value = true
    passedId.value = id
}

const handleRemoval = (id) => {
    if(id) {
        listRef.value?.removeData?.(id)
    }
}

watch(
    () => [ page.props.errors, page.props.flash.success ], 
    ([errors, success]) => {
        if (errors && Object.keys(errors).length > 0) {
            toast.error(errors.message ?? 'Something went wrong')
        }

        if(success) {
            toast.success(success ?? 'Processed successfully')
        }
    }, 
    { deep: true }
)
</script>

<template>
    <Head title="History" />

    <AppLayout page-title="History">
        <template #content>
            <div v-if="hasData">
                <Filters 
                    :stats-for-sort="stats"
                    @applied-sort="(value) => handleSorting(value)"
                />
                <List ref="listRef"
                    :sort-by="appliedSortFilter"
                    @view-recipients="(id) => handleViewRecipients(id)"
                    @resend="(id) => handleResend(id)"
                    @duplicate="(id) => handleDuplicateBlast(id)"
                    @delete="(id) => handleDelete(id)"
                />
            </div>
            <div v-else class="flex min-h-[100dvh] items-center justify-center">
                <EmptyHistory/>
            </div>
        </template>
        <template #modal>
            <DeletionConfirm 
                v-model="showDeletionModal"
                :history-id="passedId"
                @deleted-history="(id) => handleRemoval(id)"
            />
        </template>
    </AppLayout>
</template>
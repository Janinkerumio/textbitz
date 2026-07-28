<script setup>
import Modal from '@/Components/Breeze/Modal.vue';
import DangerButton from '@/Components/Breeze/DangerButton.vue';
import SecondaryButton from '@/Components/Breeze/SecondaryButton.vue';
import { ref, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import crossPlatformToast from '@/helpers/crossPlatformToast';

const props = defineProps({
    templateId: [Number, String],
    modelValue: Boolean
})

const showConfirmModal = ref(false)
const form = useForm({})
const page = usePage()
const toast = crossPlatformToast()

const emit = defineEmits(['update:modelValue', 'deletedTemplate'])

const openModal = () => {
    showConfirmModal.value = true
}

const closeModal = () => {
    showConfirmModal.value = false
    emit('update:modelValue', false)
}

const deleteTemplate = () => {
    const templateId = props.templateId

    form.delete(route('api.templates.delete', templateId), {
        preserveScroll: true,
        onSuccess: () => {
            emit('deletedTemplate', templateId)
            toast.success(page.props.flash.success)
        },
        onError: (errors) => {
            toast.error(errors ?? 'Something went wrong')
        },
        onFinish: () => closeModal()
    })
}

watch(() => props.modelValue, (value) => {
    if (value) {
        openModal()
    }
})
</script>

<template>
    <Modal :show="showConfirmModal" @close="closeModal">
        <div class="p-6">
            <h2
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
            >
                Are you sure you want to delete this template?
            </h2>
            <div class="mt-6 flex gap-2">
                <DangerButton
                    @click="deleteTemplate"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Delete
                </DangerButton>
                <SecondaryButton @click="closeModal">
                    Cancel
                </SecondaryButton>
            </div>
        </div>
    </Modal>
</template>
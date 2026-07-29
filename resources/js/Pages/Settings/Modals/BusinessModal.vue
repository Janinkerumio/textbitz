<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import BottomModal from '@/Components/Modal/BottomModal.vue';
import InputLabel from '@/Components/Breeze/InputLabel.vue';
import TextInput from '@/Components/Breeze/TextInput.vue';
import InputError from '@/Components/Breeze/InputError.vue';
import SubmitButton from '@/Components/Button/SubmitButton.vue';
import crossPlatformToast from '@/helpers/crossPlatformToast';

const props = defineProps({
    modelValue: Boolean
})

const snapshot = ref(null)

const emit = defineEmits(['update:modelValue', 'changed'])

const page = usePage()
const toast = crossPlatformToast()

const form  = useForm({
    business_name: page.props?.corporate?.business_name ?? 'My Business',
    sms_signature: page.props?.corporate?.sms_signature ?? 'Textbitz User'
})

const submit = () => {
    form.post(route('api.settings.change.business'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('update:modelValue', false)
            toast.success(page.props.flash.success)
        },
        onError: (error) => {
            toast.error(error ?? 'Something went wrong')
        }
    })
}

watch(() => props.modelValue, (isOpen, wasOpen) => {
    if(isOpen) {
        snapshot.value = {
            business_name: form.business_name,
            sms_signature: form.sms_signature
        }
    }

    if(wasOpen && !isOpen && snapshot.value) {
            const current = {
                business_name: form.business_name,
                sms_signature: form.sms_signature
            }

            if(JSON.stringify(current) !== JSON.stringify(snapshot.value))
            {
                emit('changed', current)
            }
        }
})
</script>

<template>
    <BottomModal
        :model-value="modelValue"
        @update:model-value="emit('update:modelValue', $event)"
    >
        <h1 class="font-semibold mb-5 dark:text-gray-200">Business</h1>
        <form @submit.prevent="submit" class="px-2 flex flex-col gap-4">
            <div class="w-full flex flex-col">
                <InputLabel value="Business Name"/>
                <TextInput
                    class="w-full"
                    name="business_name"
                    v-model="form.business_name"
                    required
                />
                <InputError :message="form.errors.business_name" />
            </div>
            <div class="w-full flex flex-col">
                <InputLabel value="SMS Signature"/>
                <TextInput
                    class="w-full"
                    name="sms_signature"
                    v-model="form.sms_signature"
                    required
                />
                <InputError :message="form.errors.sms_signature" />
            </div>
            <div class="w-full flex justify-end">
                <SubmitButton :disabled="form.processing">
                    Save
                </SubmitButton>
            </div>
        </form>
    </BottomModal>
</template>
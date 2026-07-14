<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import BottomModal from '@/Components/Modal/BottomModal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import SubmitButton from '@/Components/Button/SubmitButton.vue';
import { BookPlus } from 'lucide-vue-next';
import { usePhoneFormatter, normalizePhone, stripSpaces } from '@/Composables/usePHPhoneFormatter';

defineProps({
    modelValue: Boolean
})

const addedTag = ref('')

const emit = defineEmits(['update:modelValue'])

const form = useForm({
    contact_name: '',
    phone_num: '',
    tags: []
})

const { error: phoneFormatError, handlePhoneInput } = usePhoneFormatter(form, 'phone_num')

const addTag = () => {
    const tag = addedTag.value.trim()
    if (!tag) return
    if (form.tags.some(t => t.toLowerCase() === tag.toLowerCase())) {
        addedTag.value = ''
        return
    }
    form.tags.push(tag)
    addedTag.value = ''
}

const removeTag = (index) => {
    form.tags.splice(index, 1)
}

const submit = () => {
    form.phone_num = normalizePhone(stripSpaces(form.phone_num))
    console.log('Added contact: ' + JSON.stringify(form))
}
</script>

<template>
    <BottomModal
        :model-value="modelValue"
        @update:model-value="emit('update:modelValue', $event)"
    >
        <h1 class="font-semibold text-lg mb-4">New contact</h1>
        <form @submit.prevent="submit" class="flex flex-col gap-4 px-2">
            <div class="flex flex-col gap-1 w-full">
                <InputLabel value="Name" />
                <TextInput
                    name="contact_name"
                    v-model="form.contact_name"
                    required
                />
                <InputError :message="form.errors.contact_name" />
            </div>
            <div class="flex flex-col gap-1 w-full">
                <InputLabel value="Phone Number" />
                <TextInput
                    name="phone_num"
                    :model-value="form.phone_num"
                    @input="handlePhoneInput"
                    required
                />
                <InputError :message="phoneFormatError || form.errors.phone_num" />
            </div>
            <div class="w-full flex flex-col">
                <InputLabel value="Tags"/>
                <TransitionGroup
                    tag="div"
                    name="tag"
                    class="flex flex-wrap gap-2 mb-2"
                >
                    <span 
                        v-for="(tag, index) in form.tags" 
                        :key="tag"
                        class="flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-500/30 text-blue-700 dark:text-blue-300"
                    >
                        {{ tag }}
                        <button 
                            type="button" 
                            @click="removeTag(index)"
                            class="text-blue-500 hover:text-blue-700 dark:hover:text-blue-100 font-bold leading-none"
                        >
                            &times;
                        </button>
                    </span>
                </TransitionGroup>
                <InputError class="mt-2" :message="form.errors.tags" />
                <Transition name="slide-fade">
                    <TextInput
                        class="w-full"
                        placeholder="Type a tag and press Enter"
                        v-model="addedTag"
                        @keydown.enter.prevent="addTag"
                    />
                </Transition>
            </div>
            <div class="flex justify-end mt-5">
                <SubmitButton :disabled="form.processing">
                    <BookPlus :size="20" class="text-gray-100"/>Save
                </SubmitButton>
            </div>
        </form>
    </BottomModal>
</template>

<style scoped>
.tag-enter-active,
.tag-leave-active {
    transition: all 0.1s ease;
}
.tag-enter-from {
    opacity: 0;
    transform: scale(0.8);
}
.tag-leave-to {
    opacity: 0;
    transform: scale(0.8);
}
.tag-leave-active {
    position: absolute;
}
.tag-move {
    transition: transform 0.1s ease;
}

.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.1s ease;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}
</style>
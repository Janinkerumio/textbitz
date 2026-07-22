<script setup>
import { watch, ref, Transition } from 'vue';
import { useForm } from '@inertiajs/vue3';
import BottomModal from '@/Components/Modal/BottomModal.vue';
import { fetchOneContact } from '@/data/api/fetchViaAxios';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/Breeze/InputLabel.vue';
import { Trash, Send } from 'lucide-vue-next';
import onlyInitials from '@/utils/onlyInitials';
import randomAvatarColor from '@/utils/avatarColors';
import InputError from '@/Components/Breeze/InputError.vue';
import SubmitButton from '@/Components/Button/SubmitButton.vue';
import IconButton from '@/Components/Button/IconButton.vue';
import { dialog } from '#nativephp'

const props = defineProps({
    modelValue: Boolean,
    ID: {
        type: Number,
        default: null
    }
})

const contact = ref({})
const loading = ref(false)
const error = ref(null)
const newTag = ref('')
const showTagsField = ref(false)

const emit = defineEmits(['update:modelValue'])

const form = useForm({
    contact_name: '',
    phone_num: '',
    tags: []
})

const submit = () => {
    form.put(route('api.contacts.update', props.ID), {
        preserveScroll: true,
        onSuccess: async () => {
            form.reset()
            emit('update:modelValue', false)
            await dialog.toast('Contact updated successfully!')
        },
        onError: async (errors) => {
            console.error(errors)
            await dialog.alert(
                'Update Failed',
                errors.contact_name ?? 'Something went wrong. Please try again.'
            )
        }
    })
}

const addTag = () => {
    const tag = newTag.value.trim()
    if (!tag) return
    if (form.tags.some(t => t.toLowerCase() === tag.toLowerCase())) {
        newTag.value = ''
        return
    }
    form.tags.push(tag)
    newTag.value = ''
}

const removeTag = (index) => {
    form.tags.splice(index, 1)
}

const addToBlast = (id) => {
    console.log('Add to blast with ID: '+id)
}

const deleteContact = (id) => {
    console.log('Delete contact with ID: '+id)
}

const initiateDataFetching = async (id) => {
    if(!id) {
        contact.value = {}
        form.reset()
        return
    }

    loading.value = true
    error.value = null

    try {
        const response = await fetchOneContact(id)
        contact.value = response.data

        form.defaults({
            contact_name: response.data.contact_name,
            phone_num: response.data.phone_num,
            tags: response.data.tags
        })
        form.reset()
    } catch (e) {
        console.error(e)
        error.value = e
        contact.value = {}
    } finally {
        loading.value = false
    }
}

watch(() => props.ID, (id) => {
    initiateDataFetching(id)
}, { immediate: true })

</script>

<template>
    <BottomModal
        :model-value="modelValue"
        @update:model-value="emit('update:modelValue', $event)"
    >
        <Transition name="fade" mode="out-in">
            <div v-if="loading" class="px-2 py-8 flex justify-center">
                <!-- Skeleton body later-->
            </div>
            <div v-else>
                <div class="flex items-center justify-between mb-4 px-2">
                    <div class="rounded-full text-xl flex items-center justify-center p-1 w-14 h-14 font-semibold" :class="randomAvatarColor(form.contact_name || '?')">
                        {{ onlyInitials(form.contact_name || '?') }}
                    </div>
                    <div class="flex flex-wrap gap-5 px-4">
                        <IconButton @click="addToBlast(ID)" color-class="text-gray-500" :icon="Send"/>
                        <IconButton @click="deleteContact(ID)" color-class="text-red-500" :icon="Trash"/>
                    </div>
                </div>
                <form @submit.prevent="submit" class="px-2 flex flex-col gap-4">
                    <div class="w-full flex flex-col">
                        <InputLabel value="Name"/>
                        <TextInput
                            class="w-full"
                            name="contact_name"
                            v-model="form.contact_name"
                        />
                        <InputError class="mt-2" :message="form.errors.contact_name" />
                    </div>
                    <div class="w-full flex flex-col">
                        <InputLabel value="Phone Number"/>
                        <TextInput
                            class="w-full"
                            type="tel"
                            inputmode="numeric"
                            name="phone_num"
                            v-model="form.phone_num"
                        />
                        <InputError class="mt-2" :message="form.errors.phone_num" />
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
                            <button @click="showTagsField = !showTagsField" 
                                type="button" 
                                class="text-xs rounded-full bg-blue-500 py-1 px-2 text-blue-100"
                            >
                                {{ !showTagsField ? 'Add tags' : 'Close' }}
                            </button>
                        </TransitionGroup>
                        <InputError class="mt-2" :message="form.errors.tags" />
                        <Transition name="slide-fade">
                            <TextInput v-if="showTagsField"
                                class="w-full"
                                placeholder="Type a tag and press Enter"
                                v-model="newTag"
                                @keydown.enter.prevent="addTag"
                            />
                        </Transition>
                    </div>
                    <div class="flex justify-end gap-2">
                        <SubmitButton v-if="form.isDirty" :disabled="form.processing">
                            Save Changes
                        </SubmitButton>
                    </div>
                </form>
            </div>
        </Transition>
    </BottomModal>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.1s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

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
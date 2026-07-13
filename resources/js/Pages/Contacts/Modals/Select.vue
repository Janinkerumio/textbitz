<script setup>
import { watch, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import BottomModal from '@/Components/Modal/BottomModal.vue';
import { fetchOneContact } from '@/data/api/fetchViaAxios';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Trash, Send } from 'lucide-vue-next';
import onlyInitials from '@/utils/onlyInitials';
import avatarColors from '@/utils/avatarColors';

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
const newTags = ref('')
const showTagsField = ref(false)

const emit = defineEmits(['update:modelValue'])

const form = useForm({
    contact_name: '',
    phone_num: '',
    tags: []
})

const addTag = () => {
    const tag = newTag.value.trim()
    if (!tag) return
    if (form.tags.includes(tag)) {
        newTags.value = ''
        return
    }
    form.tags.push(tag)
    newTags.value = ''
}

const removeTag = (index) => {
    form.tags.splice(index, 1)
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
const avatarColor = (name) => {
    let hash = 0
    for (const n of name) {
        hash += n.charCodeAt(0)
    }
    return avatarColors[hash % avatarColors.length]
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
        <div class="flex items-center justify-between mb-4 px-2">
            <div class="rounded-full text-xl flex items-center justify-center p-1 w-14 h-14 font-semibold" :class="avatarColor(contact.contact_name)">
                {{ onlyInitials(contact.contact_name) }}
            </div>
            <div class="flex flex-wrap gap-5 px-4">
                <button class="text-gray-500"><Send :size="28" /></button>
                <button class="text-red-500"><Trash :size="28" /></button>
            </div>
        </div>
        <form v-if="!loading" @submit.prevent="" class="px-2 flex flex-col gap-4">
            <div class="w-full flex flex-col">
                <InputLabel value="Name"/>
                <TextInput
                    class="w-full"
                    name="contact_name"
                    v-model="form.contact_name"
                />
            </div>
            <div class="w-full flex flex-col">
                <InputLabel value="Phone Number"/>
                <TextInput
                    class="w-full"
                    name="phone_num"
                    v-model="form.phone_num"
                />
            </div>
            <div class="w-full flex flex-col">
                <InputLabel value="Tags"/>
                <div class="flex flex-wrap gap-2 mb-2">
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
                    <button @click="showTagsField = !showTagsField" class="text-xs rounded-full bg-blue-500 py-1 px-2 text-blue-100">{{ !showTagsField ? 'Add tags' : 'Close' }}</button>
                </div>
                <TextInput v-if="showTagsField"
                    class="w-full"
                    placeholder="Type a tag and press Enter"
                    v-model="newTag"
                    @keydown.enter.prevent="addTag"
                />
            </div>
            <div class="flex justify-end gap-2">
                <button v-if="form.isDirty" type="button" class="px-4 py-2 rounded-xl bg-blue-500 text-gray-100 active:opacity-50 transtion-all duration-100">
                    Save changes
                </button>
            </div>
        </form>
    </BottomModal>
</template>
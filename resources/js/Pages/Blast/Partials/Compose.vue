<script setup>
import { computed, watch, reactive } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import SubmitButton from '@/Components/Button/SubmitButton.vue';
import InputError from '@/Components/Breeze/InputError.vue';
import { Send } from 'lucide-vue-next';
import settings from '@/data/settings';
import crossPlatformToast from '@/helpers/crossPlatformToast';
import TextInput from '@/Components/Breeze/TextInput.vue';

const props = defineProps({
    messageVariables: {
        type: Array,
        default: []
    },
    preMadeMessage: {
        type: Object,
        default: {}
    },
    selectedRecipients: {
        type: Object,
        default: {}
    }
})

const toast = crossPlatformToast()
const page = usePage()

const chars = computed(() => form.message.length)
const messageVariables = computed(() => props.messageVariables.length !== 0
                                ? props.messageVariables
                                : settings.message.variables
                            )

const params = reactive({
    selectMode: false
})

const form = useForm({
    template_id: props.preMadeMessage?.id ?? null,
    title: props.preMadeMessage?.title ?? '',
    message: props.preMadeMessage?.message ?? '',
    recipients: [],
    excludedRecipients: []
})

const submit = () => {
    toast.show('This feature is under development')
    // form.post(
    //     route('api.blast.create', [{select_all: props.selectedRecipients.selectAll}]), {
    //         preserveScroll: true,
    //         onSuccess: () => {
    //             form.reset()
    //             toast.success(page.props.flash.success)
    //         },
    //         onError: (error) => {
    //             toast.error(error ?? 'Something went wrong')
    //         }
    //     })
}

const parsePayload = (payload) => {
    if (payload.selectAll) {
        form.excludedRecipients = [...payload.excludedIds]
        form.recipients = []
    } else {
        form.recipients = [...payload.contactIds]
        form.excludedRecipients = []
    }
    params.selectMode = payload.selectAll
}

watch(() => props.preMadeMessage, (value) => {
    if(value) {
        form.message = value.message
        form.template_id = value.id
        form.title = value.title
    }
})

watch(() => props.selectedRecipients, (payload) => {
    parsePayload(payload)
}, { deep: true })
</script>

<template>
    <form @submit.prevent="submit" class="mt-4 p-4 flex flex-col bg-white/20 dark:bg-gray-600/50 backdrop-blur-xl rounded-2xl border border-gray-200 dark:border-gray-600 shadow">
        <div class="py-2 w-full">
            <p class="text-gray-500 dark:text-gray-300 text-sm mb-2">Blast Title</p>
            <TextInput 
                class="w-full"
                name="title"
                v-model="form.title"
                :readonly="preMadeMessage?.title === form.title"
                required
            />
            <InputError :message="form.title.errors" />
        </div>
        <p class="text-gray-500 dark:text-gray-300 text-sm mb-2">Message</p>
        <div class="flex flex-wrap items-center gap-1 py-1 border-t-[0.25px] border-gray-300">
            <p class="text-xs text-gray-500 dark:text-gray-400">Available variables:</p>
            <span 
                v-for="(variable, index) in messageVariables"
                :key="index" 
                class="px-2 py-1 text-xs bg-gray-400/40 text-gray-600 dark:text-gray-300 rounded-full"
            >
                {{ '{' + variable.key + '}' }}
            </span>
        </div>
        <div class="overflow-x-auto snap-x snap-mandatory py-2">
            <textarea
                name="message"
                v-model="form.message"
                rows="6"
                placeholder="Type your message..."
                class="text-gray-800 dark:text-gray-200 w-full resize-none bg-transparent border-none focus:outline-none focus:ring-0 focus:shadow-none placeholder:text-gray-400" 
            />
            <InputError :message="form.errors.message" />
        </div>
        <div class="flex flex-wrap gap-2 max-w-full justify-between border-t-[0.5px] border-gray-300 pt-2">
            <div class="flex-1 flex flex-wrap gap-1">
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ chars }} characters</p>
            </div>
            <SubmitButton :disabled="form.processing">
                <Send :size="20"/>
                <p>Save</p>
            </SubmitButton>
        </div>
    </form>
</template>
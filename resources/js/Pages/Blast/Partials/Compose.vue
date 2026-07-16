<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import SubmitButton from '@/Components/Button/SubmitButton.vue';
import { Send } from 'lucide-vue-next';

const form = useForm({
    message: '',
    recipients: []
})

const chars = computed(() => form.message.length)
</script>

<template>
    <form @submit.prevent="" class="mt-4 p-4 flex flex-col bg-white/20 dark:bg-gray-600/50 backdrop-blur-xl rounded-2xl border border-gray-200 dark:border-gray-600 shadow">
        <p class="text-gray-500 dark:text-gray-300 text-xs">Message</p>
        <div class="overflow-x-auto snap-x snap-mandatory py-2">
            <textarea
                name="message"
                v-model="form.message"
                rows="6"
                placeholder="Type your message..."
                class="w-full resize-none bg-transparent border-none focus:outline-none focus:ring-0 focus:shadow-none placeholder:text-gray-400" 
            />
        </div>
        <div class="flex justify-between border-t-[0.5px] border-gray-300 py-2">
            <p class="text-xs text-gray-500">{{ chars }} characters</p>
            <SubmitButton :disabled="form.processing">
                <Send :size="20"/>
                <p>Save and Send</p>
            </SubmitButton>
        </div>
    </form>
</template>
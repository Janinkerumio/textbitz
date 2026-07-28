<script setup>
import BottomModal from '@/Components/Modal/BottomModal.vue';
import TextInput from '@/Components/Breeze/TextInput.vue';
import InputLabel from '@/Components/Breeze/InputLabel.vue';
import InputError from '@/Components/Breeze/InputError.vue';
import SubmitButton from '@/Components/Button/SubmitButton.vue';
import OptionsDropdown from '@/Components/Dropdown/OptionsDropdown.vue';
import { FilePenLine } from 'lucide-vue-next';
import { fetchOneTemplate } from '@/data/api/fetchViaAxios';
import { watch, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import crossPlatformToast from '@/helpers/crossPlatformToast';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    templateId: {
        type: [Number, String],
        default: null
    },
    categories: {
        type: Array,
        default: []
    }
})

const toast = crossPlatformToast()
const page = usePage()

const template = ref({})
const loading = ref(false)
const showCategoryDropdown = ref(false)

const emit = defineEmits(['update:modelValue'])

const form = useForm({
    title: '',
    category: '',
    message: ''
})

const openCategorySelection = () => {
    showCategoryDropdown.value = !showCategoryDropdown.value
}

const selectedCategory = (category) => {
    form.category = category
    openCategorySelection()
}

const submit = () => {
    form.put(route('api.templates.update', props.templateId), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            emit('update:modelValue', false)
            toast.success(page.props.flash.success)
        }, onError: (errors) => {
            console.error(errors)
            toast.error(errors.title ?? 'Something went wrong. Please try again later')
        }
    })
}

const initiateDataFetching = async (id) => {
    if(!id) {
        template.value = {}
        form.reset()
        return
    }

    loading.value = true

    try {
        const response = await fetchOneTemplate(id)
        template.value = response.data

        form.defaults({
            title: response.data.title,
            category: response.data.category,
            message: response.data.message
        })
        form.reset()
    } catch (e) {
        console.error(e)
        template.value = {}
    } finally {
        loading.value = false
    }
}

watch(() => props.templateId, (id) => {
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
                <div class="flex justify-between items-end">
                    <h1 class="font-semibold text-lg mb-4 dark:text-gray-200">Edit template</h1>
                    <i class="text-gray-600 rounded-full bg-gray-300 p-3">
                        <FilePenLine :size="24" />
                    </i>
                </div>
                <form @submit.prevent="submit" class="flex flex-col gap-4 px-2">
                    <div class="flex flex-col gap-1 w-full">
                        <InputLabel value="Title" />
                        <TextInput
                            name="title"
                            v-model="form.title"
                            required
                        />
                        <InputError :message="form.errors.title" />
                    </div>
                    <div class="flex flex-col gap-1 max-w-full">
                        <InputLabel value="Category" />
                        <div class="relative">
                            <TextInput
                                name="category"
                                v-model="form.category"
                                @click="openCategorySelection"
                                @input="showCategoryDropdown = false"
                                class="w-full"
                            />
                            <OptionsDropdown
                                :isOpen="showCategoryDropdown"
                                width-class="w-full"
                            >
                                <ul class="py-1 max-h-40 overflow-y-auto">
                                    <li
                                        v-for="(category, index) in categories"
                                        :key="index"
                                        @click="selectedCategory(category)"
                                        class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                                    >
                                        {{  category }}
                                    </li>
                                    <li v-if="categories.length === 0" class="px-4 py-2 text-gray-400 italic">
                                        No categories yet
                                    </li>
                                </ul>
                            </OptionsDropdown>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1 w-full">
                        <InputLabel value="Message" />
                        <textarea
                            name="message"
                            v-model="form.message"
                            rows="6"
                            required
                            placeholder="Use {name} to personalize..."
                            class="text-gray-800 dark:text-gray-200 w-full resize-none bg-transparent shadow border-[1.5px] border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-0 focus:shadow-none placeholder:text-gray-400 transition-all duration-200" 
                        />
                        <InputError :message="form.errors.message" />
                    </div>
                    <div class="flex justify-end mt-5">
                        <SubmitButton :disabled="form.processing">
                            <FilePenLine :size="20" class="text-gray-100"/><p class="uppercase">Save</p>
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
</style>
<script setup>
import { ref, watch } from 'vue';
import BottomModal from '@/Components/BottomModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SlidingSwitch from '@/Components/SlidingSwitch.vue';
import TextInput from '@/Components/TextInput.vue';
import settings from '@/data/settings';
import { ChevronsDownUp } from 'lucide-vue-next';
import { darkMode } from '@/Composables/useDarkMode';

const props = defineProps({
    modelValue: Boolean
})

const snapshot = ref(null)
const isNotify = ref(JSON.parse(localStorage.getItem('isNotify')) ?? false)
const darkModeOptions = darkMode
const isDarkModeOpen = ref(false)
const defaultCategory = ref(settings.preferences.defaultCategory)

const emit = defineEmits([
    'update:modelValue',
    'changed'
])

const selectDarkMode = (option) => {
    darkModeOptions.value = option
    isDarkModeOpen.value = false
}

watch(isNotify, (value) => {
    localStorage.setItem('isNotify', JSON.stringify(value))
})

watch(darkModeOptions, (value) => {
    localStorage.setItem('darkMode', value)
})

watch(
    () => props.modelValue,
    (isOpen, wasOpen) => {
        if(isOpen) {
            snapshot.value = {
                defaultCategory: defaultCategory.value,
                isNotify: isNotify.value,
                darkMode: darkModeOptions.value
            }
        }

        if(wasOpen && !isOpen && snapshot.value) {
            const current = {
                defaultCategory: defaultCategory.value,
                isNotify: isNotify.value,
                darkMode: darkModeOptions.value
            }

            if(JSON.stringify(current) !== JSON.stringify(snapshot.value))
            {
                emit('changed', current)
            }
        }
    }
)
</script>

<template>
    <BottomModal
        :modelValue="modelValue"
        @update:model-value="emit('update:modelValue', $event)"
    >
        <h1 class="font-semibold mb-5 dark:text-gray-200">Preferences</h1>
        <div class="px-2 flex flex-col gap-4">
            <div class="w-full flex flex-col">
                <InputLabel value="Default Category"/>
                <TextInput
                    class="w-full"
                    name="category"
                    v-model="defaultCategory"
                    disabled
                />
            </div>
            <div class="flex items-center justify-between">
                <InputLabel value="Notifications" />
                <SlidingSwitch v-model="isNotify"/>
            </div>
            <div class="relative">
                <div class="flex justify-between items-center">
                    <InputLabel value="Dark Mode" />

                    <button
                        type="button"
                        @click="isDarkModeOpen = !isDarkModeOpen"
                        class="flex flex-row dark:text-gray-100 items-center gap-2 text-xs"
                    >
                        {{ darkModeOptions }}
                        <ChevronsDownUp
                            :size="24"
                            class="text-gray-600 dark:text-gray-300"
                        />
                    </button>
                </div>

                <Transition
                    enter-active-class="transition-all duration-200"
                    enter-from-class="opacity-0 max-h-0"
                    enter-to-class="opacity-100 max-h-40"
                    leave-active-class="transition-all duration-200"
                    leave-from-class="opacity-100 max-h-40"
                    leave-to-class="opacity-0 max-h-0"
                >
                    <div
                        v-if="isDarkModeOpen"
                        class="absolute right-0 bottom-full mt-2 z-60 rounded-lg border border-gray-200 dark:border-gray-500 dark:text-gray-200 bg-white/20 dark:bg-gray-400/10 backdrop-blur-lg shadow-lg overflow-hidden"
                    >
                        <button
                            v-for="option in settings.darkModeChoices"
                            :key="option"
                            @click="selectDarkMode(option)"
                            class="block w-full px-4 py-3 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-400"
                        >
                            {{ option }}
                        </button>
                    </div>
                </Transition>
            </div>
        </div>
    </BottomModal>
</template>
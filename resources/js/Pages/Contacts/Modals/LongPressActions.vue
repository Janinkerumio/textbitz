<script setup>
import { computed } from 'vue';
import FooterActionsModal from '@/Components/Footer/FooterActionsModal.vue';
import { Trash, SendHorizontal } from 'lucide-vue-next';

const props = defineProps({
    modelValue: Boolean,
    selectedIds: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits([
    'update:modelValue',
    'addToBlastHandler',
    'deleteContactsHandler'
])

const count = computed(() => props.selectedIds.length)
</script>

<template>
    <FooterActionsModal 
        :model-value="modelValue"
        @update:model-value="emit('update:modelValue', $event)"
    >
        <div class="flex flex-row item-center gap-4">
            <p class="text-center text-sm mt-2 text-gray-600">{{ count }} selected</p>
            <button 
                @click="emit('addToBlastHandler')"
                class="flex flex-col gap-1 items-center text-blue-500"
            >
                <SendHorizontal :size="22" />
                <small class="text-[10px]">Add to blast</small>
            </button>
            <button 
                @click="emit('deleteContactsHandler')"
                class="flex flex-col gap-1 items-center text-red-500"
            >
                <Trash :size="22" />
                <small class="text-[10px]">Delete</small>
            </button>
        </div>
    </FooterActionsModal>
</template>
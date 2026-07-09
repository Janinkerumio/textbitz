<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { InfiniteScroll } from '@inertiajs/vue3';
import { colorForTag } from '@/Composables/useTagColors';
import MediumSpinner from '@/Components/Spinners/MediumSpinner.vue';
import onlyInitials from '@/utils/onlyInitials';
import avatarColors from '@/utils/avatarColors';

const props = defineProps({
    contacts: Object
})

const infiniteScrollRef = ref(null)
const sentinel = ref(null)
let observer = null

const initObserver = () => {
    if (observer) observer.disconnect();

    observer = new IntersectionObserver((entries) => {
        const entry = entries[0];
        if (entry.isIntersecting) {
            infiniteScrollRef.value?.fetchNext();
        }
    }, {
        rootMargin: '200px',
    })
}

const avatarColor = (name) => {
    let hash = 0
    for (const n of name) {
        hash += n.charCodeAt(0)
    }
    return avatarColors[hash % avatarColors.length]
}

const clickEvent = (event) => {
    console.log(event)
}

onMounted(() => {
    initObserver()
    if(sentinel.value) observer.observe(sentinel.value)
})

watch(sentinel, (newEl, oldEl) => {
    if (oldEl) observer?.unobserve(oldEl)
    if (newEl) observer?.observe(newEl)
})

onBeforeUnmount(() => observer?.disconnect())
</script>

<template>
    <InfiniteScroll 
        ref="infiniteScrollRef"
        data="contacts" 
        class="mt-5" 
        items-element="#contacts-list"
        manual
    >
        <div id="contacts-list" class="grid grid-cols-1 gap-2 h-max">
            <div v-for="contact in contacts.data" :key="contact.id" @click="clickEvent(contact)" class="bg-white/70 dark:bg-gray-500/40 backdrop-blur rounded-xl border border-gray-100 dark:border-gray-600 shadow-sm p-4">
                <div class="flex flex-row gap-2 max-w-full">
                    <div class="rounded-full p-3 w-12 h-12 font-semibold" :class="avatarColor(contact.contact_name)">
                        {{ onlyInitials(contact.contact_name) }}
                    </div>
                    <div class="flex flex-col">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-300">{{ contact.contact_name }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ contact.phone_num }}</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <span v-for="tag in contact.tags" :key="tag" class="text-xs px-2 py-0.5 rounded-full" :class="colorForTag(tag)">
                                {{ tag }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <template #next="{ loading, hasMore }">
            <div v-if="hasMore" ref="sentinel" class="h-4"></div>
            <div v-if="loading" class="flex justify-center py-4">
                <MediumSpinner />
            </div>
        </template>
    </InfiniteScroll>
</template>
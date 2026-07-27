<script setup>
import SearchBar from '@/Components/Input/SearchBar.vue';
import { reactive, watch } from 'vue';
import { debounce } from 'lodash-es'

const form = reactive({
    search: ''
})

const emit = defineEmits(['searchEmit'])

const emitChanges = debounce(() => {
    emit('searchEmit', {
        search: form.search
    })
}, 200)

watch(form, emitChanges, { deep: true })
</script>

<template>
    <SearchBar 
        name="search_template"
        v-model="form.search"
        placeholder="Search template"
    />
</template>
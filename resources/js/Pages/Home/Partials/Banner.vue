<script setup>
import { usePage, router } from '@inertiajs/vue3';
import { Zap, MailPlus } from 'lucide-vue-next';

const page = usePage()
const user = page.props.auth.user
</script>

<template>
    <div class="mt-12 bg-gradient-to-br from-blue-600 via-blue-500 to-amber-500/40 p-4 rounded-2xl">
        <h1 class="font-bold font-mono text-white text-3xl">Hello {{ user.name }}</h1>
        <div class="flex justify-between mt-2">
            <div class="flex flex-col gap-4">
                <small class="flex-1 max-w-32 text-white rounded-full bg-white/20 flex flex-row gap-2 items-center py-1 px-2">
                    <Zap :size="16"/>
                    Ready to send
                </small>
                <div class="flex flex-col">
                    <h1 class="font-extrabold text-white text-xl">Launch Your Campaign</h1>
                    <small class="text-white/70">
                        Create a new SMS blast to reach your contacts instantly
                    </small>
                </div>
                <button @click="router.get(route('app.create-blast'))" class="flex self-start ml-2 text-sm text-blue-600 px-3 py-2 rounded-xl bg-white/80 backdrop-blur-md shadow">
                    New Blast
                </button>
            </div>
            <i class="flex self-end text-white/90">
                <MailPlus :size="120" :stroke-width="2.5"/>
            </i>
        </div>
    </div>
</template>
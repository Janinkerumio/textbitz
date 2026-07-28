<script setup>
import NavLinks from '@/Components/Button/NavLinks.vue';
import { Link } from '@inertiajs/vue3';
import { House, Settings, UsersRound, FileText, PenSquare, History } from 'lucide-vue-next';

</script>

<template>
    <div class="flex flex-col items-center gap-2 py-6 px-4 w-full">
        <Link :href="route('app.dashboard')" 
            class="w-full px-4 py-2 flex flex-row gap-4 items-center  rounded-lg"
            :class="route().current('app.dashboard')
                        ? 'text-blue-600 bg-blue-200'
                        : 'text-gray-600'"
        >
            <House />Dashboard
        </Link>
        <NavLinks :pageLink="route('app.contacts')" routeName="app.contacts">
            <UsersRound :size="24" />
        </NavLinks>
        <NavLinks :pageLink="route('app.blast.create')" routeName="app.blast.create">
            <PenSquare :size="24" />
        </NavLinks>
        <NavLinks :pageLink="route('app.blast.history')" routeName="app.blast.history">
            <History :size="24" />
        </NavLinks>
        <NavLinks :pageLink="route('app.blast-templates')" routeName="app.blast-templates">
            <FileText :size="24" />
        </NavLinks>
        <NavLinks :pageLink="route('app.settings')" routeName="app.settings">
            <Settings :size="24" />
        </NavLinks>
    </div>
</template>
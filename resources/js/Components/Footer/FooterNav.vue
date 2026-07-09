<script setup>
import NavLinks from '@/Components/Button/NavLinks.vue';
import { House, Settings, UsersRound, FileText, PenSquare, History } from 'lucide-vue-next';

</script>

<template>
    <footer class="fixed w-full bottom-0 bg-white/90 dark:bg-gray-900/90 backdrop-blur-lg border-t border-gray-200 dark:border-gray-600 z-50">
        <div class="flex flex-wrap justify-between px-2">
            <NavLinks :pageLink="route('app.dashboard')" routeName="app.dashboard">
                <House :size="24" />
            </NavLinks>
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
    </footer>
</template>
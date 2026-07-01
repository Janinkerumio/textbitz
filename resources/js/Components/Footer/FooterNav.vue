<script setup>
import NavLinks from '@/Components/Button/NavLinks.vue';
import { House, Settings, UsersRound, FileText, PenSquare } from 'lucide-vue-next';

</script>

<template>
    <footer class="fixed w-full bottom-0 pb-2 bg-white/90 dark:bg-gray-900/90 backdrop-blur-lg border-t border-gray-200 dark:border-gray-600 z-50">
        <div class="flex flex-wrap justify-between px-4">
            <NavLinks :pageLink="route('app.dashboard')" routeName="app.dashboard">
                <House :size="26" />
            </NavLinks>
            <NavLinks :pageLink="route('app.contacts')" routeName="app.contacts">
                <UsersRound :size="26" />
            </NavLinks>
            <NavLinks :pageLink="route('app.create-blast')" routeName="app.create-blast">
                <PenSquare :size="26" />
            </NavLinks>
            <NavLinks :pageLink="route('app.blast-templates')" routeName="app.blast-templates">
                <FileText :size="26" />
            </NavLinks>
            <NavLinks :pageLink="route('app.settings')" routeName="app.settings">
                <Settings :size="26" />
            </NavLinks>
        </div>
    </footer>
</template>
<script setup>
import { ref, computed, Transition } from 'vue';
import { Head } from '@inertiajs/vue3';
import LogIn from './Partials/LogIn.vue';
import SignUp from './Partials/SignUp.vue';

defineProps({
    status: {
        type: String,
        default: null
    },
    canResetPassword: {
        type: Boolean,
        default: true
    }
})

const toggleAuth = ref(false)
const direction = ref('forward')
const transitionName = computed(() => direction.value === 'forward' ? 'slide-left' : 'slide-right')

const switchScreen = () => {
    direction.value = toggleAuth.value ? 'back' : 'forward'
    toggleAuth.value = !toggleAuth.value
}
</script>

<template>
    <Head title="Welcome" />
    <div class="bg-gray-50 p-4 dark:bg-black text-gray-800 dark:text-gray-300 min-h-screen">
        <div class="corner-band-blue absolute w-[500px] h-[300px] -top-24 -right-24 bg-blue-600 shadow-[0_-6px_12px_rgba(0,0,0,0.35)]"
            style="--slide-x: 300px; --slide-y: -300px;"
        ></div>
        <div
            class="corner-band-amber absolute w-[900px] h-[70px] -top-[120px] -right-[180px] rotate-45
                bg-gradient-to-r from-amber-600 via-amber-400 to-amber-300
                shadow-[50px_-6px_12px_rgba(0,0,0,0.35)]"
            style="--slide-x: -60px; --slide-y: 60px;"
        ></div>
        <div
            class="absolute w-[900px] h-[140px] -top-[-50px] -right-[250px] rotate-45
                bg-gray-50 shadow-[0_-6px_12px_rgba(0,0,0,0.35)]"
        ></div>
        <div
            class="absolute w-[900px] h-[140px] -top-[-60px] -right-[250px] rotate-45
                bg-gray-50"
        ></div>

        <div class="corner-band-blue absolute w-[500px] h-[300px] -bottom-24 -left-24 bg-blue-600"
            style="--slide-x: -300px; --slide-y: 300px;"
        ></div>
        <div
            class="corner-band-amber absolute w-[900px] h-[70px] -bottom-[120px] -left-[180px] rotate-45
                bg-gradient-to-r from-amber-300 via-amber-400 to-amber-600
                shadow-[0_6px_12px_rgba(0,0,0,0.35)]"
                style="--slide-x: 60px; --slide-y: -60px;"
        ></div>
        <div
            class="absolute w-[900px] h-[140px] -bottom-[-50px] -right-[250px] rotate-45
                bg-gray-50 shadow-[0_6px_12px_rgba(0,0,0,0.35)]"
        ></div>
        <div
            class="absolute w-[900px] h-[140px] -bottom-[-80px] -right-[250px] rotate-45
                bg-gray-50 "
        ></div>
        <div class="relative flex items-center justify-center h-screen px-4">
            <Transition :name="transitionName" mode="out-in">
                <LogIn 
                    v-if="!toggleAuth"
                    :status="status"
                    :can-reset-password="canResetPassword"
                    @switch-screen="switchScreen"
                    key="login"
                />
                <SignUp 
                    v-else
                    @switch-screen="switchScreen"
                    key="register"
                />
            </Transition>
        </div>
    </div>
</template>

<style scoped>
.slide-left-enter-active,
.slide-left-leave-active,
.slide-right-enter-active,
.slide-right-leave-active {
    transition: transform 0.3s ease, opacity 0.3s ease;
    inset: 0;
}

.slide-left-enter-from {
    transform: translateX(40px);
    opacity: 0;
}
.slide-left-leave-to {
    transform: translateX(-40px);
    opacity: 0;
}
.slide-right-enter-from {
    transform: translateX(-40px);
    opacity: 0;
}
.slide-right-leave-to {
    transform: translateX(40px);
    opacity: 0;
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
 
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideInCorner {
    from {
        transform: translate(var(--slide-x, 0), var(--slide-y, 0)) rotate(45deg);
    }
    to {
        transform: translate(0, 0) rotate(45deg);
    }
}

.corner-band-amber {
    animation-name: slideInCorner;
    animation-duration: 0.3s;
    animation-timing-function: ease-out;
    animation-delay: 0.3s;
    animation-fill-mode: both;
}
.corner-band-blue {
    animation: slideInCorner;
    animation-duration: 1s;
    animation-timing-function: ease-out;
    animation-fill-mode: both;
}
</style>
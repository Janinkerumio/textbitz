<script setup>
import { MessageSquareShare, Mail, Lock, EyeOff, Eye } from 'lucide-vue-next';
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';

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

const showPassword = ref(false)

const form = useForm({
    email: '',
    password: '',
    remember: false
})

const emit = defineEmits(['switchScreen'])

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password')
    })
}
</script>

<template>
    <div class="flex flex-col w-full gap-8">
        <header class="flex justify-center items-center gap-3">
            <i class="rounded-xl text-gray-100 bg-blue-500 flex item-center justify-center p-[10px]">
                <MessageSquareShare :size="25" :stroke-width="3"/>
            </i>
            <p class="text-xl font-bold">TextBitz</p>
        </header>
        <section class="flex flex-col gap-1 w-full">
            <h1 class="flex items-start text-[28px] font-bold">Welcome Back</h1>
            <p class="text-gray-500">Log in to send your next blast</p>
        </section>
        <form @submit.prevent="submit" class="flex flex-col gap-2 w-full">
            <div>
                <label for="email" class="text-sm">
                    Email
                </label>
                <div class="relative gap-2">
                    <Mail :size="24" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" />
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        autofocus
                        autocomplete="username"
                        placeholder="you@email.com"
                        class="w-full pl-14 pr-4 py-2.5 rounded-xl border border-[#E5E7EB] bg-white text-lg text-gray-600 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition-all"
                    />
                </div>
                <p v-if="form.errors.email" class="mt-1.5 text-xs text-[#DC2626]">{{ form.errors.email }}</p>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm">Password</label>
                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs font-medium text-amber-600 hover:opacity-75">
                        Forgot password?
                    </Link>
                </div>
                <div class="relative">
                    <Lock :size="24" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#9CA3AF]" />
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        v-model="form.password"
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full pl-14 pr-4 py-2.5 rounded-xl border border-[#E5E7EB] bg-white text-lg text-gray-600 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition-all"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#9CA3AF] hover:text-[#6B7280]"
                        :aria-label="showPassword ? 'Hide password' : 'Show password'"
                    >
                        <EyeOff v-if="showPassword" :size="22" />
                        <Eye v-else :size="22" />
                    </button>
                </div>
                <p v-if="form.errors.password" class="mt-1.5 text-xs text-[#DC2626]">{{ form.errors.password }}</p>
            </div>

            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input
                    type="checkbox"
                    v-model="form.remember"
                    class="w-4 h-4 rounded border-[#D1D5DB] text-blue-500 focus:ring-blue-500/40"
                />
                <span class="text-sm text-[#6B7280]">Remember me</span>
            </label>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full mt-8 py-2.5 rounded-xl bg-blue-500 text-white text-lg font-semibold hover:opacity-75 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
            >
                {{ form.processing ? 'Logging in…' : 'Log in' }}
            </button>
            <p class="mt-8 text-center text-sm text-gray-500">
                New to TextBitz?
                <span @click="emit('switchScreen')" class="font-semibold text-gray-800">Create an account</span>
            </p>
        </form>
    </div>
</template>
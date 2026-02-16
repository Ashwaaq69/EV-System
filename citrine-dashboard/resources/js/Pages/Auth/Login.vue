<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { Mail, Lock, LogIn, ArrowRight } from 'lucide-vue-next';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Sign In | CitrineOS" />

        <div class="mb-6 text-center">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Welcome back</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Please enter your details to sign in</p>
        </div>

        <div v-if="status" class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm font-medium text-green-600 dark:text-green-400 flex items-center gap-2">
            <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div class="space-y-2">
                <InputLabel for="email" value="Email Address" class="text-zinc-600 dark:text-zinc-400 font-medium" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 transition-colors group-focus-within:text-[#FF2D20]">
                        <Mail class="h-4 w-4" />
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="block w-full pl-10 bg-zinc-50 dark:bg-zinc-800/50 border-zinc-200 dark:border-zinc-700 focus:ring-[#FF2D20] focus:border-[#FF2D20]"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="john@example.com"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <InputLabel for="password" value="Password" class="text-zinc-600 dark:text-zinc-400 font-medium" />
                    <Link
                        v-if="canResetPassword"
                        href="/forgot-password"
                        class="text-xs font-medium text-[#FF2D20] hover:text-[#E0261B] transition-colors"
                    >
                        Forgot password?
                    </Link>
                </div>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 transition-colors group-focus-within:text-[#FF2D20]">
                        <Lock class="h-4 w-4" />
                    </div>
                    <TextInput
                        id="password"
                        type="password"
                        class="block w-full pl-10 bg-zinc-50 dark:bg-zinc-800/50 border-zinc-200 dark:border-zinc-700 focus:ring-[#FF2D20] focus:border-[#FF2D20]"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <div class="flex items-center">
                <label class="flex items-center cursor-pointer group">
                    <Checkbox name="remember" v-model:checked="form.remember" class="text-[#FF2D20] focus:ring-[#FF2D20]" />
                    <span class="ms-2 text-sm text-zinc-500 dark:text-zinc-400 group-hover:text-zinc-700 dark:group-hover:text-zinc-300 transition-colors">Keep me signed in</span>
                </label>
            </div>

            <PrimaryButton
                class="w-full justify-center h-11 bg-[#FF2D20] hover:bg-[#E0261B] text-white border-none text-sm font-bold rounded-xl shadow-lg shadow-red-500/20 transition-all active:scale-[0.98] gap-2"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                <LogIn v-if="!form.processing" class="h-4 w-4" />
                <span>{{ form.processing ? 'Signing in...' : 'Sign In' }}</span>
            </PrimaryButton>
        </form>

        <div class="mt-8 pt-6 border-t border-zinc-100 dark:border-zinc-800 text-center">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Don't have an account? 
                <Link
                    href="/register"
                    class="font-bold text-[#FF2D20] hover:text-[#E0261B] transition-colors inline-flex items-center gap-1 group"
                >
                    Create account
                    <ArrowRight class="h-3 w-3 group-hover:translate-x-0.5 transition-transform" />
                </Link>
            </p>
        </div>
    </GuestLayout>
</template>

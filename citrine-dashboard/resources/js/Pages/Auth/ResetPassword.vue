<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Mail, Lock, ShieldCheck } from 'lucide-vue-next';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/reset-password', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password | CitrineOS" />

        <div class="mb-6 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-zinc-100 dark:bg-zinc-800 text-[#FF2D20] mb-4">
                <ShieldCheck class="h-6 w-6" />
            </div>
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">New Password</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Please secure your account with a new password</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div class="space-y-1.5">
                <InputLabel for="email" value="Email" class="text-zinc-600 dark:text-zinc-400 font-medium" />
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
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div class="space-y-1.5">
                <InputLabel for="password" value="New Password" class="text-zinc-600 dark:text-zinc-400 font-medium" />
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
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <div class="space-y-1.5">
                <InputLabel for="password_confirmation" value="Confirm New Password" class="text-zinc-600 dark:text-zinc-400 font-medium" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 transition-colors group-focus-within:text-[#FF2D20]">
                        <Lock class="h-4 w-4" />
                    </div>
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="block w-full pl-10 bg-zinc-50 dark:bg-zinc-800/50 border-zinc-200 dark:border-zinc-700 focus:ring-[#FF2D20] focus:border-[#FF2D20]"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.password_confirmation" />
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center h-11 bg-[#FF2D20] hover:bg-[#E0261B] text-white border-none text-sm font-bold rounded-xl shadow-lg shadow-red-500/20 transition-all active:scale-[0.98]"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Resetting...' : 'Reset Password' }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

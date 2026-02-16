<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { Mail, ArrowLeft, KeyRound } from 'lucide-vue-next';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post('/forgot-password');
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password | CitrineOS" />

        <div class="mb-6 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-zinc-100 dark:bg-zinc-800 text-[#FF2D20] mb-4">
                <KeyRound class="h-6 w-6" />
            </div>
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Reset Password</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Enter your email to receive a reset link</p>
        </div>

        <div v-if="status" class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm font-medium text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
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

            <PrimaryButton
                class="w-full justify-center h-11 bg-[#FF2D20] hover:bg-[#E0261B] text-white border-none text-sm font-bold rounded-xl shadow-lg shadow-red-500/20 transition-all active:scale-[0.98]"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                {{ form.processing ? 'Sending Link...' : 'Send Reset Link' }}
            </PrimaryButton>
        </form>

        <div class="mt-8 pt-6 border-t border-zinc-100 dark:border-zinc-800 text-center">
            <Link
                href="/login"
                class="text-sm font-bold text-zinc-500 hover:text-[#FF2D20] transition-colors inline-flex items-center gap-1 group"
            >
                <ArrowLeft class="h-4 w-4 group-hover:-translate-x-0.5 transition-transform" />
                Back to Sign in
            </Link>
        </div>
    </GuestLayout>
</template>

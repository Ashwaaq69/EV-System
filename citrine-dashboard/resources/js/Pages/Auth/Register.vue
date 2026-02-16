<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { User, Mail, Lock, UserPlus, ArrowLeft } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Create Account | CitrineOS" />

        <div class="mb-6 text-center">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Create an account</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Join the future of EV charging</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div class="space-y-1.5">
                <InputLabel for="name" value="Full Name" class="text-zinc-600 dark:text-zinc-400 font-medium" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 transition-colors group-focus-within:text-[#FF2D20]">
                        <User class="h-4 w-4" />
                    </div>
                    <TextInput
                        id="name"
                        type="text"
                        class="block w-full pl-10 bg-zinc-50 dark:bg-zinc-800/50 border-zinc-200 dark:border-zinc-700 focus:ring-[#FF2D20] focus:border-[#FF2D20]"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="John Doe"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.name" />
            </div>

            <div class="space-y-1.5">
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
                        autocomplete="username"
                        placeholder="john@example.com"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div class="space-y-1.5">
                <InputLabel for="password" value="Password" class="text-zinc-600 dark:text-zinc-400 font-medium" />
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
                <InputLabel for="password_confirmation" value="Confirm Password" class="text-zinc-600 dark:text-zinc-400 font-medium" />
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
                    class="w-full justify-center h-11 bg-[#FF2D20] hover:bg-[#E0261B] text-white border-none text-sm font-bold rounded-xl shadow-lg shadow-red-500/20 transition-all active:scale-[0.98] gap-2"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    <UserPlus v-if="!form.processing" class="h-4 w-4" />
                    <span>{{ form.processing ? 'Creating account...' : 'Create Account' }}</span>
                </PrimaryButton>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-zinc-100 dark:border-zinc-800 text-center">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Already have an account? 
                <Link
                    href="/login"
                    class="font-bold text-[#FF2D20] hover:text-[#E0261B] transition-colors inline-flex items-center gap-1 group"
                >
                    <ArrowLeft class="h-3 w-3 group-hover:-translate-x-0.5 transition-transform" />
                    Sign in
                </Link>
            </p>
        </div>
    </GuestLayout>
</template>

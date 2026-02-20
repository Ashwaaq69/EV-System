<script setup>
import { Link } from '@inertiajs/vue3';
import { Zap } from 'lucide-vue-next';
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center p-6 bg-zinc-50 dark:bg-zinc-950 selection:bg-[#FF2D20] selection:text-white">
        <!-- Background Elements -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-[#FF2D20] rounded-full blur-[120px] opacity-10 animate-pulse"></div>
            <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-[#FF2D20] rounded-full blur-[120px] opacity-10 animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <div class="w-full max-w-md z-10">
            <!-- Logo Section -->
            <div class="flex flex-col items-center mb-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
                <Link href="/" class="flex items-center gap-2 group">
                    <div class="relative w-12 h-12 flex items-center justify-center bg-[#FF2D20] rounded-2xl shadow-lg shadow-red-500/20 group-hover:scale-105 transition-transform">
                        <Zap class="w-7 h-7 text-white fill-current" />
                        <div class="absolute inset-0 bg-white/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                </Link>
                <h1 class="mt-6 text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">CitrineOS</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Smart EV Charging Solutions</p>
            </div>

            <!-- Content Card -->
            <div class="bg-white/70 dark:bg-zinc-900/50 backdrop-blur-xl border border-zinc-200 dark:border-zinc-800 shadow-2xl rounded-3xl overflow-hidden animate-in fade-in slide-in-from-bottom-6 duration-1000">
                <div class="p-8">
                    <slot />
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center animate-in fade-in duration-1000 delay-500">
                <p class="text-xs text-zinc-400">
                    &copy; 2026 CitrineOS. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-in {
    animation-fill-mode: both;
}
</style>

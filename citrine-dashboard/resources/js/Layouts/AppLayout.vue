<template>
  <div class="flex h-screen bg-zinc-50 dark:bg-zinc-950">
    <!-- Sidebar -->
    <aside class="w-64 border-r border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 shadow-sm">
      <div class="flex h-full flex-col">
        <div class="flex h-16 items-center px-6">
          <Link 
            :href="$page.props.auth.user.role === 'admin' ? '/dashboard' : '/client/portal'"
            @click="navigateTo($event, $page.props.auth.user.role === 'admin' ? '/dashboard' : '/client/portal')"
            class="text-xl font-bold tracking-tighter text-[#FF2D20] flex items-center gap-2 hover:opacity-80 transition-opacity"
          >
            <div class="w-2 h-6 bg-[#FF2D20] rounded-full"></div>
            CitrineOS
          </Link>
        </div>
        
        <div class="px-3 py-2">
          <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500 px-3 mb-2">Main Menu</p>
          <nav class="space-y-1">
            <!-- Admin Links -->
            <template v-if="$page.props.auth.user.role === 'admin'">
              <Link
                href="/dashboard"
                @click="navigateTo($event, '/dashboard')"
                :class="[
                  'group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-all duration-200',
                  $page.url === '/dashboard' 
                    ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white' 
                    : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white'
                ]"
              >
                <Zap class="mr-3 h-4 w-4" :class="$page.url === '/dashboard' ? 'text-[#FF2D20]' : ''" />
                Chargers
              </Link>

              <Link
                href="/analytics"
                @click="navigateTo($event, '/analytics')"
                :class="[
                  'group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-all duration-200',
                  $page.url === '/analytics' 
                    ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white' 
                    : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white'
                ]"
              >
                <BarChart3 class="mr-3 h-4 w-4" :class="$page.url === '/analytics' ? 'text-[#FF2D20]' : ''" />
                Analytics
              </Link>

              <Link
                href="/transactions"
                @click="navigateTo($event, '/transactions')"
                :class="[
                  'group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-all duration-200',
                  $page.url === '/transactions' 
                    ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white' 
                    : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white'
                ]"
              >
                <ClipboardList class="mr-3 h-4 w-4" :class="$page.url === '/transactions' ? 'text-[#FF2D20]' : ''" />
                Transactions
              </Link>
            </template>

            <!-- Client Links -->
            <template v-else>
              <Link
                href="/client/portal"
                @click="navigateTo($event, '/client/portal')"
                :class="[
                  'group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-all duration-200',
                  $page.url === '/client/portal' || $page.url === '/'
                    ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white' 
                    : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white'
                ]"
              >
                <Zap class="mr-3 h-4 w-4" :class="$page.url === '/client/portal' ? 'text-[#FF2D20]' : ''" />
                My Portal
              </Link>
            </template>
          </nav>
        </div>

        <div class="mt-auto p-4" v-if="$page.props.auth.user">
          <Separator class="mb-4 bg-zinc-100 dark:bg-zinc-800" />
          
          <div class="flex items-center justify-between group">
            <div class="flex items-center gap-3">
              <Avatar class="h-9 w-9 border border-zinc-200 dark:border-zinc-800">
                <AvatarImage :src="`https://avatar.vercel.sh/${$page.props.auth.user.email}.png`" />
                <AvatarFallback class="bg-[#FF2D20] text-white font-bold">
                  {{ $page.props.auth.user.name?.charAt(0) || 'U' }}
                </AvatarFallback>
              </Avatar>
              <div class="text-xs">
                <p class="font-semibold text-zinc-900 dark:text-white truncate max-w-[100px]">
                  {{ $page.props.auth.user.name }}
                </p>
                <p class="text-zinc-500 dark:text-zinc-400 truncate max-w-[100px]">
                  {{ $page.props.auth.user.email }}
                </p>
              </div>
            </div>
            
            <button
              @click="handleLogout"
              class="h-8 w-8 flex items-center justify-center text-zinc-400 hover:text-[#FF2D20] hover:bg-red-50 dark:hover:bg-red-950/20 rounded-md transition-all duration-200"
              title="Logout"
            >
              <LogOut class="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
      <div class="h-full">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Separator } from '@/Components/ui/separator';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Zap, BarChart3, ClipboardList, LogOut } from 'lucide-vue-next';

const navigateTo = (e, href) => {
    // Check if we are in standalone mode
    const isStandalone = !document.getElementById('app')?.dataset.page;
    
    if (isStandalone) {
        e.preventDefault();
        // Use vue-router in standalone mode
        import('../router').then(module => {
            const router = module.default;
            router.push(href);
        });
    }
    // In Inertia mode, let the <Link> component handle it normally
};

const handleLogout = () => {
    // Check if we are in standalone mode
    const isStandalone = !document.getElementById('app')?.dataset.page;
    
    if (isStandalone) {
        window.location.href = '/login';
    } else {
        router.post('/logout');
    }
};
</script>

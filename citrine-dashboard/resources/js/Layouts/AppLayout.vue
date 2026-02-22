<template>
  <div class="flex h-screen bg-zinc-50 dark:bg-zinc-950 overflow-hidden">
    <!-- Mobile Header -->
    <header class="lg:hidden fixed top-0 left-0 right-0 h-16 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between px-4 z-50">
      <Link 
        :href="$page.props.auth?.user?.role === 'admin' ? '/dashboard' : '/client/portal'"
        class="text-xl font-bold tracking-tighter text-[#FF2D20] flex items-center gap-2"
      >
        <div class="w-1.5 h-5 bg-[#FF2D20] rounded-full"></div>
        CitrineOS
      </Link>
      <button 
        @click="isMobileMenuOpen = true"
        class="p-2 text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
      >
        <Menu class="h-6 w-6" />
      </button>
    </header>

    <!-- Sidebar (Desktop) -->
    <aside class="hidden lg:flex w-64 border-r border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 shadow-sm flex-col">
      <div class="flex h-16 items-center px-6">
        <Link 
          :href="$page.props.auth?.user?.role === 'admin' ? '/dashboard' : '/client/portal'"
          @click="navigateTo($event, $page.props.auth?.user?.role === 'admin' ? '/dashboard' : '/client/portal')"
          class="text-xl font-bold tracking-tighter text-[#FF2D20] flex items-center gap-2 hover:opacity-80 transition-opacity"
        >
          <div class="w-2 h-6 bg-[#FF2D20] rounded-full"></div>
          CitrineOS
        </Link>
      </div>
      
      <div class="px-3 py-2 flex-1 overflow-y-auto">
        <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500 px-3 mb-2">Main Menu</p>
        <nav class="space-y-1">
          <!-- Navigation Content (Shared) -->
          <NavLinks :url="$page.url" :role="$page.props.auth?.user?.role" @navigate="navigateTo" />
        </nav>
      </div>

      <!-- User Footer -->
      <div class="p-4 border-t border-zinc-100 dark:border-zinc-800" v-if="$page.props.auth.user">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <Avatar class="h-9 w-9 border border-zinc-200 dark:border-zinc-800">
              <AvatarImage :src="`https://avatar.vercel.sh/${$page.props.auth.user.email}.png`" />
              <AvatarFallback class="bg-[#FF2D20] text-white font-bold text-xs">
                {{ $page.props.auth.user.name?.charAt(0) || 'U' }}
              </AvatarFallback>
            </Avatar>
            <div class="text-xs">
              <p class="font-semibold text-zinc-900 dark:text-white truncate max-w-[100px]">{{ $page.props.auth.user.name }}</p>
              <p class="text-zinc-500 dark:text-zinc-400 truncate max-w-[100px]">{{ $page.props.auth.user.email }}</p>
            </div>
          </div>
          <div class="flex items-center gap-1">
            <button @click="toggleDark" class="h-8 w-8 flex items-center justify-center text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-md transition-all">
              <Moon v-if="!isDark" class="h-4 w-4" />
              <Sun v-else class="h-4 w-4" />
            </button>
            <button @click="handleLogout" class="h-8 w-8 flex items-center justify-center text-zinc-400 hover:text-[#FF2D20] hover:bg-red-50 dark:hover:bg-red-950/20 rounded-md transition-all" title="Logout">
              <LogOut class="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>
    </aside>

    <!-- Mobile Drawer Overlay -->
    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="isMobileMenuOpen" class="lg:hidden fixed inset-0 bg-black/50 z-[60]" @click="isMobileMenuOpen = false"></div>
    </Transition>

    <!-- Mobile Drawer Sidebar -->
    <Transition
      enter-active-class="transition-transform duration-300 ease-out"
      enter-from-class="-translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition-transform duration-200 ease-in"
      leave-from-class="translate-x-0"
      leave-to-class="-translate-x-full"
    >
      <aside v-if="isMobileMenuOpen" class="lg:hidden fixed inset-y-0 left-0 w-72 bg-white dark:bg-zinc-900 shadow-xl z-[70] flex flex-col">
        <div class="flex h-16 items-center justify-between px-6 border-b border-zinc-100 dark:border-zinc-800">
          <Link 
            :href="$page.props.auth?.user?.role === 'admin' ? '/dashboard' : '/client/portal'"
            class="text-xl font-bold tracking-tighter text-[#FF2D20] flex items-center gap-2"
          >
            <div class="w-1.5 h-5 bg-[#FF2D20] rounded-full"></div>
            CitrineOS
          </Link>
          <button @click="isMobileMenuOpen = false" class="text-zinc-400 hover:text-zinc-600 dark:text-zinc-500 dark:hover:text-zinc-300">
            <X class="h-5 w-5" />
          </button>
        </div>
        
        <div class="px-3 py-6 flex-1 overflow-y-auto">
          <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500 px-3 mb-4">Main Menu</p>
          <nav class="space-y-2">
            <NavLinks :url="$page.url" :role="$page.props.auth?.user?.role" @navigate="(e, href) => { isMobileMenuOpen = false; navigateTo(e, href); }" />
          </nav>
        </div>

        <!-- User Footer -->
        <div class="p-4 border-t border-zinc-100 dark:border-zinc-800" v-if="$page.props.auth.user">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <Avatar class="h-9 w-9 border border-zinc-200 dark:border-zinc-800">
                <AvatarImage :src="`https://avatar.vercel.sh/${$page.props.auth.user.email}.png`" />
                <AvatarFallback class="bg-[#FF2D20] text-white font-bold text-xs">
                  {{ $page.props.auth.user.name?.charAt(0) || 'U' }}
                </AvatarFallback>
              </Avatar>
              <div class="text-xs">
                <p class="font-semibold text-zinc-900 dark:text-white truncate max-w-[100px]">{{ $page.props.auth.user.name }}</p>
                <p class="text-zinc-500 dark:text-zinc-400 truncate max-w-[100px]">{{ $page.props.auth.user.email }}</p>
              </div>
            </div>
            <div class="flex items-center gap-1">
              <button @click="toggleDark" class="h-8 w-8 flex items-center justify-center text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-md transition-all">
                <Moon v-if="!isDark" class="h-4 w-4" />
                <Sun v-else class="h-4 w-4" />
              </button>
              <button @click="handleLogout" class="h-8 w-8 flex items-center justify-center text-zinc-400 hover:text-[#FF2D20] hover:bg-red-50 dark:hover:bg-red-950/20 rounded-md transition-all" title="Logout">
                <LogOut class="h-4 w-4" />
              </button>
            </div>
          </div>
        </div>
      </aside>
    </Transition>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto pt-16 lg:pt-0">
      <div class="h-full">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, defineComponent } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { Separator } from '@/Components/ui/separator';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Zap, BarChart3, ClipboardList, LogOut, Users, Moon, Sun, Menu, X } from 'lucide-vue-next';

// Shared Components for Responsiveness
const NavLinks = defineComponent({
  props: ['url', 'role'],
  emits: ['navigate'],
  template: `
    <template v-if="role === 'admin'">
      <Link href="/dashboard" @click="$emit('navigate', $event, '/dashboard')" :class="[
        'group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-all duration-200',
        url === '/dashboard' ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white'
      ]">
        <Zap class="mr-3 h-4 w-4" :class="url === '/dashboard' ? 'text-[#FF2D20]' : ''" />
        Chargers
      </Link>
      <Link href="/analytics" @click="$emit('navigate', $event, '/analytics')" :class="[
        'group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-all duration-200',
        url === '/analytics' ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white'
      ]">
        <BarChart3 class="mr-3 h-4 w-4" :class="url === '/analytics' ? 'text-[#FF2D20]' : ''" />
        Analytics
      </Link>
      <Link href="/transactions" @click="$emit('navigate', $event, '/transactions')" :class="[
        'group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-all duration-200',
        url === '/transactions' ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white'
      ]">
        <ClipboardList class="mr-3 h-4 w-4" :class="url === '/transactions' ? 'text-[#FF2D20]' : ''" />
        Transactions
      </Link>
      <Link href="/users" @click="$emit('navigate', $event, '/users')" :class="[
        'group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-all duration-200',
        url === '/users' ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white'
      ]">
        <Users class="mr-3 h-4 w-4" :class="url === '/users' ? 'text-[#FF2D20]' : ''" />
        Users
      </Link>
    </template>
    <template v-else>
      <Link href="/client/portal" @click="$emit('navigate', $event, '/client/portal')" :class="[
        'group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-all duration-200',
        url === '/client/portal' || url === '/' ? 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-white' : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white'
      ]">
        <Zap class="mr-3 h-4 w-4" :class="url === '/client/portal' ? 'text-[#FF2D20]' : ''" />
        My Portal
      </Link>
    </template>
  `
});

const UserFooter = defineComponent({
  props: ['user', 'isDark'],
  emits: ['toggleDark', 'logout'],
  template: `
    <div class="flex items-center justify-between group">
      <div class="flex items-center gap-3">
        <Avatar class="h-9 w-9 border border-zinc-200 dark:border-zinc-800">
          <AvatarImage :src="'https://avatar.vercel.sh/' + user.email + '.png'" />
          <AvatarFallback class="bg-[#FF2D20] text-white font-bold">
            {{ user.name?.charAt(0) || 'U' }}
          </AvatarFallback>
        </Avatar>
        <div class="text-xs">
          <p class="font-semibold text-zinc-900 dark:text-white truncate max-w-[100px]">
            {{ user.name }}
          </p>
          <p class="text-zinc-500 dark:text-zinc-400 truncate max-w-[100px]">
            {{ user.email }}
          </p>
        </div>
      </div>
      
      <div class="flex items-center gap-1">
        <button @click="$emit('toggleDark')" class="h-8 w-8 flex items-center justify-center text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-md transition-all duration-200">
          <Moon v-if="!isDark" class="h-4 w-4" />
          <Sun v-else class="h-4 w-4" />
        </button>
        <button @click="$emit('logout')" class="h-8 w-8 flex items-center justify-center text-zinc-400 hover:text-[#FF2D20] hover:bg-red-50 dark:hover:bg-red-950/20 rounded-md transition-all duration-200">
          <LogOut class="h-4 w-4" />
        </button>
      </div>
    </div>
  `
});

// Mobile State
const isMobileMenuOpen = ref(false);

// Dark Mode
const isDark = ref(false);

onMounted(() => {
    // Restore saved preference
    const saved = localStorage.getItem('theme');
    if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        isDark.value = true;
    }
});

const toggleDark = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

const navigateTo = (e, href) => {
    const isStandalone = !document.getElementById('app')?.dataset.page;
    if (isStandalone) {
        e.preventDefault();
        import('../router').then(module => {
            const router = module.default;
            router.push(href);
        });
    }
};

const handleLogout = () => {
    const isStandalone = !document.getElementById('app')?.dataset.page;
    if (isStandalone) {
        window.location.href = '/login';
    } else {
        router.post('/logout');
    }
};
</script>

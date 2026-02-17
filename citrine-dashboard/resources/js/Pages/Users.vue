<template>
  <AppLayout title="User Management">
    <div class="p-8 max-w-7xl mx-auto space-y-8 text-zinc-900 dark:text-zinc-100">
      <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">User Management</h1>
          <p class="text-zinc-500 dark:text-zinc-400">View and manage all system users and their roles.</p>
        </div>
        <Button class="bg-[#FF2D20] hover:bg-[#E0261B] text-white border-none gap-2" @click="openAddUserModal">
          <UserPlus class="h-4 w-4" />
          Add New User
        </Button>
      </header>

      <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card class="border-none shadow-sm dark:bg-zinc-900 overflow-hidden relative group">
          <CardHeader class="flex flex-row items-center justify-between pb-2">
            <CardTitle class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Total Users</CardTitle>
            <UsersIcon class="h-4 w-4 text-blue-500" />
          </CardHeader>
          <CardContent>
            <div class="text-3xl font-bold">{{ users.length }}</div>
            <p class="text-xs text-zinc-400 mt-1">Platform-wide accounts</p>
          </CardContent>
          <div class="absolute bottom-0 left-0 h-1 bg-blue-500 w-full transform translate-y-1 group-hover:translate-y-0 transition-transform"></div>
        </Card>

        <Card class="border-none shadow-sm dark:bg-zinc-900 overflow-hidden relative group">
          <CardHeader class="flex flex-row items-center justify-between pb-2">
            <CardTitle class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Admins</CardTitle>
            <ShieldCheck class="h-4 w-4 text-[#FF2D20]" />
          </CardHeader>
          <CardContent>
            <div class="text-3xl font-bold">{{ adminCount }}</div>
            <p class="text-xs text-zinc-400 mt-1">Full system access</p>
          </CardContent>
          <div class="absolute bottom-0 left-0 h-1 bg-[#FF2D20] w-full transform translate-y-1 group-hover:translate-y-0 transition-transform"></div>
        </Card>

        <Card class="border-none shadow-sm dark:bg-zinc-900 overflow-hidden relative group">
          <CardHeader class="flex flex-row items-center justify-between pb-2">
            <CardTitle class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Clients</CardTitle>
            <UserIcon class="h-4 w-4 text-emerald-500" />
          </CardHeader>
          <CardContent>
            <div class="text-3xl font-bold">{{ clientCount }}</div>
            <p class="text-xs text-zinc-400 mt-1">Energy consumers</p>
          </CardContent>
          <div class="absolute bottom-0 left-0 h-1 bg-emerald-500 w-full transform translate-y-1 group-hover:translate-y-0 transition-transform"></div>
        </Card>
      </section>

      <Card class="border-none shadow-sm dark:bg-zinc-900">
        <CardHeader>
          <CardTitle>User Directory</CardTitle>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow class="hover:bg-transparent border-zinc-100 dark:border-zinc-800">
                <TableHead class="text-[10px] font-bold uppercase text-zinc-400">User</TableHead>
                <TableHead class="text-[10px] font-bold uppercase text-zinc-400">Role</TableHead>
                <TableHead class="text-[10px] font-bold uppercase text-zinc-400">Created</TableHead>
                <TableHead class="text-right text-[10px] font-bold uppercase text-zinc-400">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="user in users" :key="user.id" class="group border-zinc-100 dark:border-zinc-800">
                <TableCell>
                  <div class="flex items-center gap-3">
                    <Avatar class="h-8 w-8">
                      <AvatarImage :src="`https://avatar.vercel.sh/${user.email}.png`" />
                      <AvatarFallback>{{ user.name.charAt(0) }}</AvatarFallback>
                    </Avatar>
                    <div>
                      <p class="font-bold text-sm">{{ user.name }}</p>
                      <p class="text-xs text-zinc-500">{{ user.email }}</p>
                    </div>
                  </div>
                </TableCell>
                <TableCell>
                  <Badge :variant="user.role === 'admin' ? 'destructive' : 'secondary'" class="capitalize font-medium text-[10px] tracking-wide">
                    {{ user.role }}
                  </Badge>
                </TableCell>
                <TableCell class="text-xs text-zinc-500 font-medium">
                  {{ formatDate(user.created_at) }}
                </TableCell>
                <TableCell class="text-right">
                  <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                      <Button variant="ghost" size="icon" class="h-8 w-8 p-0">
                        <MoreHorizontal class="h-4 w-4" />
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-40">
                      <DropdownMenuItem v-if="user.id !== authId" class="text-red-600 dark:text-red-400" @click="confirmDelete(user)">
                        <Trash class="mr-2 h-4 w-4" /> Delete User
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </CardContent>
      </Card>

      <!-- Add User Dialog -->
      <Dialog :open="isModalOpen" @update:open="isModalOpen = $event">
        <DialogContent class="sm:max-w-[425px] dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800">
          <DialogHeader>
            <DialogTitle>Add New User</DialogTitle>
            <DialogDescription>
              Create a new account for an admin or client.
            </DialogDescription>
          </DialogHeader>
          <form @submit.prevent="submitForm" class="grid gap-6 py-4">
            <div class="grid gap-2">
              <Label for="name" class="text-xs font-bold uppercase tracking-wider text-zinc-500">Full Name</Label>
              <Input id="name" v-model="form.name" placeholder="John Doe" class="dark:bg-zinc-800" />
              <p v-if="form.errors.name" class="text-xs text-red-500 italic">{{ form.errors.name }}</p>
            </div>
            <div class="grid gap-2">
              <Label for="email" class="text-xs font-bold uppercase tracking-wider text-zinc-500">Email Address</Label>
              <Input id="email" type="email" v-model="form.email" placeholder="john@example.com" class="dark:bg-zinc-800" />
              <p v-if="form.errors.email" class="text-xs text-red-500 italic">{{ form.errors.email }}</p>
            </div>
            <div class="grid gap-2">
              <Label for="role" class="text-xs font-bold uppercase tracking-wider text-zinc-500">Account Role</Label>
              <select id="role" v-model="form.role" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:bg-zinc-800 dark:border-zinc-700">
                <option value="client">Client</option>
                <option value="admin">Administrator</option>
              </select>
            </div>
            <div class="grid gap-2">
              <Label for="password" class="text-xs font-bold uppercase tracking-wider text-zinc-500">Password</Label>
              <Input id="password" type="password" v-model="form.password" placeholder="••••••••" class="dark:bg-zinc-800" />
              <p v-if="form.errors.password" class="text-xs text-red-500 italic">{{ form.errors.password }}</p>
            </div>
            <DialogFooter class="mt-4">
              <Button type="submit" :disabled="form.processing" class="bg-[#FF2D20] hover:bg-[#E0261B] text-white border-none w-full shadow-lg shadow-red-200 dark:shadow-none">
                {{ form.processing ? 'Creating...' : 'Create User Account' }}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/Components/ui/table';
import { Badge } from '@/Components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { 
  DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem 
} from '@/Components/ui/dropdown-menu';
import { 
  Users as UsersIcon, UserPlus, ShieldCheck, User as UserIcon, 
  MoreHorizontal, Trash 
} from 'lucide-vue-next';

const props = defineProps({
  users: Array,
});

const authId = computed(() => usePage().props.auth.user.id);
const adminCount = computed(() => props.users.filter(u => u.role === 'admin').length);
const clientCount = computed(() => props.users.filter(u => u.role === 'client').length);

const isModalOpen = ref(false);

const form = useForm({
  name: '',
  email: '',
  role: 'client',
  password: '',
});

const openAddUserModal = () => {
  form.reset();
  form.clearErrors();
  isModalOpen.value = true;
};

const submitForm = () => {
  form.post('/users', {
    onSuccess: () => {
      isModalOpen.value = false;
      form.reset();
    },
  });
};

const confirmDelete = (user) => {
  if (confirm(`Are you sure you want to delete ${user.name}? This action cannot be undone.`)) {
    form.delete(`/users/${user.id}`);
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '—';
  return new Date(dateString).toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};
</script>

<template>
  <AppLayout>
    <div class="p-8 max-w-7xl mx-auto space-y-8">
      <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Dashboard Overview</h1>
          <p class="text-zinc-500 dark:text-zinc-400">Real-time status of your charging network</p>
        </div>
        <div class="flex items-center gap-3">
          <Button variant="outline" size="sm" @click="$inertia.reload()" class="gap-2">
            <RefreshCw class="h-4 w-4" />
            Sync Status
          </Button>
          
          <Dialog v-model:open="isAddModalOpen">
            <DialogTrigger as-child>
              <Button size="sm" class="bg-[#FF2D20] hover:bg-[#E0261B] text-white !border-none">
                <Plus class="mr-2 h-4 w-4" />
                Add Charger
              </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px] dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800">
              <DialogHeader>
                <DialogTitle>Add New Charger</DialogTitle>
                <DialogDescription>
                  Register a new charge point to the CitrineOS network.
                </DialogDescription>
              </DialogHeader>
              <form @submit.prevent="submitForm" class="grid gap-6 py-4">
                <div class="grid gap-2">
                  <Label for="identifier" class="text-xs font-bold uppercase tracking-wider text-zinc-500">Charger Identifier</Label>
                  <Input
                    id="identifier"
                    v-model="form.identifier"
                    placeholder="e.g. CS-999"
                    class="dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 font-medium"
                  />
                  <p v-if="form.errors.identifier" class="text-xs text-red-500 font-medium italic">{{ form.errors.identifier }}</p>
                </div>
                <div class="grid gap-2">
                  <Label for="location" class="text-xs font-bold uppercase tracking-wider text-zinc-500">Location Name</Label>
                  <Input
                    id="location"
                    v-model="form.location_name"
                    placeholder="e.g. Main Station B"
                    class="dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 font-medium"
                  />
                  <p v-if="form.errors.location_name" class="text-xs text-red-500 font-medium italic">{{ form.errors.location_name }}</p>
                </div>
                <DialogFooter class="mt-4">
                  <Button type="submit" :disabled="form.processing" class="bg-[#FF2D20] hover:bg-[#E0261B] text-white border-none w-full shadow-lg shadow-red-200 dark:shadow-none">
                    {{ form.processing ? 'Registering...' : 'Register Charger' }}
                  </Button>
                </DialogFooter>
              </form>
            </DialogContent>
          </Dialog>
        </div>
      </header>

      <!-- Stats Cards -->
      <section class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <Card class="relative overflow-hidden group hover:shadow-md transition-shadow">
          <CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
            <CardTitle class="text-sm font-medium text-zinc-500">Online Units</CardTitle>
            <Zap class="h-4 w-4 text-green-500" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-green-600">{{ onlineChargers }}</div>
            <p class="text-xs text-zinc-400 mt-1">Ready for charging</p>
          </CardContent>
          <div class="absolute bottom-0 left-0 h-1 bg-green-500 w-full transform translate-y-1 group-hover:translate-y-0 transition-transform"></div>
        </Card>

        <Card class="relative overflow-hidden group hover:shadow-md transition-shadow">
          <CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
            <CardTitle class="text-sm font-medium text-zinc-500">Offline Units</CardTitle>
            <PowerOff class="h-4 w-4 text-red-500" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-red-600">{{ offlineChargers }}</div>
            <p class="text-xs text-zinc-400 mt-1">Maintenance required</p>
          </CardContent>
          <div class="absolute bottom-0 left-0 h-1 bg-red-500 w-full transform translate-y-1 group-hover:translate-y-0 transition-transform"></div>
        </Card>

        <Card class="relative overflow-hidden group hover:shadow-md transition-shadow bg-zinc-900 text-white dark:bg-zinc-800">
          <CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
            <CardTitle class="text-sm font-medium text-zinc-300">Total Network</CardTitle>
            <Activity class="h-4 w-4 text-[#FF2D20]" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ totalChargers }}</div>
            <div class="mt-4 space-y-2">
              <div class="flex justify-between text-[10px] uppercase font-bold text-zinc-500">
                <span>Utilization</span>
                <span>{{ utilization }}%</span>
              </div>
              <div class="h-1.5 w-full bg-zinc-800 dark:bg-zinc-700 rounded-full overflow-hidden">
                <div :style="{ width: utilization + '%' }" class="h-full bg-gradient-to-r from-[#FF2D20] to-orange-500 transition-all duration-1000"></div>
              </div>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Main Layout -->
      <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
        <!-- Chargers List -->
        <Card class="lg:col-span-3 border-none shadow-sm shadow-zinc-200 dark:shadow-none bg-white dark:bg-zinc-900">
          <CardHeader class="flex flex-row items-center justify-between">
            <div>
              <CardTitle>Charger Status</CardTitle>
              <CardDescription>System-wide monitoring station</CardDescription>
            </div>
            <div class="flex items-center gap-4">
              <div class="flex items-center gap-2">
                <span class="text-xs text-zinc-500 font-medium">Items:</span>
                <select 
                  v-model="perPageInternal" 
                  @change="updatePerPage"
                  class="rounded-md border-zinc-200 py-1 text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-300 focus:ring-[#FF2D20]"
                >
                  <option :value="5">5</option>
                  <option :value="10">10</option>
                  <option :value="25">25</option>
                </select>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead class="w-[100px]">Identifier</TableHead>
                  <TableHead>Location</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead class="text-right">Action</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="charger in chargers.data" :key="charger.id" class="group">
                  <TableCell class="font-medium">
                    <div class="flex items-center gap-2">
                      <div :class="['w-2 h-2 rounded-full', charger.online ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]' : 'bg-zinc-300']"></div>
                      {{ charger.name }}
                    </div>
                  </TableCell>
                  <TableCell class="text-zinc-500">{{ charger.location }}</TableCell>
                  <TableCell>
                    <Badge :variant="charger.online ? 'default' : 'secondary'" :class="charger.online ? 'bg-green-100 text-green-700 hover:bg-green-100' : ''">
                      {{ charger.online ? 'Active' : 'Offline' }}
                    </Badge>
                  </TableCell>
                  <TableCell class="text-right">
                    <Button variant="ghost" size="sm" class="opacity-0 group-hover:opacity-100 transition-opacity">
                      Manage
                    </Button>
                  </TableCell>
                </TableRow>
                <TableRow v-if="chargers.data.length === 0">
                  <TableCell colspan="4" class="h-24 text-center text-zinc-500 italic">
                    No chargers found in network.
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
            
            <div class="mt-6 flex items-center justify-between border-t pt-4">
              <p class="text-xs text-zinc-500 font-medium">
                Page <span class="text-zinc-900 border dark:text-zinc-300">{{ chargers.current_page }}</span> of {{ chargers.last_page }}
              </p>
              <div class="flex gap-2">
                <Button 
                  variant="outline" 
                  size="sm"
                  @click="navigate(chargers.prev_page_url)"
                  :disabled="!chargers.prev_page_url"
                >
                  <ChevronLeft class="h-4 w-4" />
                </Button>
                <Button 
                  variant="outline" 
                  size="sm"
                  @click="navigate(chargers.next_page_url)"
                  :disabled="!chargers.next_page_url"
                >
                  <ChevronRight class="h-4 w-4" />
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle class="text-base">System Health</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="flex items-start gap-3">
                <div class="mt-1 h-2 w-2 rounded-full bg-green-500"></div>
                <div>
                  <p class="text-xs font-semibold">Core Connectivity</p>
                  <p class="text-[10px] text-zinc-500">99.9% uptime (24h)</p>
                </div>
              </div>
              <Separator />
              <div class="flex items-start gap-3">
                <div class="mt-1 h-2 w-2 rounded-full bg-green-500"></div>
                <div>
                  <p class="text-xs font-semibold">Database Latency</p>
                  <p class="text-[10px] text-zinc-500">12ms average</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card class="bg-gradient-to-br from-[#FF2D20] to-orange-600 text-white border-none shadow-lg shadow-red-200 dark:shadow-none">
            <CardHeader>
              <CardTitle class="text-white text-base">Carbon Savings</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="text-3xl font-bold">12.4t</div>
              <p class="text-[10px] text-zinc-100 opacity-80 mt-1">Estimated CO2 offset this month</p>
              <Button class="w-full mt-4 bg-white/20 hover:bg-white/30 text-white border-none transition-all">
                View Report
              </Button>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/Components/ui/table';
import { Badge } from '@/Components/ui/badge';
import { Separator } from '@/Components/ui/separator';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { 
  RefreshCw, 
  Zap, 
  PowerOff, 
  Activity, 
  ChevronLeft, 
  ChevronRight,
  Plus
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps({
  chargers: {
    type: Object,
    required: true
  },
  onlineChargers: {
    type: Number,
    default: 0,
  },
  offlineChargers: {
    type: Number,
    default: 0,
  },
});

const isAddModalOpen = ref(false);
const form = useForm({
  identifier: '',
  location_name: '',
});

const submitForm = () => {
  form.post(route('dashboard.store'), {
    onSuccess: () => {
      isAddModalOpen.value = false;
      form.reset();
    },
  });
};

const perPageInternal = ref(props.chargers.per_page || 10);

const totalChargers = computed(() => props.onlineChargers + props.offlineChargers);
const utilization = computed(() => {
  const total = totalChargers.value || 1;
  return Math.round((props.onlineChargers / total) * 100);
});

const updatePerPage = () => {
  router.get('/dashboard', { per_page: perPageInternal.value }, { preserveState: true });
};

const navigate = (url) => {
  if (url) {
    router.get(url, { per_page: perPageInternal.value }, { preserveState: true });
  }
};
</script>


<style scoped>
.bg-gradient-header {
  background: linear-gradient(90deg, #ff7a60 0%, #ff2d20 50%, #6d28d9 100%);
}
</style>

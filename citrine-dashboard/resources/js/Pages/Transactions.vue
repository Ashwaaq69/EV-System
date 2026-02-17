<template>
  <AppLayout>
    <div class="p-8 max-w-7xl mx-auto space-y-8">
      <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Transaction Control</h1>
          <p class="text-zinc-500 dark:text-zinc-400">Monitor and manage real-time charging sessions</p>
        </div>
        <div class="flex items-center gap-2">
          <Badge variant="outline" class="px-3 py-1 gap-2 border-zinc-200 text-zinc-600 dark:border-zinc-800 dark:text-zinc-400">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            System Live
          </Badge>
        </div>
      </header>

      <!-- Sessions Table -->
      <Card class="border-none shadow-sm shadow-zinc-200 dark:shadow-none bg-white dark:bg-zinc-900">
        <CardHeader class="flex flex-row items-center justify-between pb-4">
          <div>
            <CardTitle>Charging Sessions</CardTitle>
            <CardDescription>Comprehensive log of user interactions</CardDescription>
          </div>
          <div class="flex items-center gap-4">
            <div class="relative w-48 md:w-64">
              <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-zinc-500" />
              <Input 
                placeholder="Search transactions..." 
                v-model="searchQuery"
                class="pl-9 h-9 border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/50"
              />
            </div>
            <div class="flex items-center gap-2">
              <label class="text-xs text-zinc-500 uppercase font-bold tracking-tighter">View</label>
              <select 
                v-model="perPage" 
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
          <div class="rounded-md border border-zinc-100 dark:border-zinc-800">
            <Table>
              <TableHeader class="bg-zinc-50/50 dark:bg-zinc-800/50">
                <TableRow>
                  <TableHead class="w-[180px]">User</TableHead>
                  <TableHead>Charger / Location</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Timeline</TableHead>
                  <TableHead>Energy / Cost</TableHead>
                  <TableHead class="text-right">Action</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="session in sessions.data" :key="session.id" class="group hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50">
                  <TableCell class="font-medium">
                    <div class="flex items-center gap-3">
                      <div class="h-8 w-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-bold text-zinc-500">
                        {{ session.user?.charAt(0) }}
                      </div>
                      <span class="text-zinc-900 dark:text-zinc-100 text-sm">{{ session.user }}</span>
                    </div>
                  </TableCell>
                  <TableCell>
                    <div class="flex flex-col">
                      <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ session.charger }}</span>
                      <span class="text-[10px] text-zinc-500 uppercase tracking-tight">{{ session.location }}</span>
                    </div>
                  </TableCell>
                  <TableCell>
                    <Badge 
                      :variant="session.status === 'active' ? 'default' : 'outline'"
                      :class="session.status === 'active' ? 'bg-green-500/10 text-green-600 border-green-200 hover:bg-green-500/10' : 'text-zinc-500 border-zinc-200'"
                    >
                      {{ session.status }}
                    </Badge>
                  </TableCell>
                  <TableCell class="text-zinc-500 text-xs">
                    <div class="flex items-center gap-2">
                      <Clock class="h-3 w-3" />
                      {{ formatDate(session.start_time) }}
                    </div>
                  </TableCell>
                  <TableCell>
                    <div class="flex flex-col">
                      <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ session.kwh }} <span class="text-[10px] text-zinc-400">kWh</span></span>
                      <span class="text-[10px] font-bold text-green-600">RM {{ session.cost }}</span>
                    </div>
                  </TableCell>
                  <TableCell class="text-right">
                    <div class="flex items-center justify-end gap-2">
                       <Button
                         v-if="session.status === 'active'"
                         variant="destructive"
                         size="sm"
                         @click="stopSession(session.id)"
                         class="h-8 text-[11px] font-bold uppercase bg-red-50 text-red-600 border-red-100 hover:bg-red-600 hover:text-white transition-all px-3"
                       >
                         Stop
                       </Button>
                       <Button 
                         variant="ghost" 
                         size="sm" 
                         @click="downloadInvoice(session)"
                         class="h-8 px-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50"
                         title="Download Receipt"
                       >
                         <Download class="h-4 w-4 mr-1" />
                         <span class="text-[10px] font-bold uppercase">Receipt</span>
                       </Button>
                    </div>
                  </TableCell>
                </TableRow>
                <TableRow v-if="sessions.data.length === 0">
                  <TableCell colspan="6" class="h-24 text-center text-zinc-500">
                    No transactions recorded.
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>

          <!-- Pagination -->
          <div class="mt-6 flex items-center justify-between">
            <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">
              Showing {{ sessions.from }}-{{ sessions.to }} of {{ sessions.total }}
            </p>
            <div class="flex items-center gap-4">
              <span class="text-xs text-zinc-500">Page {{ sessions.current_page }}</span>
              <div class="flex gap-2">
                <Button 
                  variant="outline" 
                  size="icon" 
                  class="h-8 w-8"
                  @click="navigate(sessions.prev_page_url)"
                  :disabled="!sessions.prev_page_url"
                >
                  <ChevronLeft class="h-4 w-4" />
                </Button>
                <Button 
                  variant="outline" 
                  size="icon" 
                  class="h-8 w-8"
                  @click="navigate(sessions.next_page_url)"
                  :disabled="!sessions.next_page_url"
                >
                  <ChevronRight class="h-4 w-4" />
                </Button>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/Components/ui/table';
import { Badge } from '@/Components/ui/badge';
import { Input } from '@/Components/ui/input';
import { Clock, ChevronLeft, ChevronRight, MoreHorizontal, Search, Download, FileText } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  sessions: Object
});

const searchQuery = ref(new URLSearchParams(window.location.search).get('search') || '');
const perPage = ref(props.sessions.per_page);

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleString([], { 
    month: 'short', 
    day: 'numeric', 
    hour: '2-digit', 
    minute: '2-digit' 
  });
};

const updatePerPage = () => {
  router.get('/transactions', { 
    per_page: perPage.value,
    search: searchQuery.value 
  }, { preserveState: true });
};

const navigate = (url) => {
  if (url) {
    const searchParams = new URLSearchParams(new URL(url, window.location.origin).search);
    searchParams.set('per_page', perPage.value);
    if (searchQuery.value) searchParams.set('search', searchQuery.value);
    
    router.get(`${url.split('?')[0]}?${searchParams.toString()}`, {}, { preserveState: true });
  }
};

// Debounced search
let searchTimeout;
watch(searchQuery, (val) => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get('/transactions', { 
      per_page: perPage.value,
      search: val 
    }, { preserveState: true, preserveScroll: true });
  }, 400);
});

const stopSession = (id) => {
  if (confirm('Are you sure you want to stop this charging session?')) {
    router.post(`/transactions/${id}/stop`);
  }
};

const downloadInvoice = (session) => {
  const url = `/transactions/${session.id}/invoice`;
  window.open(url, '_blank');
};
</script>

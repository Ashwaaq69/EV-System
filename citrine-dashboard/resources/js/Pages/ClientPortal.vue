<template>
  <AppLayout>
    <div class="p-8 max-w-7xl mx-auto space-y-8">
      <!-- Header -->
      <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Client Portal</h1>
          <p class="text-zinc-500 dark:text-zinc-400">Welcome back, {{ $page.props.auth.user.name }}</p>
        </div>
        <div class="flex items-center gap-3">
          <Button variant="outline" size="sm" class="gap-2">
            View History
          </Button>
          <Button size="sm" class="bg-[#FF2D20] hover:bg-[#E0261B] text-white border-none">
            Top Up Balance
          </Button>
        </div>
      </header>

      <!-- Client Specific Content -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <Card class="border-none shadow-sm dark:bg-zinc-900 overflow-hidden group">
          <CardHeader class="flex flex-row items-center justify-between pb-2">
            <CardTitle class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Available Credits</CardTitle>
            <DollarSign class="h-4 w-4 text-[#FF2D20]" />
          </CardHeader>
          <CardContent>
            <div class="text-3xl font-bold text-zinc-900 dark:text-white">RM 150.00</div>
            <p class="text-xs text-zinc-400 mt-1">Valid for all network stations</p>
          </CardContent>
          <div class="absolute bottom-0 left-0 h-1 bg-[#FF2D20] w-full transform translate-y-1 group-hover:translate-y-0 transition-transform"></div>
        </Card>

        <Card class="border-none shadow-sm dark:bg-zinc-900 overflow-hidden group">
          <CardHeader class="flex flex-row items-center justify-between pb-2">
            <CardTitle class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Active Session</CardTitle>
            <Zap class="h-4 w-4 text-green-500" />
          </CardHeader>
          <CardContent>
            <div v-if="activeSession" class="space-y-4">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-zinc-500">Energy Delivered</span>
                    <span class="font-bold">12.4 kWh</span>
                </div>
                <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 animate-pulse" style="width: 65%"></div>
                </div>
                <Button variant="outline" size="sm" class="w-full border-red-200 text-red-600 hover:bg-red-50 dark:border-red-900/50 dark:hover:bg-red-950/20">
                    Stop Charging
                </Button>
            </div>
            <div v-else class="text-center py-4 text-zinc-400">
                <p class="text-sm">No active session</p>
            </div>
          </CardContent>
          <div class="absolute bottom-0 left-0 h-1 bg-green-500 w-full transform translate-y-1 group-hover:translate-y-0 transition-transform"></div>
        </Card>
      </div>

      <!-- Recent Usage -->
      <Card class="border-none shadow-sm dark:bg-zinc-900">
        <CardHeader class="flex flex-row items-center justify-between">
          <div>
            <CardTitle>Recent Activity</CardTitle>
            <CardDescription>Your latest charging sessions</CardDescription>
          </div>
          <div class="flex items-center gap-2" v-if="sessions.data?.length > 0">
            <Button 
                variant="outline" 
                size="sm" 
                class="h-8 w-8 p-0" 
                @click="fetchSessions(sessions.prev_page_url)"
                :disabled="!sessions.prev_page_url"
            >
                <ChevronLeft class="h-4 w-4" />
            </Button>
            <Button 
                variant="outline" 
                size="sm" 
                class="h-8 w-8 p-0" 
                @click="fetchSessions(sessions.next_page_url)"
                :disabled="!sessions.next_page_url"
            >
                <ChevronRight class="h-4 w-4" />
            </Button>
          </div>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Date</TableHead>
                <TableHead>Location</TableHead>
                <TableHead>Energy</TableHead>
                <TableHead class="text-right">Cost</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="session in sessions.data" :key="session.id" class="group transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                <TableCell class="text-xs text-zinc-500 italic">
                    {{ new Date(session.start_time).toLocaleDateString() }}
                </TableCell>
                <TableCell class="font-medium">
                    {{ session.charge_point?.location?.name || session.charge_point?.identifier || 'Unknown Station' }}
                </TableCell>
                <TableCell>{{ session.kwh_consumed || 0 }} kWh</TableCell>
                <TableCell class="text-right font-bold text-zinc-900 dark:text-zinc-100">
                    RM {{ parseFloat(session.total_cost || 0).toFixed(2) }}
                </TableCell>
              </TableRow>
              <TableRow v-if="(!sessions.data || sessions.data.length === 0) && !loading">
                <TableCell colspan="4" class="h-24 text-center text-zinc-500 italic">
                  No charging activity found.
                </TableCell>
              </TableRow>
              <TableRow v-if="loading">
                <TableCell colspan="4" class="h-24 text-center">
                    <RefreshCw class="h-5 w-5 animate-spin mx-auto text-[#FF2D20]" />
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
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
import { Zap, DollarSign, ChevronLeft, ChevronRight, RefreshCw } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const activeSession = ref(true);
const loading = ref(false);
const sessions = ref({
    data: [],
    prev_page_url: null,
    next_page_url: null,
});

const fetchSessions = async (url = '/api/client/sessions') => {
    loading.value = true;
    try {
        const response = await axios.get(url);
        // Handle both paginated object and simple array responses
        if (Array.isArray(response.data)) {
            sessions.value = {
                data: response.data,
                prev_page_url: null,
                next_page_url: null,
            };
        } else {
            sessions.value = response.data || { data: [], prev_page_url: null, next_page_url: null };
        }
    } catch (error) {
        console.error('Failed to fetch sessions:', error);
        // Mock fallback for standalone or dev errors
        sessions.value = {
            data: [
                { id: 1, start_time: '2026-02-15T08:00:00Z', charge_point: { identifier: 'Main Station A' }, kwh_consumed: 22.4, total_cost: 8.50 },
                { id: 2, start_time: '2026-02-14T10:30:00Z', charge_point: { identifier: 'East Plaza' }, kwh_consumed: 15.2, total_cost: 5.70 },
                { id: 3, start_time: '2026-02-13T18:45:00Z', charge_point: { identifier: 'North Hub B' }, kwh_consumed: 12.8, total_cost: 4.80 },
            ],
            prev_page_url: null,
            next_page_url: null,
        };
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchSessions();
});
</script>

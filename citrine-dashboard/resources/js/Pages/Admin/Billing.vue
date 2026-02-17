<template>
  <AppLayout>
    <div class="p-8 max-w-7xl mx-auto space-y-8">
      <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Financial Settlement</h1>
          <p class="text-zinc-500 dark:text-zinc-400">Manage revenue, tax collections, and daily reports</p>
        </div>
        <div class="flex items-center gap-3">
          <Button variant="outline" size="sm" class="gap-2" @click="generateReport">
            <RefreshCw class="h-4 w-4" />
            Generate Today's Report
          </Button>
          <Button size="sm" class="bg-[#FF2D20] hover:bg-[#E0261B] text-white border-none gap-2">
            <Download class="h-4 w-4" />
            Export Ledger
          </Button>
        </div>
      </header>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card class="border-none shadow-sm dark:bg-zinc-900 relative overflow-hidden group">
          <CardHeader class="flex flex-row items-center justify-between pb-2">
            <CardTitle class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Gross Network Revenue</CardTitle>
            <DollarSign class="h-4 w-4 text-[#FF2D20]" />
          </CardHeader>
          <CardContent>
            <div class="text-3xl font-bold">RM {{ Number(stats.total_revenue).toLocaleString() }}</div>
            <p class="text-xs text-green-600 mt-1">↑ 14% from last month</p>
          </CardContent>
          <div class="absolute bottom-0 left-0 h-1 bg-[#FF2D20] w-full transform translate-y-1 group-hover:translate-y-0 transition-transform"></div>
        </Card>

        <Card class="border-none shadow-sm dark:bg-zinc-900 relative overflow-hidden group">
          <CardHeader class="flex flex-row items-center justify-between pb-2">
            <CardTitle class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Total SST (8%) Collected</CardTitle>
            <ShieldCheck class="h-4 w-4 text-blue-500" />
          </CardHeader>
          <CardContent>
            <div class="text-3xl font-bold">RM {{ Number(stats.total_tax).toLocaleString() }}</div>
            <p class="text-xs text-zinc-400 mt-1">Payable to Royal Malaysian Customs</p>
          </CardContent>
          <div class="absolute bottom-0 left-0 h-1 bg-blue-500 w-full transform translate-y-1 group-hover:translate-y-0 transition-transform"></div>
        </Card>

        <Card class="border-none shadow-sm dark:bg-zinc-900 relative overflow-hidden group">
          <CardHeader class="flex flex-row items-center justify-between pb-2">
            <CardTitle class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Premium Subscribers</CardTitle>
            <UsersIcon class="h-4 w-4 text-purple-500" />
          </CardHeader>
          <CardContent>
            <div class="text-3xl font-bold">{{ stats.active_subscribers }} Active</div>
            <p class="text-xs text-purple-600 mt-1">Gold, Silver, and Bronze plans</p>
          </CardContent>
          <div class="absolute bottom-0 left-0 h-1 bg-purple-500 w-full transform translate-y-1 group-hover:translate-y-0 transition-transform"></div>
        </Card>
      </div>

      <!-- Settlement Reports Table -->
      <Card class="border-none shadow-sm dark:bg-zinc-900">
        <CardHeader class="flex flex-row items-center justify-between">
          <div>
            <CardTitle>Daily Settlement History</CardTitle>
            <CardDescription>Consolidated reports for daily financial reconciliation</CardDescription>
          </div>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader class="bg-zinc-50 dark:bg-zinc-800">
              <TableRow>
                <TableHead>Report Date</TableHead>
                <TableHead>Sessions</TableHead>
                <TableHead>Revenue</TableHead>
                <TableHead>Tax (SST)</TableHead>
                <TableHead>Discounts</TableHead>
                <TableHead>Net Settlement</TableHead>
                <TableHead class="text-right">Status</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="report in reports.data" :key="report.id" class="group">
                <TableCell class="font-bold">
                   {{ new Date(report.report_date).toLocaleDateString('en-MY', { day: '2-digit', month: 'short', year: 'numeric' }) }}
                </TableCell>
                <TableCell>{{ report.total_sessions }} units</TableCell>
                <TableCell>RM {{ Number(report.total_revenue).toFixed(2) }}</TableCell>
                <TableCell>RM {{ Number(report.total_tax).toFixed(2) }}</TableCell>
                <TableCell class="text-red-500">-RM {{ Number(report.total_discounts).toFixed(2) }}</TableCell>
                <TableCell class="font-bold text-green-600">RM {{ Number(report.net_settlement).toFixed(2) }}</TableCell>
                <TableCell class="text-right">
                  <Badge variant="success" class="bg-green-100 text-green-700 hover:bg-green-100 uppercase text-[10px]">{{ report.status }}</Badge>
                </TableCell>
              </TableRow>
              <TableRow v-if="reports.data.length === 0">
                <TableCell colspan="7" class="text-center py-12 text-zinc-400 italic">No settlement records found</TableCell>
              </TableRow>
            </TableBody>
          </Table>

          <!-- Pagination -->
          <div class="flex items-center justify-between px-2 py-4 border-t border-zinc-100 dark:border-zinc-800 mt-4" v-if="reports.last_page > 1">
            <div class="text-xs text-zinc-500">
              Page {{ reports.current_page }} of {{ reports.last_page }}
            </div>
            <div class="flex gap-2">
              <Link :href="reports.prev_page_url" v-if="reports.prev_page_url">
                <Button variant="outline" size="sm">Previous</Button>
              </Link>
              <Link :href="reports.next_page_url" v-if="reports.next_page_url">
                <Button variant="outline" size="sm">Next</Button>
              </Link>
            </div>
          </div>
        </CardContent>
      </Card>
      
      <!-- Tax Compliance Notice -->
      <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800 rounded-2xl p-6 flex gap-4">
        <div class="p-3 bg-white dark:bg-zinc-900 rounded-xl shadow-sm text-blue-600">
          <FileText class="h-6 w-6" />
        </div>
        <div>
          <h3 class="font-bold text-blue-900 dark:text-blue-100">Tax Auditing & Compliance</h3>
          <p class="text-sm text-blue-800/70 dark:text-blue-200/70 mt-1 max-w-2xl">
            All charging services in Malaysia are subject to 8% Service Tax (SST). CitrineOS automatically handles tax calculation at the point of session finalization. Settlement reports are generated daily at 00:00 UTC and represent a final, unalterable financial ledger.
          </p>
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
import { 
  DollarSign, 
  ShieldCheck, 
  Users as UsersIcon, 
  Download, 
  RefreshCw,
  FileText
} from 'lucide-vue-next';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
  reports: Object,
  stats: Object
});

const generateReport = () => {
  router.post(route('admin.billing.generate'), {}, {
    onSuccess: () => {
      // Notification is handled by the backend or a global toast if implemented
    }
  });
};
</script>

<template>
  <AppLayout>
    <div class="p-8 max-w-7xl mx-auto space-y-8">
      <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Network Analytics</h1>
          <p class="text-zinc-500 dark:text-zinc-400">Deep insights into performance and sustainability</p>
        </div>
        <div class="flex items-center gap-3">
          <Button variant="outline" size="sm" class="gap-2" @click="exportData">
            <Download class="h-4 w-4" />
            Export Data
          </Button>
          <Button size="sm" class="bg-[#FF2D20] hover:bg-[#E0261B] text-white border-none" @click="generateReport">
            Generate Report
          </Button>
        </div>
      </header>

      <Tabs default-value="overview" class="space-y-6">
        <TabsList class="bg-zinc-100 dark:bg-zinc-900 p-1">
          <TabsTrigger value="overview">Overview</TabsTrigger>
          <TabsTrigger value="revenue">Revenue</TabsTrigger>
          <TabsTrigger value="utilization">Utilization</TabsTrigger>
          <TabsTrigger value="sustainability">Sustainability</TabsTrigger>
        </TabsList>

         <TabsContent value="overview" class="space-y-6">
           <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
             <Card v-for="metric in metrics" :key="metric.label" class="border-none shadow-sm dark:bg-zinc-900 overflow-hidden relative">
               <CardHeader class="flex flex-row items-center justify-between pb-2">
                 <CardTitle class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">{{ metric.label }}</CardTitle>
                 <component :is="metric.icon" class="h-4 w-4 text-[#FF2D20]" />
               </CardHeader>
               <CardContent>
                 <div class="text-2xl font-bold">{{ metric.value }}</div>
                 <div class="mt-1 flex items-center gap-1">
                   <span :class="['text-[10px] font-bold', metric.trendUp ? 'text-green-600' : 'text-red-500']">
                     {{ metric.trendUp ? '↑' : '↓' }} {{ metric.change }}
                   </span>
                   <span class="text-[10px] text-zinc-400">vs last period</span>
                 </div>
               </CardContent>
               <div class="absolute bottom-0 left-0 h-0.5 w-full bg-zinc-100 dark:bg-zinc-800">
                 <div class="h-full bg-[#FF2D20]" :style="{ width: '30%' }"></div>
               </div>
             </Card>
           </div>

           <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
             <Card class="border-none shadow-sm dark:bg-zinc-900">
               <CardHeader>
                 <CardTitle class="text-base text-zinc-900 dark:text-white">Revenue Distribution</CardTitle>
                 <CardDescription>Daily revenue across all stations</CardDescription>
               </CardHeader>
               <CardContent class="h-[300px]">
                 <Bar :data="revenueData" :options="chartOptions" />
               </CardContent>
             </Card>

             <Card class="border-none shadow-sm dark:bg-zinc-900">
               <CardHeader>
                 <CardTitle class="text-base text-zinc-900 dark:text-white">Network Utilization</CardTitle>
                 <CardDescription>Hourly occupancy rate (%)</CardDescription>
               </CardHeader>
               <CardContent class="h-[300px]">
                 <Line :data="utilizationData" :options="chartOptions" />
               </CardContent>
             </Card>
           </div>
         </TabsContent>

         <TabsContent value="revenue" class="space-y-6">
           <Card class="border-none shadow-sm dark:bg-zinc-900">
              <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-7">
                <div>
                  <CardTitle class="text-base text-zinc-900 dark:text-white">Profitability by Location</CardTitle>
                  <CardDescription>Financial breakdown of your charging network</CardDescription>
                </div>
                <div class="relative w-64">
                  <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-zinc-500" />
                  <Input
                    v-model="searchQuery"
                    placeholder="Filter locations..."
                    class="pl-9 h-9 bg-zinc-50 dark:bg-zinc-800 border-none focus-visible:ring-1 focus-visible:ring-[#FF2D20]"
                  />
                </div>
              </CardHeader>
              <CardContent>
                <div class="overflow-x-auto">
                  <table class="w-full text-sm">
                    <thead>
                      <tr class="text-left border-b border-zinc-100 dark:border-zinc-800">
                        <th class="pb-3 font-bold uppercase text-[10px] text-zinc-400">Location</th>
                        <th class="pb-3 font-bold uppercase text-[10px] text-zinc-400">Total Revenue</th>
                        <th class="pb-3 font-bold uppercase text-[10px] text-zinc-400">Sessions</th>
                        <th class="pb-3 font-bold uppercase text-[10px] text-zinc-400">Net Margin</th>
                        <th class="pb-3 font-bold uppercase text-[10px] text-zinc-400 text-right">Trend</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                      <tr v-for="loc in paginatedLocations" :key="loc.name" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/10 transition-colors group">
                        <td class="py-4 font-bold group-hover:text-[#FF2D20] transition-colors">{{ loc.name }}</td>
                        <td class="py-4">RM {{ loc.revenue.toLocaleString() }}</td>
                        <td class="py-4 text-zinc-500">{{ loc.sessions }}</td>
                        <td class="py-4">
                          <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-full font-bold text-[10px]">{{ loc.margin }}%</span>
                        </td>
                        <td class="py-4 text-right text-emerald-500 font-bold">↑ 4.2%</td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div v-if="filteredLocations.length > 0" class="flex items-center justify-between mt-6">
                  <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">
                    Showing {{ rangeStart }}-{{ rangeEnd }} of {{ filteredLocations.length }}
                  </p>
                  <div class="flex items-center gap-1">
                    <Button 
                      variant="outline" 
                      size="icon" 
                      class="h-8 w-8 rounded-lg border-zinc-200 dark:border-zinc-800" 
                      :disabled="currentPage === 1"
                      @click="currentPage--"
                    >
                      <ChevronLeft class="h-4 w-4" />
                    </Button>
                    
                    <Button 
                      v-for="page in totalPages" 
                      :key="page"
                      variant="outline" 
                      size="sm" 
                      :class="['h-8 min-w-[32px] rounded-lg border-zinc-200 dark:border-zinc-800 font-bold text-[10px]', 
                              currentPage === page ? 'bg-[#FF2D20] text-white border-none' : 'hover:bg-zinc-50 dark:hover:bg-zinc-800']"
                      @click="currentPage = page"
                    >
                      {{ page }}
                    </Button>

                    <Button 
                      variant="outline" 
                      size="icon" 
                      class="h-8 w-8 rounded-lg border-zinc-200 dark:border-zinc-800" 
                      :disabled="currentPage === totalPages"
                      @click="currentPage++"
                    >
                      <ChevronRight class="h-4 w-4" />
                    </Button>
                  </div>
                </div>
              </CardContent>
           </Card>
         </TabsContent>

         <TabsContent value="utilization" class="space-y-6">
           <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
             <Card class="lg:col-span-2 border-none shadow-sm dark:bg-zinc-900">
                <CardHeader>
                  <CardTitle>Usage Peak Hours</CardTitle>
                  <CardDescription>Identifying high-demand periods</CardDescription>
                </CardHeader>
                <CardContent class="h-[400px]">
                  <Line :data="utilizationData" :options="chartOptions" />
                </CardContent>
             </Card>
             <Card class="border-none shadow-sm dark:bg-zinc-900 overflow-hidden relative group">
                <CardHeader>
                  <CardTitle class="text-[10px] font-bold uppercase tracking-widest text-[#FF2D20]">Efficiency Score</CardTitle>
                </CardHeader>
                <CardContent class="text-center py-10">
                  <div class="text-6xl font-black italic tracking-tighter text-[#FF2D20]">A+</div>
                  <p class="text-xs text-zinc-500 mt-4 leading-relaxed px-4">Your network is operating at peak efficiency. Recommendation: Expand Station B capacity by 20%.</p>
                  <Button variant="outline" size="sm" class="mt-8">View Recommendations</Button>
                </CardContent>
                <div class="absolute -right-8 -bottom-8 h-24 w-24 bg-[#FF2D20]/10 rounded-full blur-2xl group-hover:scale-150 transition-transform"></div>
             </Card>
           </div>
         </TabsContent>

         <TabsContent value="sustainability" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <Card class="border-none shadow-sm bg-gradient-to-br from-zinc-900 to-zinc-950 text-white relative overflow-hidden">
                <CardHeader>
                  <CardTitle class="text-emerald-400">Carbon Impact Dashboard</CardTitle>
                  <CardDescription class="text-zinc-500">Live environmental offsets</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                   <div>
                     <p class="text-[10px] uppercase font-bold text-zinc-500 tracking-widest">Total CO₂ Offsets</p>
                     <p class="text-4xl font-black italic">{{ analytics.carbonSavings.carbon_saved_ton }} TONS</p>
                   </div>
                   <div class="space-y-2">
                     <div class="flex justify-between text-[10px] font-bold uppercase">
                       <span>Green Energy vs Grid</span>
                       <span class="text-emerald-400">85% Clean</span>
                     </div>
                     <div class="h-2 w-full bg-zinc-800 rounded-full overflow-hidden">
                       <div class="h-full bg-emerald-500" :style="{ width: '85%' }"></div>
                     </div>
                   </div>
                   <div class="pt-4 flex items-center gap-4">
                     <div class="h-12 w-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center">
                       <Leaf class="h-6 w-6 text-emerald-400" />
                     </div>
                     <div>
                       <p class="text-xs font-bold">{{ analytics.carbonSavings.trees_equivalent }} Trees Planted</p>
                       <p class="text-[10px] text-zinc-500">Equivalent environmental cleanup</p>
                     </div>
                   </div>
                </CardContent>
                <div class="absolute top-0 right-0 p-8 opacity-10">
                   <Leaf class="h-32 w-32" />
                </div>
              </Card>

              <Card class="border-none shadow-sm dark:bg-zinc-900">
                <CardHeader>
                  <CardTitle>Energy Mix Analytics</CardTitle>
                </CardHeader>
                <CardContent class="h-[300px]">
                  <Doughnut :data="carbonData" :options="doughnutOptions" />
                </CardContent>
              </Card>
            </div>
         </TabsContent>
      </Tabs>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/Components/ui/tabs';
import { 
  Download, 
  TrendingUp, 
  Zap, 
  Leaf, 
  DollarSign, 
  Activity,
  BarChart3,
  Search,
  ChevronLeft,
  ChevronRight
} from 'lucide-vue-next';
import { Bar, Line, Doughnut } from 'vue-chartjs';
import { 
  Chart as ChartJS, 
  Title, 
  Tooltip, 
  Legend, 
  BarElement, 
  CategoryScale, 
  LinearScale, 
  LineElement, 
  PointElement, 
  ArcElement 
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, LineElement, PointElement, ArcElement);

const props = defineProps({
  analytics: Object
});

const metrics = computed(() => [
  { label: 'Total Revenue', value: 'RM ' + Number(props.analytics.total_revenue || 0).toLocaleString(), change: '12%', trendUp: true, icon: DollarSign },
  { label: 'Avg Utilization', value: (props.analytics.chargerUtilisation?.data?.[0] || 0) + '%', change: '5%', trendUp: true, icon: Activity },
  { label: 'Energy Delivered', value: (props.analytics.carbonSavings?.total_kwh || 0) + ' kWh', change: '8%', trendUp: true, icon: Zap },
  { label: 'Carbon Savings', value: (props.analytics.carbonSavings?.carbon_saved_ton || 0) + ' Tons', change: '2%', trendUp: true, icon: Leaf },
]);

// Filtering and Pagination
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 5;

const filteredLocations = computed(() => {
  if (!props.analytics.profitabilityByLocation) return [];
  return props.analytics.profitabilityByLocation.filter(loc => 
    loc.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const totalPages = computed(() => Math.ceil(filteredLocations.value.length / itemsPerPage));

const paginatedLocations = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredLocations.value.slice(start, start + itemsPerPage);
});

const rangeStart = computed(() => filteredLocations.value.length === 0 ? 0 : (currentPage.value - 1) * itemsPerPage + 1);
const rangeEnd = computed(() => Math.min(currentPage.value * itemsPerPage, filteredLocations.value.length));

const exportData = () => {
  const csvContent = "data:text/csv;charset=utf-8," 
    + "Location,Revenue,Sessions,Margin\n"
    + props.analytics.profitabilityByLocation.map(l => `${l.name},${l.revenue},${l.sessions},${l.margin}%`).join("\n");
  
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute("download", "network_analytics.csv");
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const generateReport = () => {
  window.print();
};

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
  },
  scales: {
    y: { 
      beginAtZero: true,
      grid: { color: 'rgba(0,0,0,0.05)' },
      ticks: { font: { size: 10 } }
    },
    x: { 
      grid: { display: false },
      ticks: { font: { size: 10 } }
    }
  }
};

const doughnutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  cutout: '75%',
};

const revenueData = computed(() => ({
  labels: props.analytics.revenuePerStation.labels,
  datasets: [{
    label: 'Revenue',
    backgroundColor: '#FF2D20',
    borderRadius: 4,
    data: props.analytics.revenuePerStation.data
  }]
}));

const utilizationData = computed(() => ({
  labels: props.analytics.peakUsage.labels,
  datasets: [{
    label: 'Utilization',
    borderColor: '#6366f1',
    backgroundColor: 'rgba(99, 102, 241, 0.1)',
    fill: true,
    tension: 0.4,
    data: props.analytics.peakUsage.data
  }]
}));

const energyData = computed(() => ({
  labels: props.analytics.energyConsumption.labels,
  datasets: [{
    label: 'Energy (kWh)',
    borderColor: '#FF2D20',
    backgroundColor: 'rgba(255, 45, 32, 0.1)',
    fill: true,
    tension: 0.4,
    data: props.analytics.energyConsumption.data
  }]
}));

const carbonData = {
  labels: ['Renewable', 'Grid Mix'],
  datasets: [{
    data: [85, 15],
    backgroundColor: ['#22c55e', '#e4e4e7'],
    borderWidth: 0,
  }]
};
</script>

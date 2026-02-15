<template>
  <AppLayout>
    <div class="p-8 max-w-7xl mx-auto space-y-8">
      <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Network Analytics</h1>
          <p class="text-zinc-500 dark:text-zinc-400">Deep insights into performance and sustainability</p>
        </div>
        <div class="flex items-center gap-3">
          <Button variant="outline" size="sm" class="gap-2">
            <Download class="h-4 w-4" />
            Export Data
          </Button>
          <Button size="sm" class="bg-[#FF2D20] hover:bg-[#E0261B] text-white border-none">
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
            <Card v-for="metric in metrics" :key="metric.label" class="border-none shadow-sm dark:bg-zinc-900">
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
            </Card>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <Card class="border-none shadow-sm dark:bg-zinc-900">
              <CardHeader>
                <CardTitle class="text-base">Revenue Distribution</CardTitle>
                <CardDescription>Daily revenue across all stations</CardDescription>
              </CardHeader>
              <CardContent class="h-[300px]">
                <Bar :data="revenueData" :options="chartOptions" />
              </CardContent>
            </Card>

            <Card class="border-none shadow-sm dark:bg-zinc-900">
              <CardHeader>
                <CardTitle class="text-base">Network Utilization</CardTitle>
                <CardDescription>Hourly occupancy rate (%)</CardDescription>
              </CardHeader>
              <CardContent class="h-[300px]">
                <Line :data="utilizationData" :options="chartOptions" />
              </CardContent>
            </Card>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <Card class="lg:col-span-2 border-none shadow-sm dark:bg-zinc-900">
              <CardHeader>
                <CardTitle class="text-base">Energy Consumption</CardTitle>
                <CardDescription>Daily kWh delivery log</CardDescription>
              </CardHeader>
              <CardContent class="h-[300px]">
                <Line :data="energyData" :options="chartOptions" />
              </CardContent>
            </Card>

            <Card class="border-none shadow-sm dark:bg-zinc-900">
              <CardHeader>
                <CardTitle class="text-base text-[#FF2D20]">Sustainability Focus</CardTitle>
                <CardDescription>Carbon savings & offsets</CardDescription>
              </CardHeader>
              <CardContent class="flex flex-col justify-center items-center h-full pb-8">
                <div class="relative h-44 w-44">
                  <Doughnut :data="carbonData" :options="doughnutOptions" />
                  <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-bold">85%</span>
                    <span class="text-[8px] uppercase font-bold text-zinc-500">Green Mix</span>
                  </div>
                </div>
                <div class="mt-4 text-center">
                  <p class="text-sm font-bold">128 Tons Saved</p>
                  <p class="text-[10px] text-zinc-500">Equivalent to 5.2k trees</p>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>
      </Tabs>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/Components/ui/tabs';
import { 
  Download, 
  TrendingUp, 
  Zap, 
  Leaf, 
  DollarSign, 
  Activity,
  BarChart3
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

const metrics = [
  { label: 'Total Revenue', value: 'RM 45,200', change: '12%', trendUp: true, icon: DollarSign },
  { label: 'Avg Utilization', value: '68%', change: '5%', trendUp: true, icon: Activity },
  { label: 'Energy Delivered', value: '128 MWh', change: '8%', trendUp: true, icon: Zap },
  { label: 'Carbon Savings', value: '12.4t', change: '2%', trendUp: false, icon: Leaf },
];

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

const revenueData = {
  labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
  datasets: [{
    label: 'Revenue',
    backgroundColor: '#FF2D20',
    borderRadius: 4,
    data: [450, 590, 800, 810, 560, 550, 400]
  }]
};

const utilizationData = {
  labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
  datasets: [{
    label: 'Utilization',
    borderColor: '#6366f1',
    backgroundColor: 'rgba(99, 102, 241, 0.1)',
    fill: true,
    tension: 0.4,
    data: [20, 15, 65, 80, 75, 50]
  }]
};

const energyData = {
  labels: ['1 Jan', '2 Jan', '3 Jan', '4 Jan', '5 Jan', '6 Jan', '7 Jan'],
  datasets: [{
    label: 'Energy (kWh)',
    borderColor: '#FF2D20',
    backgroundColor: 'rgba(255, 45, 32, 0.1)',
    fill: true,
    tension: 0.4,
    data: [1200, 1900, 1700, 2100, 2400, 1800, 2600]
  }]
};

const carbonData = {
  labels: ['Renewable', 'Grid Mix'],
  datasets: [{
    data: [85, 15],
    backgroundColor: ['#22c55e', '#e4e4e7'],
    borderWidth: 0,
  }]
};
</script>

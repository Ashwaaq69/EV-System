<template>
  <AppLayout>
    <div class="p-8 max-w-7xl mx-auto space-y-8">
      <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Dashboard Overview</h1>
          <p class="text-zinc-500 dark:text-zinc-400">Real-time status of your charging network</p>
        </div>
        <div class="flex items-center gap-3">
          <Button variant="outline" size="sm" @click="refreshAll" class="gap-2">
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
      <section class="grid grid-cols-1 gap-6 md:grid-cols-4">
        <Card class="relative overflow-hidden group hover:shadow-md transition-shadow">
          <CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
            <CardTitle class="text-sm font-medium text-zinc-500">Online Units</CardTitle>
            <Zap class="h-4 w-4 text-green-500" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-green-600">{{ onlineChargers }}</div>
            <p class="text-[10px] text-zinc-400 mt-1">
              <span class="text-green-500 font-bold">{{ availableChargers }}</span> Available, 
              <span class="text-orange-500 font-bold">{{ chargingChargers }}</span> Charging
            </p>
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

        <Card class="relative overflow-hidden group hover:shadow-md transition-shadow border-orange-200 dark:border-orange-900">
          <CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
            <CardTitle class="text-sm font-medium text-zinc-500">Active Charging</CardTitle>
            <BatteryCharging class="h-4 w-4 text-orange-500" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-orange-600">{{ activeSessions.length }}</div>
            <p class="text-xs text-zinc-400 mt-1">In progress sessions</p>
          </CardContent>
          <div class="absolute bottom-0 left-0 h-1 bg-orange-500 w-full transform translate-y-1 group-hover:translate-y-0 transition-transform"></div>
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
                <span>Network Health</span>
                <span>{{ networkHealth }}%</span>
              </div>
              <div class="h-1.5 w-full bg-zinc-800 dark:bg-zinc-700 rounded-full overflow-hidden">
                <div :style="{ width: networkHealth + '%' }" class="h-full bg-gradient-to-r from-[#FF2D20] to-orange-500 transition-all duration-1000"></div>
              </div>
              <div class="flex justify-between text-[10px] uppercase font-bold text-zinc-400 mt-2">
                <span>Live Occupancy</span>
                <span>{{ occupancyRate }}%</span>
              </div>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Active Sessions Panel -->
      <div v-if="activeSessions.length > 0" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Active Sessions Panel -->
        <Card class="lg:col-span-2 border-orange-200 dark:border-orange-900 shadow-sm relative overflow-hidden">
          <CardHeader class="flex flex-row items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-orange-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
              </div>
              <div>
                <CardTitle class="text-base">Active Charging Sessions</CardTitle>
                <CardDescription>{{ activeSessions.length }} session{{ activeSessions.length !== 1 ? 's' : '' }} in progress</CardDescription>
              </div>
            </div>
            <Button variant="outline" size="sm" @click="refreshSessions" class="gap-2">
              <RefreshCw class="h-3 w-3" />
              Refresh
            </Button>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>User</TableHead>
                  <TableHead>Charger</TableHead>
                  <TableHead>Elapsed</TableHead>
                  <TableHead class="text-right">Energy</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="session in activeSessions" :key="session.id" class="group">
                  <TableCell>
                    <div>
                      <p class="font-medium text-sm">{{ session.user_name }}</p>
                      <p class="text-[10px] text-zinc-400">{{ session.user_email }}</p>
                    </div>
                  </TableCell>
                  <TableCell>
                    <div class="flex items-center gap-2">
                      <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                      </span>
                      <span class="font-medium text-xs">{{ session.charger }}</span>
                    </div>
                    <p class="text-[10px] text-zinc-500">{{ session.location }}</p>
                  </TableCell>
                  <TableCell>
                    <Badge variant="outline" class="font-mono text-xs border-orange-300 text-orange-600 dark:border-orange-800 dark:text-orange-400">
                      {{ getElapsedTime(session.start_time) }}
                    </Badge>
                  </TableCell>
                  <TableCell class="text-right">
                    <span class="text-sm font-semibold">{{ session.kwh.toFixed(2) }} kWh</span>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </CardContent>
        </Card>

        <!-- OCPP Live Stream -->
        <Card class="border-none shadow-sm bg-zinc-950 text-zinc-400 overflow-hidden relative border border-zinc-800">
          <CardHeader class="pb-2 border-b border-zinc-900 bg-zinc-900/50">
            <div class="flex justify-between items-center">
              <CardTitle class="text-[10px] uppercase font-bold tracking-widest text-orange-500">Network Telemetry (OCPP)</CardTitle>
              <span class="flex h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
            </div>
          </CardHeader>
          <CardContent class="h-[300px] font-mono text-[9px] p-2 overflow-hidden relative">
            <div class="space-y-1 text-zinc-500">
              <div v-for="(log, i) in liveLogs" :key="i" class="flex gap-2 border-b border-zinc-900/50 pb-1">
                <span class="text-zinc-700 shrink-0">{{ log.time }}</span>
                <span :class="log.color" class="shrink-0 font-bold uppercase">{{ log.type }}</span>
                <span class="text-zinc-400 truncate text-[8px]">{{ log.msg }}</span>
              </div>
            </div>
            <!-- Gradient Mask -->
            <div class="absolute inset-x-0 bottom-0 h-12 bg-gradient-to-t from-zinc-950 to-transparent"></div>
          </CardContent>
        </Card>
      </div>



      <!-- Main Layout (Chargers & Sidebar) -->
      <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
        <!-- Chargers List (Stations) -->
        <Card class="lg:col-span-3 border-none shadow-sm shadow-zinc-200 dark:shadow-none bg-white dark:bg-zinc-900">
          <CardHeader class="flex flex-row items-center justify-between pb-2">
            <div>
              <CardTitle>Charger Status</CardTitle>
              <CardDescription>Network-wide station health and availability</CardDescription>
            </div>
            <div class="flex items-center gap-4">
              <div class="relative w-48 md:w-64">
                <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-zinc-500" />
                <Input 
                  placeholder="Search stations..." 
                  v-model="searchQuery"
                  class="pl-9 h-9 border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/50"
                />
              </div>
              <div class="flex items-center gap-2">
                <span class="text-xs text-zinc-500 font-medium">View:</span>
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
                <TableRow class="hover:bg-transparent border-zinc-100 dark:border-zinc-800">
                  <TableHead class="w-[100px]">Identifier</TableHead>
                  <TableHead>Location</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead class="text-right">Action</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="charger in chargers.data" :key="charger.id" class="group border-zinc-100 dark:border-zinc-800">
                  <TableCell class="font-medium">
                    <div class="flex items-center gap-2">
                      <div :class="['w-2 h-2 rounded-full', charger.online ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]' : 'bg-zinc-300']"></div>
                      {{ charger.name }}
                    </div>
                  </TableCell>
                  <TableCell class="text-zinc-500">{{ charger.location }}</TableCell>
                  <TableCell>
                    <Badge 
                      :variant="charger.online ? 'default' : 'secondary'" 
                      class="cursor-pointer transition-all hover:scale-105"
                      :class="charger.online ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-zinc-100 text-zinc-500 hover:bg-zinc-200'"
                      @click="toggleStatus(charger.id)"
                      :title="`Click to ${charger.status === 'Unavailable' ? 'Activate' : 'Deactivate'}`"
                    >
                      {{ charger.online ? 'Active' : 'Inactive' }}
                    </Badge>
                  </TableCell>
                  <TableCell class="text-right">
                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                      <Button variant="ghost" size="icon" class="h-8 w-8 text-zinc-500 hover:text-zinc-900" @click="openEditModal(charger)">
                        <Edit class="h-4 w-4" />
                      </Button>
                      <Button variant="ghost" size="icon" class="h-8 w-8 text-red-500 hover:text-red-700 hover:bg-red-50" @click="deleteCharger(charger)">
                        <Trash2 class="h-4 w-4" />
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
                <TableRow v-if="chargers.data.length === 0">
                  <TableCell colspan="4" class="h-24 text-center text-zinc-500 italic">
                    No chargers found in network.
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
            
            <div class="mt-6 flex items-center justify-between border-t border-zinc-100 dark:border-zinc-800 pt-4">
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

        <!-- Sidebar Actions (Quick Stats) -->
        <div class="space-y-6">
          <Card>
            <CardHeader class="pb-2">
              <CardTitle class="text-base">System Health</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="flex items-start gap-3">
                <div class="mt-1 h-2 w-2 rounded-full bg-green-500"></div>
                <div>
                  <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Core Connectivity</p>
                  <p class="text-[10px] text-zinc-500 font-medium">99.9% uptime (24h)</p>
                </div>
              </div>
              <Separator class="bg-zinc-100 dark:bg-zinc-800" />
              <div class="flex items-start gap-3">
                <div class="mt-1 h-2 w-2 rounded-full bg-green-500"></div>
                <div>
                  <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Database Latency</p>
                  <p class="text-[10px] text-zinc-500 font-medium">12ms average</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card class="bg-gradient-to-br from-[#FF2D20] to-orange-600 text-white border-none shadow-lg shadow-red-200 dark:shadow-none">
            <CardHeader class="pb-2">
              <CardTitle class="text-white text-base">Sustainability Target</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="text-3xl font-bold">85%</div>
              <p class="text-[10px] text-zinc-100 opacity-80 mt-1">Renewable energy goal reached</p>
              <Button class="w-full mt-4 bg-white/20 hover:bg-white/30 text-white border-none transition-all text-xs font-bold uppercase tracking-wider">
                Full Strategy
              </Button>
            </CardContent>
          </Card>
        </div>
      </div>

      <!-- Analytics Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Revenue per Station -->
        <Card class="dark:bg-zinc-900 border-none shadow-sm">
          <CardHeader>
            <CardTitle class="text-xs font-bold uppercase tracking-widest text-zinc-500">Revenue per Station</CardTitle>
          </CardHeader>
          <CardContent class="h-[250px] flex items-center justify-center">
            <Bar v-if="analyticsFetched" :data="revenueChartData" :options="baseChartOptions" />
            <div v-else class="flex flex-col items-center gap-2 text-zinc-400">
              <RefreshCw class="h-6 w-6 animate-spin" />
              <span class="text-xs font-medium">Computing revenue...</span>
            </div>
          </CardContent>
        </Card>

        <!-- Charger Utilisation -->
        <Card class="dark:bg-zinc-900 border-none shadow-sm">
          <CardHeader>
            <CardTitle class="text-xs font-bold uppercase tracking-widest text-zinc-500">Charger Utilisation %</CardTitle>
          </CardHeader>
          <CardContent class="h-[250px] flex items-center justify-center">
            <Bar v-if="analyticsFetched" :data="utilizationChartData" :options="baseChartOptions" />
            <div v-else class="flex flex-col items-center gap-2 text-zinc-400">
              <RefreshCw class="h-6 w-6 animate-spin" />
              <span class="text-xs font-medium">Calculating load...</span>
            </div>
          </CardContent>
        </Card>

        <!-- Carbon Savings Card -->
        <Card class="relative overflow-hidden border-none shadow-lg bg-gradient-to-br from-green-500 to-emerald-700 text-white">
          <CardHeader>
            <CardTitle class="text-xs font-bold uppercase tracking-widest text-green-100">Carbon Saving Estimation</CardTitle>
          </CardHeader>
          <CardContent class="flex flex-col justify-between h-[180px]">
            <div v-if="analyticsFetched">
              <div class="flex items-baseline gap-2">
                <span class="text-4xl font-bold">{{ analyticsData.carbonSavings.carbon_saved_ton }}</span>
                <span class="text-xl font-medium opacity-80">tons CO₂</span>
              </div>
              <p class="text-xs text-green-100 mt-2 opacity-90 leading-relaxed">
                Equivalent to planting <span class="font-bold underlineDecoration">{{ analyticsData.carbonSavings.trees_equivalent }} trees</span> this year.
              </p>
              <div class="mt-6 flex items-center gap-4">
                <div class="text-center">
                  <p class="text-[10px] uppercase font-bold opacity-60">Total Energy</p>
                  <p class="text-sm font-bold">{{ analyticsData.carbonSavings.total_kwh }} kWh</p>
                </div>
                <Separator orientation="vertical" class="h-8 bg-green-400/30" />
                <Leaf class="h-8 w-8 text-green-200 opacity-40 ml-auto" />
              </div>
            </div>
            <div v-else class="h-full flex flex-col items-center justify-center gap-2">
              <RefreshCw class="h-6 w-6 animate-spin opacity-50" />
              <span class="text-xs">Measuring impact...</span>
            </div>
          </CardContent>
          <div class="absolute -right-4 -bottom-4 h-24 w-24 bg-white/10 rounded-full blur-2xl"></div>
        </Card>

        <!-- Energy Consumption Report -->
        <Card class="lg:col-span-2 dark:bg-zinc-900 border-none shadow-sm">
          <CardHeader>
            <CardTitle class="text-xs font-bold uppercase tracking-widest text-zinc-500">Energy Consumption Report</CardTitle>
          </CardHeader>
          <CardContent class="h-[250px] flex items-center justify-center">
            <Line v-if="analyticsFetched" :data="energyChartData" :options="baseChartOptions" />
            <div v-else class="flex flex-col items-center gap-2 text-zinc-400">
              <RefreshCw class="h-6 w-6 animate-spin" />
              <span class="text-xs font-medium">Fetching energy log...</span>
            </div>
          </CardContent>
        </Card>

        <!-- Peak Usage Analytics -->
        <Card class="dark:bg-zinc-900 border-none shadow-sm">
          <CardHeader>
            <CardTitle class="text-xs font-bold uppercase tracking-widest text-zinc-500">Peak Usage Analytics</CardTitle>
          </CardHeader>
          <CardContent class="h-[250px] flex items-center justify-center">
            <Line v-if="analyticsFetched" :data="peakChartData" :options="baseChartOptions" />
            <div v-else class="flex flex-col items-center gap-2 text-zinc-400">
              <RefreshCw class="h-6 w-6 animate-spin" />
              <span class="text-xs font-medium">Scanning peak hours...</span>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Heatmap & Profitability -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Charging Session Heatmap -->
        <Card class="dark:bg-zinc-900 border-none shadow-sm">
          <CardHeader>
            <CardTitle class="text-xs font-bold uppercase tracking-widest text-zinc-500">Charging Session Heatmap</CardTitle>
            <CardDescription class="text-[10px]">Frequency of sessions by Day vs Hour</CardDescription>
          </CardHeader>
          <CardContent>
            <div v-if="analyticsFetched" class="overflow-x-auto">
              <div class="min-w-[500px] flex flex-col gap-1">
                <div class="flex gap-1 ml-10">
                  <div v-for="h in [0,4,8,12,16,20]" :key="h" class="flex-1 text-[9px] text-zinc-500 text-center font-bold">
                    {{ String(h).padStart(2, '0') }}:00
                  </div>
                </div>
                <div v-for="(day, dIndex) in analyticsData.sessionHeatmap.days" :key="day" class="flex items-center gap-2">
                  <div class="w-8 text-[10px] font-bold text-zinc-400">{{ day }}</div>
                  <div class="flex-1 flex gap-0.5">
                    <div 
                      v-for="(count, hIndex) in analyticsData.sessionHeatmap.matrix[dIndex]" 
                      :key="hIndex"
                      class="flex-1 aspect-square rounded-[1px] transition-all hover:scale-125 hover:z-10"
                      :style="{ backgroundColor: getHeatmapColor(count) }"
                      :title="`${day} ${hIndex}:00 - ${count} sessions`"
                    ></div>
                  </div>
                </div>
              </div>
              <div class="mt-4 flex items-center justify-end gap-2 pr-2">
                <span class="text-[9px] font-bold text-zinc-500 uppercase">Less</span>
                <div class="flex gap-0.5">
                  <div v-for="i in 5" :key="i" class="h-2 w-2 rounded-[1px]" :style="{ backgroundColor: getHeatmapColor(i-1) }"></div>
                </div>
                <span class="text-[9px] font-bold text-zinc-500 uppercase">More</span>
              </div>
            </div>
            <div v-else class="h-[200px] flex flex-col items-center justify-center gap-2 text-zinc-400">
              <RefreshCw class="h-6 w-6 animate-spin" />
              <span class="text-xs font-medium">Generating heatmap...</span>
            </div>
          </CardContent>
        </Card>

        <!-- Profitability by Location -->
        <Card class="dark:bg-zinc-900 border-none shadow-sm">
          <CardHeader>
            <CardTitle class="text-xs font-bold uppercase tracking-widest text-zinc-500">Profitability by Location</CardTitle>
          </CardHeader>
          <CardContent>
            <Table v-if="analyticsFetched">
              <TableHeader>
                <TableRow class="hover:bg-transparent border-zinc-100 dark:border-zinc-800">
                  <TableHead class="text-[10px] font-bold uppercase text-zinc-400">Location</TableHead>
                  <TableHead class="text-[10px] font-bold uppercase text-zinc-400">Sessions</TableHead>
                  <TableHead class="text-[10px] font-bold uppercase text-zinc-400">Energy</TableHead>
                  <TableHead class="text-right text-[10px] font-bold uppercase text-zinc-400">Profit Margin</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="loc in analyticsData.profitabilityByLocation" :key="loc.name" class="group border-zinc-100 dark:border-zinc-800">
                  <TableCell class="font-bold text-sm">{{ loc.name }}</TableCell>
                  <TableCell class="text-zinc-500">{{ loc.sessions }}</TableCell>
                  <TableCell class="text-zinc-500">{{ loc.energy }} kWh</TableCell>
                  <TableCell class="text-right">
                    <div class="flex items-center justify-end gap-2">
                      <div class="w-12 h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                        <div :style="{ width: loc.margin + '%' }" class="h-full bg-emerald-500"></div>
                      </div>
                      <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ loc.margin }}%</span>
                    </div>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
            <div v-else class="h-[200px] flex flex-col items-center justify-center gap-2 text-zinc-400">
              <RefreshCw class="h-6 w-6 animate-spin" />
              <span class="text-xs font-medium">Auditing locations...</span>
            </div>
          </CardContent>
        </Card>
      </div>

      <Dialog v-model:open="isEditModalOpen">
        <DialogContent class="sm:max-w-[425px] dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800">
          <DialogHeader>
            <DialogTitle>Edit Charger</DialogTitle>
            <DialogDescription>
              Update information for this charge point.
            </DialogDescription>
          </DialogHeader>
          <form @submit.prevent="updateCharger" class="grid gap-6 py-4">
            <div class="grid gap-2">
              <Label for="edit_identifier" class="text-xs font-bold uppercase tracking-wider text-zinc-500">Charger Identifier</Label>
              <Input
                id="edit_identifier"
                v-model="editForm.identifier"
                class="dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 font-medium"
              />
              <p v-if="editForm.errors.identifier" class="text-xs text-red-500 font-medium italic">{{ editForm.errors.identifier }}</p>
            </div>
            <div class="grid gap-2">
              <Label for="edit_location" class="text-xs font-bold uppercase tracking-wider text-zinc-500">Location Name</Label>
              <Input
                id="edit_location"
                v-model="editForm.location_name"
                class="dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 font-medium"
              />
              <p v-if="editForm.errors.location_name" class="text-xs text-red-500 font-medium italic">{{ editForm.errors.location_name }}</p>
            </div>
            <DialogFooter class="mt-4">
              <Button type="submit" :disabled="editForm.processing" class="bg-[#FF2D20] hover:bg-[#E0261B] text-white border-none w-full shadow-lg shadow-red-200 dark:shadow-none">
                {{ editForm.processing ? 'Updating...' : 'Update Details' }}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
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
  Plus,
  BatteryCharging,
  Leaf,
  BarChart3,
  Search,
  Edit,
  Trash2
} from 'lucide-vue-next';
import { Bar, Line } from 'vue-chartjs';
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
  Filler
} from 'chart.js';
import axios from 'axios';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, LineElement, PointElement, Filler);
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps({
  chargers: {
    type: Object,
    required: true
  },
  availableChargers: {
    type: Number,
    default: 0,
  },
  chargingChargers: {
    type: Number,
    default: 0,
  },
  onlineChargers: {
    type: Number,
    default: 0,
  },
  offlineChargers: {
    type: Number,
    default: 0,
  },
  activeSessions: {
    type: Array,
    default: () => [],
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

const isEditModalOpen = ref(false);
const editChargerId = ref(null);
const editForm = useForm({
  identifier: '',
  location_name: '',
});

const openEditModal = (charger) => {
  editChargerId.value = charger.id;
  editForm.identifier = charger.identifier;
  editForm.location_name = charger.location;
  isEditModalOpen.value = true;
};

const updateCharger = () => {
  editForm.patch(route('chargers.update', editChargerId.value), {
    onSuccess: () => {
      isEditModalOpen.value = false;
      editForm.reset();
    },
  });
};

const deleteCharger = (charger) => {
  if (confirm(`Are you sure you want to delete charger ${charger.identifier}?`)) {
    router.delete(route('chargers.destroy', charger.id));
  }
};

const toggleStatus = (id) => {
  router.patch(route('chargers.toggle-status', id), {}, { preserveScroll: true });
};

const searchQuery = ref(new URLSearchParams(window.location.search).get('search') || '');
const perPageInternal = ref(props.chargers.per_page || 10);

const totalChargers = computed(() => props.onlineChargers + props.offlineChargers);
const networkHealth = computed(() => {
  const total = totalChargers.value || 1;
  return Math.round((props.onlineChargers / total) * 100);
});
const occupancyRate = computed(() => {
  const online = props.onlineChargers || 1;
  return Math.round((props.chargingChargers / online) * 100);
});

const updatePerPage = () => {
  router.get('/dashboard', { 
    per_page: perPageInternal.value,
    search: searchQuery.value 
  }, { preserveState: true });
};

const navigate = (url) => {
  if (url) {
    const searchParams = new URLSearchParams(new URL(url, window.location.origin).search);
    searchParams.set('per_page', perPageInternal.value);
    if (searchQuery.value) searchParams.set('search', searchQuery.value);
    
    router.get(`${url.split('?')[0]}?${searchParams.toString()}`, {}, { preserveState: true });
  }
};

// Debounced search
let searchTimeout;
watch(searchQuery, (val) => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get('/dashboard', { 
      per_page: perPageInternal.value,
      search: val 
    }, { preserveState: true, preserveScroll: true });
  }, 400);
});

// --- Analytics Data & Methods ---
const analyticsFetched = ref(false);
const analyticsData = ref({
  revenuePerStation: { labels: [], data: [] },
  chargerUtilisation: { labels: [], data: [] },
  sessionHeatmap: { days: [], hours: [], matrix: [] },
  peakUsage: { labels: [], data: [] },
  energyConsumption: { labels: [], data: [] },
  carbonSavings: { total_kwh: 0, carbon_saved_ton: 0, trees_equivalent: 0 },
  profitabilityByLocation: []
});

const fetchAnalytics = async () => {
  try {
    const response = await axios.get('/api/admin/analytics');
    analyticsData.value = response.data;
    analyticsFetched.value = true;
  } catch (err) {
    console.error('Failed to fetch analytics:', err);
  }
};

const refreshAll = () => {
  router.reload();
  fetchAnalytics();
};

const getHeatmapColor = (count) => {
  if (count === 0) return 'rgba(0,0,0,0.05)';
  if (count < 2) return '#fee2e2'; // Lightest red/orange
  if (count < 5) return '#fca5a5';
  if (count < 10) return '#f87171';
  if (count < 20) return '#ef4444';
  return '#b91c1c'; // Darkest
};

// --- Chart Configurations ---
const baseChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    y: { 
      beginAtZero: true, 
      grid: { color: 'rgba(0,0,0,0.03)' },
      ticks: { font: { size: 9 }, color: '#94a3b8' }
    },
    x: { 
      grid: { display: false },
      ticks: { font: { size: 9 }, color: '#94a3b8' }
    }
  }
};

const revenueChartData = computed(() => ({
  labels: analyticsData.value.revenuePerStation.labels,
  datasets: [{
    label: 'Revenue (RM)',
    backgroundColor: '#FF2D20',
    borderRadius: 4,
    data: analyticsData.value.revenuePerStation.data
  }]
}));

const utilizationChartData = computed(() => ({
  labels: analyticsData.value.chargerUtilisation.labels,
  datasets: [{
    label: 'Utilisation %',
    backgroundColor: '#6366f1',
    borderRadius: 4,
    data: analyticsData.value.chargerUtilisation.data
  }]
}));

const energyChartData = computed(() => ({
  labels: analyticsData.value.energyConsumption.labels,
  datasets: [{
    label: 'Energy (kWh)',
    borderColor: '#10b981',
    backgroundColor: 'rgba(16, 185, 129, 0.1)',
    fill: true,
    tension: 0.4,
    data: analyticsData.value.energyConsumption.data
  }]
}));

const peakChartData = computed(() => ({
  labels: analyticsData.value.peakUsage.labels,
  datasets: [{
    label: 'Sessions',
    borderColor: '#f59e0b',
    backgroundColor: 'rgba(245, 158, 11, 0.1)',
    fill: true,
    tension: 0.4,
    data: analyticsData.value.peakUsage.data
  }]
}));

// --- Existing Logic ---
const now = ref(Date.now());
let tickInterval = null;
let refreshInterval = null;

const getElapsedTime = (startTime) => {
  if (!startTime) return '00:00:00';
  const start = new Date(startTime).getTime();
  const diff = Math.max(0, Math.floor((now.value - start) / 1000));
  const h = String(Math.floor(diff / 3600)).padStart(2, '0');
  const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
  const s = String(diff % 60).padStart(2, '0');
  return `${h}:${m}:${s}`;
};

const refreshSessions = () => {
  router.reload({ only: ['activeSessions', 'availableChargers', 'chargingChargers', 'onlineChargers', 'offlineChargers'] });
};

const liveLogs = ref([
  { time: '17:50:01', type: 'HB', msg: 'CS-001 -> Connection Stable', color: 'text-blue-500' },
  { time: '17:50:05', type: 'STS', msg: 'CS-004 -> Unit Available', color: 'text-green-500' },
  { time: '17:51:12', type: 'MET', msg: 'CS-002 -> Metervalue: 2.45kWh', color: 'text-orange-500' },
]);

let logInterval = null;

onMounted(() => {
  fetchAnalytics();
  tickInterval = setInterval(() => { now.value = Date.now(); }, 1000);
  refreshInterval = setInterval(() => {
    refreshSessions();
    fetchAnalytics();
  }, 10000);
  
  logInterval = setInterval(() => {
    const types = ['HB', 'STS', 'MET', 'SEC'];
    const colors = ['text-blue-500', 'text-green-400', 'text-amber-500', 'text-red-500'];
    const idx = Math.floor(Math.random() * types.length);
    const nowTime = new Date();
    const timeStr = `${nowTime.getHours().toString().padStart(2, '0')}:${nowTime.getMinutes().toString().padStart(2, '0')}:${nowTime.getSeconds().toString().padStart(2, '0')}`;
    
    liveLogs.value.unshift({
      time: timeStr,
      type: types[idx],
      msg: `CS-00${Math.floor(Math.random() * 5) + 1} -> Automated Signal ACK`,
      color: colors[idx]
    });
    
    if (liveLogs.value.length > 25) {
      liveLogs.value.pop();
    }
  }, 2500);
});

onUnmounted(() => {
  clearInterval(tickInterval);
  clearInterval(refreshInterval);
  clearInterval(logInterval);
});
</script>


<style scoped>
.bg-gradient-header {
  background: linear-gradient(90deg, #ff7a60 0%, #ff2d20 50%, #6d28d9 100%);
}
</style>

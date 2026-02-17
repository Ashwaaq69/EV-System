<template>
  <AppLayout>
    <div class="p-8 max-w-7xl mx-auto space-y-8">
      <!-- Header -->
      <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          
          <p class="text-zinc-500 dark:text-zinc-400">Welcome, {{ authUser?.name || 'User' }} </p>
        </div>
        <div class="flex items-center gap-3">
          <Button variant="outline" size="sm" class="gap-2" @click="activeTab = 'notifications'">
            <Bell class="h-4 w-4" />
            <span class="hidden sm:inline">Notifications</span>
            <Badge v-if="notifications.length > 0"S variant="destructive" class="ml-1 px-1 min-w-[1.2rem] h-5">{{ notifications.length }}</Badge>
          </Button>
          <Button size="sm" class="bg-[#FF2D20] hover:bg-[#E0261B] text-white border-none gap-2" @click="activeTab = 'wallet'">
            <Plus class="h-4 w-4" />
            Top Up
          </Button>
        </div>
      </header>

      <Tabs v-model="activeTab" class="w-full">
        <TabsList class="grid grid-cols-4 md:grid-cols-8 mb-8 bg-zinc-100 dark:bg-zinc-800 p-1 rounded-xl">
          <TabsTrigger value="overview" class="gap-2">
            <LayoutDashboard class="h-4 w-4" />
            <span class="hidden md:inline">Overview</span>
          </TabsTrigger>
          <TabsTrigger value="map" class="gap-2">
            <MapPin class="h-4 w-4" />
            <span class="hidden md:inline">Map</span>
          </TabsTrigger>
          <TabsTrigger value="vehicles" class="gap-2">
            <Car class="h-4 w-4" />
            <span class="hidden md:inline">Vehicles</span>
          </TabsTrigger>
          <TabsTrigger value="history" class="gap-2">
            <History class="h-4 w-4" />
            <span class="hidden md:inline">History</span>
          </TabsTrigger>
          <TabsTrigger value="wallet" class="gap-2">
            <WalletIcon class="h-4 w-4" />
            <span class="hidden md:inline">Billing</span>
          </TabsTrigger>
          <TabsTrigger value="notifications" class="gap-2">
            <Bell class="h-4 w-4" />
            <span class="hidden md:inline">Notifications</span>
            <Badge v-if="notifications.length > 0" variant="destructive" class="ml-0.5 px-1 min-w-[1rem] h-4 text-[10px]">{{ notifications.length }}</Badge>
          </TabsTrigger>
          <TabsTrigger value="profile" class="gap-2">
            <User class="h-4 w-4" />
            <span class="hidden md:inline">Profile</span>
          </TabsTrigger>
        </TabsList>

        <!-- OVERVIEW TAB -->
        <TabsContent value="overview" class="space-y-8 animate-in fade-in slide-in-from-bottom-2">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <Card class="border-none shadow-sm dark:bg-zinc-900 overflow-hidden group">
              <CardHeader class="flex flex-row items-center justify-between pb-2">
                <CardTitle class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Wallet Balance</CardTitle>
                <DollarSign class="h-4 w-4 text-[#FF2D20]" />
              </CardHeader>
              <CardContent>
                <div class="text-3xl font-bold text-zinc-900 dark:text-white">RM {{ wallet.balance.toFixed(2) }}</div>
                <p class="text-xs text-zinc-400 mt-1">Ready for next session</p>
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
                    <span class="text-zinc-500">Energy (kWh)</span>
                    <span class="font-bold font-mono">{{ chargingData.kwh.toFixed(2) }}</span>
                  </div>
                  <div class="flex justify-between items-center text-sm">
                    <span class="text-zinc-500">Cost (RM)</span>
                    <span class="font-bold font-mono">{{ chargingData.cost.toFixed(2) }}</span>
                  </div>
                  <div class="flex justify-between items-center text-sm text-[10px] font-mono text-zinc-400">
                    <span>{{ chargingData.time }} elapsed</span>
                  </div>
                  <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 animate-pulse transition-all duration-1000" :style="{ width: `${Math.min(chargingData.kwh * 2, 100)}%` }"></div>
                  </div>
                  <Button variant="destructive" size="sm" class="w-full" @click="stopCharging">
                    Stop Charging
                  </Button>
                </div>
                <div v-else class="text-center py-6 text-zinc-400">
                  <p class="text-sm italic">No active session</p>
                  <Button variant="link" size="sm" class="mt-2 text-[#FF2D20]" @click="activeTab = 'map'">Find a Charger</Button>
                </div>
              </CardContent>
              <div class="absolute bottom-0 left-0 h-1 bg-green-500 w-full transform translate-y-1 group-hover:translate-y-0 transition-transform"></div>
            </Card>

            <Card class="border-none shadow-sm dark:bg-zinc-900 overflow-hidden group">
              <CardHeader class="flex flex-row items-center justify-between pb-2">
                <CardTitle class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Default Vehicle</CardTitle>
                <Car class="h-4 w-4 text-blue-500" />
              </CardHeader>
              <CardContent>
                <div v-if="defaultVehicle" class="space-y-1">
                  <div class="text-lg font-bold">{{ defaultVehicle.brand }} {{ defaultVehicle.model }}</div>
                  <p class="text-sm text-zinc-500">{{ defaultVehicle.plate_number }}</p>
                  <Badge variant="outline" class="mt-2 text-[10px]">{{ defaultVehicle.battery_capacity_kwh }} kWh • {{ defaultVehicle.connector_type }}</Badge>
                </div>
                <div v-else class="text-center py-6 text-zinc-400">
                  <p class="text-sm">No vehicles added</p>
                  <Button variant="link" size="sm" class="mt-2 text-blue-600" @click="activeTab = 'vehicles'">Register EV</Button>
                </div>
              </CardContent>
              <div class="absolute bottom-0 left-0 h-1 bg-blue-500 w-full transform translate-y-1 group-hover:translate-y-0 transition-transform"></div>
            </Card>
          </div>

          <Card class="border-none shadow-sm dark:bg-zinc-900">
            <CardHeader>
              <CardTitle>Recent Activity</CardTitle>
            </CardHeader>
            <CardContent>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Date</TableHead>
                    <TableHead>Location</TableHead>
                    <TableHead>Energy</TableHead>
                    <TableHead class="text-right">Action</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow v-for="session in (portalSessions?.data || []).slice(0, 5)" :key="session.id">
                    <TableCell>{{ new Date(session.start_time).toLocaleDateString() }}</TableCell>
                    <TableCell>{{ session.charge_point?.location?.name || session.charge_point?.identifier || 'Main Station' }}</TableCell>
                    <TableCell>{{ session.kwh_consumed }} kWh</TableCell>
                    <TableCell class="text-right">
                      <Button variant="ghost" size="sm" class="h-8 gap-2" @click="downloadInvoice(session)">
                        <Download class="h-3 w-3" />
                        Receipt
                      </Button>
                    </TableCell>
                  </TableRow>
                  <TableRow v-if="!portalSessions?.data?.length">
                    <TableCell colspan="4" class="text-center py-4 text-zinc-400 italic">No activity yet</TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </TabsContent>

        <!-- MAP TAB - forceMount so content exists when tab is selected -->
        <TabsContent value="map" force-mount class="mt-2 data-[state=inactive]:hidden">
          <div class="space-y-4">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Find a charger</h2>
            <p class="text-sm text-zinc-500">Select a station on the map or from the list below.</p>

            <div class="relative rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800" style="height: 400px;">
              <div ref="mapRef" class="absolute inset-0 w-full h-full z-0"></div>
              <div v-if="mapLoading" class="absolute inset-0 flex items-center justify-center z-20 bg-zinc-100 dark:bg-zinc-800">
                <p class="text-zinc-500">Loading map...</p>
              </div>
              <div v-else-if="mapError" class="absolute inset-0 flex items-center justify-center z-20 bg-zinc-100 dark:bg-zinc-800">
                <p class="text-zinc-500">Map could not load. Use the list below.</p>
              </div>

              <!-- Station Detail Overlay -->
              <div v-if="selectedStation" class="absolute bottom-6 left-6 right-6 md:right-auto md:w-96 bg-white dark:bg-zinc-900 p-6 rounded-2xl shadow-2xl border border-zinc-100 dark:border-zinc-800 z-30">
              <div class="flex justify-between items-start mb-4">
                <div>
                  <h3 class="font-bold text-lg">{{ selectedStation.name }}</h3>
                  <p class="text-sm text-zinc-500">{{ selectedStation.address }}</p>
                </div>
                <Button variant="ghost" size="sm" @click="toggleFavorite(selectedStation)" :class="{ 'text-yellow-500': selectedStation.isFavorite }">
                  <Star class="h-4 w-4" :fill="selectedStation.isFavorite ? 'currentColor' : 'none'" />
                </Button>
              </div>
              
              <div class="flex gap-4 mb-6">
                <Badge :variant="selectedStation.status === 'Available' ? 'success' : 'destructive'" class="bg-opacity-10 text-opacity-100">
                   {{ selectedStation.status }}
                </Badge>
                <div class="flex items-center gap-1 text-xs text-zinc-500">
                  <MapPin class="h-3 w-3" />
                  {{ selectedStation.distance }} km
                </div>
                <div class="flex items-center gap-1 text-xs text-zinc-500">
                  <StarIcon class="h-3 w-3 text-yellow-500 fill-current" />
                  {{ selectedStation.rating }} ({{ selectedStation.reviews }})
                </div>
              </div>

              <div class="grid grid-cols-2 gap-3">
                <Button :disabled="selectedStation.status !== 'Available'" variant="outline" class="gap-2" @click="reserveStation(selectedStation)">
                   <Calendar class="h-4 w-4" />
                   Reserve
                </Button>
                <Button :disabled="selectedStation.status !== 'Available' || activeSession" class="bg-[#FF2D20] text-white hover:bg-[#E0261B] border-none gap-2" @click="startChargingMock(selectedStation)">
                   <Play class="h-4 w-4" />
                   Start Now
                </Button>
              </div>
              <Button variant="ghost" size="sm" class="w-full mt-2 text-zinc-400" @click="selectedStation = null">Close</Button>
              </div>

              <!-- Map Legend -->
              <div class="absolute top-4 right-4 z-10">
                <Card class="bg-white/90 dark:bg-zinc-900/90 backdrop-blur border-none p-2 shadow-sm">
                  <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2 text-[10px] font-medium px-2">
                      <span class="w-2 h-2 rounded-full bg-green-500"></span> Available
                    </div>
                    <div class="flex items-center gap-2 text-[10px] font-medium px-2">
                      <span class="w-2 h-2 rounded-full bg-red-500"></span> Occupied
                    </div>
                  </div>
                </Card>
              </div>
            </div>

            <!-- Stations list - always visible so tab is never blank -->
            <Card class="border border-zinc-200 dark:border-zinc-800">
              <CardHeader>
                <CardTitle class="text-base">Charging stations</CardTitle>
                <CardDescription>Click a station to see details and start charging</CardDescription>
              </CardHeader>
              <CardContent>
                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                  <button
                    v-for="s in stations"
                    :key="s.id"
                    type="button"
                    class="flex items-center gap-3 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 text-left transition-colors hover:border-[#FF2D20] hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                    :class="{ 'ring-2 ring-[#FF2D20] border-[#FF2D20]': selectedStation?.id === s.id }"
                    @click="selectedStation = s"
                  >
                    <div :class="['h-10 w-10 shrink-0 rounded-full flex items-center justify-center', s.status === 'Available' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600']">
                      <Zap class="h-5 w-5" />
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="font-medium truncate">{{ s.name }}</p>
                      <p class="text-xs text-zinc-500 truncate">{{ s.address }}</p>
                      <p class="text-xs mt-1">
                        <span :class="s.status === 'Available' ? 'text-green-600' : 'text-red-600'">{{ s.status }}</span>
                        · {{ s.distance }} km
                      </p>
                    </div>
                  </button>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        <!-- VEHICLES TAB -->
        <TabsContent value="vehicles" class="space-y-6">
          <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold">My EV Profiles</h2>
            <Button class="bg-[#FF2D20] text-white gap-2" @click="showAddVehicle = true">
              <Plus class="h-4 w-4" /> Add Vehicle
            </Button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <Card v-for="vehicle in vehicles" :key="vehicle.id" class="border shadow-sm group hover:border-[#FF2D20] transition-colors relative overflow-hidden">
               <CardContent class="pt-6">
                  <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl">
                      <Car class="h-6 w-6 text-zinc-600" />
                    </div>
                    <Badge v-if="vehicle.is_default" class="bg-blue-100 text-blue-700 hover:bg-blue-100 border-none">Default</Badge>
                  </div>
                  <h3 class="text-xl font-bold">{{ vehicle.brand }} {{ vehicle.model }}</h3>
                  <p class="text-zinc-500 font-mono">{{ vehicle.plate_number }}</p>
                  
                  <div class="grid grid-cols-2 gap-4 mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <div>
                      <p class="text-[10px] text-zinc-400 uppercase">Capacity</p>
                      <p class="text-sm font-semibold">{{ vehicle.battery_capacity_kwh }} kWh</p>
                    </div>
                    <div>
                      <p class="text-[10px] text-zinc-400 uppercase">Connector</p>
                      <p class="text-sm font-semibold">{{ vehicle.connector_type }}</p>
                    </div>
                  </div>

                  <div class="flex gap-2 mt-6">
                    <Button variant="outline" size="sm" class="flex-1 h-8" v-if="!vehicle.is_default" @click="setDefaultVehicle(vehicle)">Set Default</Button>
                    <Button variant="ghost" size="sm" class="h-8 text-red-500 hover:text-red-600 hover:bg-red-50 px-2" @click="deleteVehicle(vehicle)">
                       <Trash class="h-4 w-4" />
                    </Button>
                  </div>
               </CardContent>
            </Card>
          </div>
        </TabsContent>

        <!-- HISTORY TAB -->
        <TabsContent value="history" class="space-y-6">
          <Card class="border-none shadow-sm dark:bg-zinc-900 p-0">
             <CardHeader class="px-6 py-4 flex flex-row items-center justify-between">
                <CardTitle>Transaction History</CardTitle>
                <div class="flex gap-2 items-center">
                   <div class="relative w-48 md:w-64">
                     <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-zinc-500" />
                     <Input 
                       placeholder="Search locations..." 
                       v-model="sessionSearch"
                       class="pl-9 h-9"
                     />
                   </div>
                </div>
             </CardHeader>
             <CardContent class="p-0">
                <Table>
                  <TableHeader class="bg-zinc-50 dark:bg-zinc-800">
                    <TableRow>
                      <TableHead class="pl-6">Session ID</TableHead>
                      <TableHead>Location</TableHead>
                      <TableHead>DateTime</TableHead>
                      <TableHead>Energy</TableHead>
                      <TableHead>Cost</TableHead>
                      <TableHead class="pr-6 text-right">Invoice</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    <TableRow v-for="session in portalSessions.data" :key="session.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 cursor-pointer" @click="showReceipt(session)">
                      <TableCell class="font-mono text-[10px] pl-6">#{{ String(session.id).padStart(6, '0') }}</TableCell>
                      <TableCell class="font-medium">{{ session.charge_point?.location?.name || session.charge_point?.identifier || 'N/A' }}</TableCell>
                      <TableCell class="text-xs text-zinc-500">
                         {{ new Date(session.start_time).toLocaleDateString() }} <br/>
                         {{ new Date(session.start_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
                      </TableCell>
                      <TableCell>{{ session.kwh_consumed }} kWh</TableCell>
                      <TableCell class="font-bold">RM {{ Number(session.total_cost).toFixed(2) }}</TableCell>
                      <TableCell class="pr-6 text-right">
                         <Button variant="ghost" size="icon" class="h-8 w-8 text-blue-600" @click.stop="downloadInvoice(session)">
                            <FileText class="h-4 w-4" />
                         </Button>
                      </TableCell>
                    </TableRow>
                    <TableRow v-if="!portalSessions.data.length">
                      <TableCell colspan="6" class="text-center py-12 text-zinc-400">No transactions found</TableCell>
                    </TableRow>
                  </TableBody>
                </Table>
                
                <!-- Pagination -->
                <div class="flex items-center justify-between px-6 py-4 border-t border-zinc-100 dark:border-zinc-800" v-if="portalSessions.last_page > 1">
                  <div class="text-xs text-zinc-500">
                    Showing {{ (portalSessions.current_page - 1) * portalSessions.per_page + 1 }} to {{ Math.min(portalSessions.current_page * portalSessions.per_page, portalSessions.total) }} of {{ portalSessions.total }} entries
                  </div>
                  <div class="flex gap-2">
                    <Button variant="outline" size="sm" :disabled="portalSessions.current_page === 1" @click="fetchSessions(portalSessions.current_page - 1)">Previous</Button>
                    <Button variant="outline" size="sm" :disabled="portalSessions.current_page === portalSessions.last_page" @click="fetchSessions(portalSessions.current_page + 1)">Next</Button>
                  </div>
                </div>
             </CardContent>
          </Card>
        </TabsContent>

        <!-- BILLING TAB (Wallet + Subscriptions + Promo + dynamic pricing) -->
        <TabsContent value="wallet" class="space-y-6">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 space-y-6">
              <!-- Wallet Card -->
              <Card class="relative bg-zinc-900 text-white overflow-hidden p-6 rounded-3xl min-h-[200px] flex flex-col justify-between shadow-2xl">
                 <div class="absolute top-4 right-4 text-zinc-800">
                    <WalletIcon class="h-20 w-20" />
                 </div>
                 <div class="z-10">
                    <p class="text-zinc-400 text-[10px] font-bold uppercase tracking-widest">Prepaid Balance</p>
                    <h3 class="text-4xl font-bold mt-1">RM {{ wallet.balance.toFixed(2) }}</h3>
                 </div>
                 <div class="z-10 flex flex-col gap-1">
                    <p class="text-[10px] text-zinc-500 uppercase tracking-widest">Linked Card</p>
                    <p class="text-xs font-mono">•••• 4592</p>
                 </div>
                 <div class="absolute -bottom-12 -right-12 w-32 h-32 bg-[#FF2D20] rounded-full blur-[80px] opacity-20"></div>
              </Card>

              <!-- Quick Top Up -->
              <Card class="border shadow-sm p-4 space-y-4">
                 <h3 class="font-bold text-sm flex items-center gap-2">
                   <Plus class="h-4 w-4 text-[#FF2D20]" />
                   Quick top Up
                 </h3>
                 <div class="grid grid-cols-3 gap-2">
                    <Button variant="outline" size="sm" v-for="amount in [20, 50, 100]" :key="amount" class="text-xs" @click="topUp(amount)">
                       RM {{ amount }}
                    </Button>
                 </div>
                 <div class="space-y-2">
                    <div class="flex gap-2">
                       <Input type="number" placeholder="Other" v-model="customTopUp" class="h-8 text-xs" />
                       <Button size="sm" class="bg-zinc-900 text-white" @click="topUp(customTopUp)">Add</Button>
                    </div>
                 </div>
              </Card>

              <!-- Promo Code -->
              <Card class="border shadow-sm p-4 space-y-2">
                <h3 class="font-bold text-sm">Promo Code</h3>
                <div class="flex gap-2">
                   <Input placeholder="Enter code" v-model="promoCode" class="h-8 text-xs font-mono" />
                   <Button variant="outline" size="sm" @click="checkPromo">Apply</Button>
                </div>
                <p v-if="appliedPromo" class="text-[10px] text-green-600 font-bold">✓ {{ appliedPromo.code }} applied ({{ appliedPromo.type === 'percentage' ? '-' + appliedPromo.value + '%' : '-RM' + appliedPromo.value }})</p>
              </Card>
            </div>

            <div class="lg:col-span-2 space-y-6">
              <!-- Current Plan -->
              <Card class="border-none shadow-sm dark:bg-zinc-900 p-6 relative overflow-hidden bg-gradient-to-br from-blue-50 to-white dark:from-zinc-900 dark:to-zinc-800">
                <div v-if="activeSubscription" class="space-y-4">
                  <div class="flex justify-between items-start">
                    <div>
                      <Badge variant="default" class="bg-blue-600 mb-2">ACTIVE PLAN</Badge>
                      <h2 class="text-2xl font-black">{{ activeSubscription.plan.name }}</h2>
                      <p class="text-zinc-500 text-sm">Renews on {{ new Date(activeSubscription.expires_at).toLocaleDateString() }}</p>
                    </div>
                    <div class="text-right">
                       <p class="text-2xl font-bold text-blue-600">{{ activeSubscription.plan.discount_percentage }}%</p>
                       <p class="text-[10px] text-zinc-400 uppercase font-bold">Session Discount</p>
                    </div>
                  </div>
                  <div class="grid grid-cols-2 gap-4 py-4 border-t border-blue-100 dark:border-zinc-700">
                    <div>
                      <p class="text-[10px] text-zinc-500 uppercase">Inclusive Energy</p>
                      <p class="font-bold">{{ activeSubscription.plan.free_kwh }} kWh / mo</p>
                    </div>
                    <div>
                      <p class="text-[10px] text-zinc-500 uppercase">Status</p>
                      <p class="font-bold text-green-600">Autopay On</p>
                    </div>
                  </div>
                </div>
                <div v-else class="py-8 text-center space-y-4">
                  <Zap class="h-10 w-10 text-zinc-300 mx-auto" />
                  <div>
                    <h3 class="font-bold">No Subscription Active</h3>
                    <p class="text-sm text-zinc-500">Subscribe to get lower rates and inclusive kWh.</p>
                  </div>
                </div>
              </Card>

              <!-- Plans Comparison -->
              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <h3 class="font-bold">Subscription charging plans</h3>
                  <p class="text-[10px] text-zinc-500 uppercase font-bold tracking-widest">Malaysia Only</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <Card v-for="plan in billingPlans" :key="plan.id" class="p-4 border shadow-sm flex flex-col justify-between hover:border-blue-300 transition-colors" :class="{ 'ring-2 ring-blue-500 bg-blue-50/10': activeSubscription?.subscription_plan_id === plan.id }">
                     <div>
                        <div class="flex justify-between items-center mb-2">
                          <h4 class="font-bold text-base">{{ plan.name }}</h4>
                          <span v-if="activeSubscription?.subscription_plan_id === plan.id" class="text-blue-600"><CheckCircle class="h-4 w-4" /></span>
                        </div>
                        <p class="text-xs text-zinc-500 leading-relaxed mb-4">{{ plan.description }}</p>
                        <div class="space-y-1 mb-6">
                           <div class="flex items-center gap-2 text-xs font-semibold">
                             <Zap class="h-3 w-3 text-blue-500" /> {{ plan.free_kwh }} kWh Included
                           </div>
                           <div class="flex items-center gap-2 text-xs font-semibold">
                             <DollarSign class="h-3 w-3 text-green-500" /> {{ plan.discount_percentage }}% Discount
                           </div>
                        </div>
                     </div>
                     <div class="flex items-center justify-between">
                        <div class="text-xl font-black">RM {{ plan.price }}<span class="text-xs text-zinc-400 font-normal">/mo</span></div>
                        <Button size="sm" :disabled="activeSubscription?.subscription_plan_id === plan.id" class="h-8 text-xs" :variant="activeSubscription?.subscription_plan_id === plan.id ? 'outline' : 'default'" @click="purchaseSubscription(plan)">
                           {{ activeSubscription?.subscription_plan_id === plan.id ? 'Current' : 'Subscribe' }}
                        </Button>
                     </div>
                  </Card>
                </div>
              </div>

              <!-- dynamic pricing notice -->
              <Card class="bg-zinc-50 dark:bg-zinc-800/50 border-none p-4">
                <div class="flex gap-4 items-start">
                  <div class="p-2 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                    <Clock class="h-4 w-4 text-yellow-600" />
                  </div>
                  <div>
                    <h4 class="text-xs font-bold uppercase tracking-widest text-zinc-800 dark:text-zinc-200">Dynamic Pricing Active</h4>
                    <p class="text-[11px] text-zinc-500 mt-1">Peak hours (18:00 - 22:00) apply a surcharge. Off-peak energy is 20% cheaper for all users. Tax (SST 8%) is calculated on total session cost.</p>
                  </div>
                </div>
              </Card>
            </div>
          </div>
        </TabsContent>
        
        <!-- FAVORITES TAB -->
        <TabsContent value="favorites" class="space-y-6">
           <div v-if="favorites.length === 0" class="text-center py-20 bg-zinc-50 dark:bg-zinc-900 rounded-3xl border border-dashed border-zinc-200 dark:border-zinc-800">
              <Star class="h-12 w-12 text-zinc-200 dark:text-zinc-700 mx-auto mb-4" />
              <h3 class="text-lg font-bold">No favorite stations yet</h3>
              <p class="text-zinc-500 text-sm max-w-xs mx-auto mt-1">Add stations you frequently use to access them quickly here.</p>
              <Button variant="link" class="mt-4 text-[#FF2D20]" @click="activeTab = 'map'">Explore Map</Button>
           </div>
           <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <Card v-for="station in favorites" :key="station.id" class="overflow-hidden border group">
                 <div class="h-32 bg-zinc-100 dark:bg-zinc-800 relative">
                    <img :src="`https://picsum.photos/seed/${station.id}/400/200`" class="w-full h-full object-cover opacity-60 group-hover:opacity-100 transition-opacity" />
                    <div class="absolute top-2 right-2">
                       <Button variant="secondary" size="icon" class="rounded-full shadow-sm h-8 w-8 text-yellow-500" @click="toggleFavorite(station)">
                          <Star fill="currentColor" class="h-4 w-4" />
                       </Button>
                    </div>
                 </div>
                 <CardContent class="p-5">
                    <h3 class="font-bold cursor-pointer hover:text-[#FF2D20]" @click="selectedStation = station; activeTab = 'map'">{{ station.name }}</h3>
                    <p class="text-xs text-zinc-500 mt-1 truncate">{{ station.address }}</p>
                    <div class="flex items-center gap-3 mt-4">
                       <Badge :variant="station.status === 'Available' ? 'success' : 'destructive'" class="text-[10px]">{{ station.status }}</Badge>
                       <span class="text-[10px] text-zinc-400 font-medium">{{ station.distance }} km away</span>
                    </div>
                 </CardContent>
              </Card>
           </div>
        </TabsContent>

        <!-- PROFILE TAB -->
        <TabsContent value="profile" class="space-y-6">
          <Card class="border-none shadow-sm dark:bg-zinc-900 overflow-hidden">
            <CardHeader>
              <CardTitle>My Profile</CardTitle>
              <CardDescription>Your account information</CardDescription>
            </CardHeader>
            <CardContent class="flex flex-col sm:flex-row items-start gap-6">
              <Avatar class="h-20 w-20 border-2 border-zinc-200 dark:border-zinc-700">
                <AvatarImage :src="authUser?.email ? `https://avatar.vercel.sh/${authUser.email}.png` : undefined" />
                <AvatarFallback class="bg-[#FF2D20] text-white text-2xl font-bold">
                  {{ (authUser?.name || 'U').charAt(0).toUpperCase() }}
                </AvatarFallback>
              </Avatar>
              <div class="space-y-3 flex-1">
                <div>
                  <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Name</p>
                  <p class="text-lg font-semibold text-zinc-900 dark:text-white">{{ authUser?.name || '—' }}</p>
                </div>
                <div>
                  <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Email</p>
                  <p class="text-lg text-zinc-700 dark:text-zinc-300">{{ authUser?.email || '—' }}</p>
                </div>
                <div v-if="authUser?.role" class="pt-2">
                  <Badge variant="outline" class="text-xs">{{ authUser.role }}</Badge>
                </div>
                <div class="pt-4">
                  <Link
                    :href="'/profile'"
                    class="inline-flex items-center gap-2 text-sm font-medium text-[#FF2D20] hover:underline"
                    @click="navigateToProfile($event)"
                  >
                    <User class="h-4 w-4" />
                    Edit profile &amp; password
                  </Link>
                </div>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <!-- NOTIFICATIONS TAB -->
        <TabsContent value="notifications" class="space-y-6">
           <div class="flex justify-between items-center px-1">
             <h2 class="text-xl font-bold">Latest Notifications</h2>
             <Button variant="ghost" size="sm" class="text-blue-600 text-[10px] uppercase font-bold tracking-wider" @click="axios.post('/api/client/notifications/read').then(() => fetchNotifications())" v-if="notifications.length > 0">Mark all as read</Button>
           </div>
           
           <div class="space-y-3">
            <Card class="border-none shadow-sm dark:bg-zinc-900 overflow-hidden">
              <CardContent class="p-6">
                <!-- Push Toggle -->
                <div class="flex items-center gap-3 mb-6 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800">
                  <Bell class="h-5 w-5 text-blue-600" />
                  <div class="flex-1">
                    <p class="text-sm font-bold">Real-time alerts</p>
                    <p class="text-[11px] text-zinc-500">Get push notifications for session events and low balance.</p>
                  </div>
                  <Button v-if="!pushEnabled" size="sm" class="bg-blue-600 text-white border-none h-8 px-4" :disabled="pushEnabling" @click="enablePush">
                    {{ pushEnabling ? 'Enabling...' : 'Enable' }}
                  </Button>
                  <Badge v-else variant="default" class="bg-green-600 border-none">Active</Badge>
                </div>

                <!-- Notifications List -->
                <div class="space-y-3">
                  <div v-for="n in notifications" :key="n.id" :class="['flex items-start gap-4 p-4 rounded-xl transition-all cursor-pointer', n.is_read ? 'bg-zinc-50/50 dark:bg-zinc-800/10' : 'bg-white dark:bg-zinc-900 shadow-sm ring-1 ring-zinc-100 dark:ring-zinc-800']" @click="axios.post('/api/client/notifications/read').then(() => fetchNotifications())">
                     <div :class="['h-10 w-10 shrink-0 rounded-full flex items-center justify-center', n.type === 'success' ? 'bg-green-100 text-green-600' : n.type === 'warning' ? 'bg-yellow-100 text-yellow-600' : n.type === 'danger' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600']">
                        <Zap class="h-5 w-5" v-if="n.type === 'success'" />
                        <Bell class="h-5 w-5" v-else />
                     </div>
                     <div class="flex-1">
                        <div class="flex justify-between items-start">
                           <p class="font-bold text-sm">{{ n.title }}</p>
                           <span class="text-[10px] text-zinc-400 font-mono uppercase">{{ n.time }}</span>
                        </div>
                        <p class="text-xs text-zinc-500 mt-1">{{ n.message }}</p>
                     </div>
                  </div>
                  <div v-if="!notifications.length" class="text-center py-20 bg-zinc-50 dark:bg-zinc-800/20 rounded-2xl border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                     <BellOff class="h-10 w-10 text-zinc-300 mx-auto mb-3" />
                     <p class="text-zinc-500 italic text-sm">No notifications yet</p>
                  </div>
                </div>
              </CardContent>
            </Card>
           </div>
        </TabsContent>
        </Tabs>

      <!-- Add Vehicle Modal -->
      <Dialog :open="showAddVehicle" @update:open="showAddVehicle = $event">
        <DialogContent class="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>Add EV Vehicle</DialogTitle>
            <DialogDescription>Register your electric vehicle for charging sessions.</DialogDescription>
          </DialogHeader>
          <div class="grid gap-4 py-4">
            <div class="grid gap-2">
              <Label>Brand</Label>
              <Input v-model="vehicleForm.brand" placeholder="e.g. Tesla, BYD" />
            </div>
            <div class="grid gap-2">
              <Label>Model</Label>
              <Input v-model="vehicleForm.model" placeholder="e.g. Model 3, Atto 3" />
            </div>
            <div class="grid gap-2">
              <Label>Plate Number</Label>
              <Input v-model="vehicleForm.plate_number" placeholder="e.g. WPP 1234" />
            </div>
            <div class="grid gap-2">
              <Label>Battery capacity (kWh)</Label>
              <Input v-model="vehicleForm.battery_capacity_kwh" type="number" min="0" placeholder="e.g. 75" />
            </div>
            <div class="grid gap-2">
              <Label>Connector type</Label>
              <select v-model="vehicleForm.connector_type" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm">
                <option v-for="c in connectorTypes" :key="c" :value="c">{{ c }}</option>
              </select>
            </div>
            <div class="flex items-center gap-2">
              <input id="default-vehicle" type="checkbox" v-model="vehicleForm.is_default" class="rounded border-zinc-300" />
              <Label for="default-vehicle">Set as default vehicle</Label>
            </div>
          </div>
          <DialogFooter>
            <Button variant="ghost" @click="showAddVehicle = false">Cancel</Button>
            <Button class="bg-[#FF2D20] text-white" :disabled="vehicleFormSaving" @click="saveVehicle">
              {{ vehicleFormSaving ? 'Adding...' : 'Add Vehicle' }}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <!-- Reserve Slot Modal -->
      <Dialog :open="showReserveModal" @update:open="showReserveModal = $event">
        <DialogContent class="sm:max-w-sm">
          <DialogHeader>
            <DialogTitle>Reserve charging slot</DialogTitle>
            <DialogDescription v-if="selectedStation">
              {{ selectedStation.name }} — choose duration
            </DialogDescription>
          </DialogHeader>
          <div class="py-4 space-y-4">
            <div class="grid grid-cols-2 gap-2">
              <Button v-for="mins in [15, 30, 45, 60]" :key="mins" :variant="reserveSlotMinutes === mins ? 'default' : 'outline'" @click="reserveSlotMinutes = mins">
                {{ mins }} min
              </Button>
            </div>
          </div>
          <DialogFooter>
            <Button variant="ghost" @click="showReserveModal = false">Cancel</Button>
            <Button class="bg-[#FF2D20] text-white" @click="confirmReservation">Reserve</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <!-- Rating & Feedback Modal -->
      <Dialog :open="showRatingModal" @update:open="showRatingModal = $event">
        <DialogContent class="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>Rate your session</DialogTitle>
            <DialogDescription>
              How was your charging experience at {{ lastStationName }}?
            </DialogDescription>
          </DialogHeader>
          <div class="flex flex-col items-center py-6 space-y-4">
            <div class="flex gap-2">
              <StarIcon v-for="star in 5" :key="star" 
                        @click="currentRating = star"
                        :class="['h-8 w-8 cursor-pointer transition-colors', star <= currentRating ? 'text-yellow-500 fill-current' : 'text-zinc-300']" />
            </div>
            <div class="w-full space-y-2">
               <Label>Comments (Optional)</Label>
               <Input placeholder="Tell us more..." v-model="feedbackComment" />
            </div>
          </div>
          <DialogFooter>
            <Button variant="ghost" @click="showRatingModal = false">Skip</Button>
            <Button class="bg-[#FF2D20] text-white" @click="submitFeedback">Submit Feedback</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <!-- Feedback Display (Toast-like) -->
      <div v-if="toastMsg" class="fixed bottom-8 right-8 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 animate-in slide-in-from-right-10 z-50">
         <CheckCircle v-if="toastType === 'success'" class="h-5 w-5 text-green-500" />
         <AlertCircle v-else class="h-5 w-5 text-red-500" />
         <p class="text-sm font-medium">{{ toastMsg }}</p>
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
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/Components/ui/tabs';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/Components/ui/dialog';
import { 
  Zap, DollarSign, ChevronLeft, ChevronRight, RefreshCw, 
  LayoutDashboard, MapPin, Car, History, Wallet as WalletIcon, Star, 
  Bell, Plus, Map as MapIcon, Calendar, Play, Power, 
  Download, Filter, FileText, Trash, Star as StarIcon,
  CheckCircle, AlertCircle, Info, Clock, Square, User
} from 'lucide-vue-next';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { ref, onMounted, computed, watch, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Link } from '@inertiajs/vue3';
import 'leaflet/dist/leaflet.css';

const authUser = computed(() => {
  try {
    return usePage().props.auth?.user ?? null;
  } catch {
    return typeof window !== 'undefined' && window.usePage ? window.usePage().props?.auth?.user ?? null : null;
  }
});

function navigateToProfile(e) {
  const isStandalone = !document.getElementById('app')?.dataset?.page;
  if (isStandalone) {
    e.preventDefault();
    window.location.href = '/profile';
  }
}

// State
const activeTab = ref('overview');
const activeSession = ref(false);
const loading = ref(false);
const selectedStation = ref(null);
const currentSessionId = ref(null);
const customTopUp = ref('');
const toastMsg = ref('');
const toastType = ref('success');
const showRatingModal = ref(false);
const currentRating = ref(0);
const feedbackComment = ref('');
const lastStationName = ref('Main Station');
const showAddVehicle = ref(false);
const showReserveModal = ref(false);
const reserveSlotMinutes = ref(15);
const vehicleForm = ref({
  brand: '',
  model: '',
  plate_number: '',
  battery_capacity_kwh: '',
  connector_type: 'Type 2 / CCS',
  is_default: false
});
const vehicleFormSaving = ref(false);
const connectorTypes = ['Type 2 / CCS', 'Type 2', 'CHAdeMO', 'CCS Combo 1', 'CCS Combo 2', 'Tesla'];
const pushEnabled = ref(false);
const pushEnabling = ref(false);
const mapRef = ref(null);
const mapLoading = ref(false);
const mapError = ref(false);
let leafletMap = null;
let leafletMarkers = [];

// New Search & Pagination Refs
const sessionSearch = ref('');
const sessionPage = ref(1);
const notifications = ref([]);
const billingPlans = ref([]);
const activeSubscription = ref(null);
const promoCode = ref('');
const appliedPromo = ref(null);

const fetchNotifications = async () => {
  try {
    const res = await axios.get('/api/client/notifications');
    notifications.value = res.data;
  } catch (err) {
    console.error('Failed to fetch notifications:', err);
  }
};

async function enablePush() {
  if (!('Notification' in window) || !('serviceWorker' in navigator)) {
    showToast('Push notifications not supported in this browser', 'error');
    return;
  }
  pushEnabling.value = true;
  try {
    const permission = await Notification.requestPermission();
    if (permission === 'granted') {
      const reg = await navigator.serviceWorker.getRegistration() || await navigator.serviceWorker.register('/sw.js', { scope: '/' });
      if (reg) {
        pushEnabled.value = true;
        showToast('Push notifications enabled');
      }
    } else {
      showToast('Permission denied', 'error');
    }
  } catch (e) {
    showToast('Could not enable push notifications', 'error');
  } finally {
    pushEnabling.value = false;
  }
}

// Continuous Monitoring Data (Mock)
const chargingData = ref({
  kwh: 0,
  cost: 0,
  time: '00:00:00',
  startTime: null
});

let monitorInterval = null;

const startMonitoring = () => {
  chargingData.value.startTime = new Date();
  monitorInterval = setInterval(() => {
    chargingData.value.kwh += 0.05 + Math.random() * 0.1;
    chargingData.value.cost = chargingData.value.kwh * 0.85; // RM 0.85 per kWh
    
    const diff = Math.floor((new Date() - chargingData.value.startTime) / 1000);
    const h = Math.floor(diff / 3600).toString().padStart(2, '0');
    const m = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
    const s = (diff % 60).toString().padStart(2, '0');
    chargingData.value.time = `${h}:${m}:${s}`;
  }, 1000);
};

const stopMonitoring = () => {
    if (monitorInterval) clearInterval(monitorInterval);
};

// Stations (fetched from DB)
const stations = ref([]);
const fetchStations = async () => {
  try {
    const res = await axios.get('/api/client/stations');
    stations.value = res.data;
  } catch (err) {
    console.error('Failed to fetch stations:', err);
  }
};

// Vehicles (fetched from API or fallback mock)
const vehicles = ref([]);
const defaultVehicle = computed(() => vehicles.value.find(v => v.is_default));

const fetchVehicles = async () => {
  try {
    const res = await axios.get('/api/client/vehicles');
    if (Array.isArray(res.data) && res.data.length > 0) {
      vehicles.value = res.data.map(v => ({ ...v, is_default: !!v.is_default }));
    }
  } catch (_) {
    vehicles.value = [
      { id: 1, brand: 'Tesla', model: 'Model 3', plate_number: 'WPP 1234', battery_capacity_kwh: 75, connector_type: 'Type 2 / CCS', is_default: true },
    ];
  }
};

const saveVehicle = async () => {
  if (!vehicleForm.value.brand?.trim() || !vehicleForm.value.model?.trim() || !vehicleForm.value.plate_number?.trim()) {
    showToast('Please fill brand, model and plate number', 'error');
    return;
  }
  vehicleFormSaving.value = true;
  try {
    const res = await axios.post('/api/client/vehicles', {
      brand: vehicleForm.value.brand.trim(),
      model: vehicleForm.value.model.trim(),
      plate_number: vehicleForm.value.plate_number.trim(),
      battery_capacity_kwh: vehicleForm.value.battery_capacity_kwh ? Number(vehicleForm.value.battery_capacity_kwh) : null,
      connector_type: vehicleForm.value.connector_type || 'Type 2 / CCS',
      is_default: vehicleForm.value.is_default,
    });
    vehicles.value = [...vehicles.value, { ...res.data, is_default: !!res.data.is_default }];
    if (res.data.is_default) {
      vehicles.value.forEach(v => { v.is_default = v.id === res.data.id; });
    }
    showAddVehicle.value = false;
    vehicleForm.value = { brand: '', model: '', plate_number: '', battery_capacity_kwh: '', connector_type: 'Type 2 / CCS', is_default: false };
    showToast('Vehicle added successfully');
  } catch (e) {
    showToast(e.response?.data?.message || 'Failed to add vehicle', 'error');
  } finally {
    vehicleFormSaving.value = false;
  }
};

const deleteVehicle = async (vehicle) => {
  if (!confirm(`Remove ${vehicle.brand} ${vehicle.model}?`)) return;
  try {
    await axios.delete(`/api/client/vehicles/${vehicle.id}`);
    vehicles.value = vehicles.value.filter(v => v.id !== vehicle.id);
    showToast('Vehicle removed');
  } catch (_) {
    vehicles.value = vehicles.value.filter(v => v.id !== vehicle.id);
    showToast('Vehicle removed');
  }
};

// History Data
const portalSessions = ref({ data: [], current_page: 1, last_page: 1, total: 0 });

const fetchSessions = async (page = 1) => {
    try {
        const response = await axios.get('/api/client/sessions', {
          params: { page, search: sessionSearch.value }
        });
        portalSessions.value = response.data;
        sessionPage.value = response.data.current_page;
    } catch (error) {
        console.error('Failed to fetch sessions:', error);
    }
};

const wallet = ref({ balance: 0, currency: 'RM' });

const fetchWallet = async () => {
  try {
    const res = await axios.get('/api/client/wallet');
    wallet.value = { balance: res.data.balance ?? 0, currency: res.data.currency ?? 'RM' };
  } catch (_) {
    wallet.value = { balance: 0, currency: 'RM' };
  }
};

const favorites = computed(() => stations.value.filter(s => s.isFavorite));

// Actions
const showToast = (msg, type = 'success') => {
  toastMsg.value = msg;
  toastType.value = type;
  setTimeout(() => { toastMsg.value = ''; }, 3000);
};

const toggleFavorite = async (station) => {
  try {
    const res = await axios.post('/api/client/stations/favorite', { charge_point_id: station.id });
    station.isFavorite = res.data.isFavorite;
    showToast(res.data.message);
  } catch (err) {
    station.isFavorite = !station.isFavorite; // Optimistic update rollback 
    showToast('Failed to update favorite', 'error');
  }
};

const fetchBilling = async () => {
    try {
        const res = await axios.get('/api/client/billing/summary');
        billingPlans.value = res.data.plans;
        activeSubscription.value = res.data.active_subscription;
    } catch (err) {
        console.error('Failed to fetch billing info:', err);
    }
};

const purchaseSubscription = async (plan) => {
    if (confirm(`Do you want to subscribe to ${plan.name} for RM ${plan.price}?`)) {
        try {
            await axios.post('/api/client/billing/subscribe', { plan_id: plan.id });
            showToast(`Subscribed to ${plan.name}!`);
            fetchBilling();
            fetchWallet();
        } catch (err) {
            showToast(err.response?.data?.message || 'Subscription failed', 'error');
        }
    }
};

const checkPromo = async () => {
    if (!promoCode.value) return;
    try {
        const res = await axios.post('/api/client/billing/promo', { code: promoCode.value });
        appliedPromo.value = res.data.promo;
        showToast('Promo code applied!');
    } catch (err) {
        showToast('Invalid promo code', 'error');
    }
};

const topUp = async (amount) => {
  const num = parseFloat(amount);
  if (!num || num <= 0) {
    showToast('Please enter a valid amount', 'error');
    return;
  }
  
  try {
    const res = await axios.post('/api/client/wallet/top-up', { amount: num });
    showToast(`Top up of RM ${num.toFixed(2)} successful`);
    wallet.value.balance = res.data.balance;
    customTopUp.value = '';
    fetchNotifications();
  } catch (error) {
    showToast('Top up failed. Please try again.', 'error');
  }
};

const reserveStation = (station) => {
  selectedStation.value = station;
  showReserveModal.value = true;
  reserveSlotMinutes.value = 15;
};

const confirmReservation = async () => {
  if (!selectedStation.value) return;
  try {
    const res = await axios.post('/api/client/reservations', {
      charge_point_id: selectedStation.value.id,
      slot_minutes: reserveSlotMinutes.value,
    });
    showToast(res.data?.message || `Reserved for ${reserveSlotMinutes.value} minutes`);
    showReserveModal.value = false;
    selectedStation.value = null;
    fetchNotifications();
  } catch (_) {
    showToast('Reservation failed', 'error');
  }
};

const startChargingMock = async (station) => {
  if (wallet.value.balance < 5) {
     showToast('Insufficient balance. Please top up at least RM 5.00', 'error');
     activeTab.value = 'wallet';
     return;
  }
  
  try {
    const response = await axios.post('/api/client/sessions', {
      charge_point_id: station.id
    });
    
    currentSessionId.value = response.data.id;
    activeSession.value = true;
    lastStationName.value = station.name;
    selectedStation.value = null;
    activeTab.value = 'overview';
    chargingData.value = { kwh: 0, cost: 0, time: '00:00:00', startTime: new Date() };
    startMonitoring();
    showToast('Charging session started');
    fetchSessions(); // Update history
    fetchNotifications();
  } catch (error) {
    console.error('Failed to start session:', error);
    showToast('Failed to start session.', 'error');
  }
};

const stopCharging = async () => {
  stopMonitoring();
  const finalKwh = chargingData.value.kwh;
  const finalCost = chargingData.value.cost;
  
  try {
    await axios.post(`/api/client/sessions/${currentSessionId.value}/stop`, {
      kwh: finalKwh,
      cost: finalCost
    });
    
    activeSession.value = false;
    const oldId = currentSessionId.value;
    currentSessionId.value = null;
    showToast(`Session completed. RM ${finalCost.toFixed(2)} deducted.`);
    fetchSessions();
    fetchWallet();
    fetchNotifications();
    
    // Trigger Rating Modal
    setTimeout(() => {
      showRatingModal.value = true;
      currentRating.value = 0;
      feedbackComment.value = '';
    }, 1000);
  } catch (error) {
    console.error('Failed to stop session:', error);
    showToast('Failed to finalize session.', 'error');
  }
};

const submitFeedback = async () => {
  if (currentRating.value === 0) {
    showToast('Please select a rating', 'error');
    return;
  }
  try {
    const res = await axios.post('/api/client/sessions/feedback', {
      session_id: portalSessions.value?.data?.[0]?.id,
      rating: currentRating.value,
      comment: feedbackComment.value
    });
    showToast(res.data.message);
    showRatingModal.value = false;
    fetchStations(); // Update reviews count
  } catch (err) {
    showToast('Failed to submit feedback', 'error');
  }
};

const setDefaultVehicle = async (vehicle) => {
  try {
    await axios.post(`/api/client/vehicles/${vehicle.id}/default`);
    vehicles.value.forEach(v => { v.is_default = v.id === vehicle.id; });
    showToast(`${vehicle.brand} set as default`);
  } catch (_) {
    showToast('Failed to set default', 'error');
  }
};

const downloadInvoice = async (session) => {
  try {
    const id = session.id;
    const url = `/api/client/sessions/${id}/invoice`;
    const res = await axios.get(url, { responseType: 'blob' });
    
    // axios returns blob directly when responseType is 'blob'
    const blob = res.data;
    const downloadUrl = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = downloadUrl;
    a.download = `receipt-${String(id).padStart(6, '0')}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(downloadUrl);
    
    showToast('Receipt downloaded');
  } catch (err) {
    console.error('Download failed:', err);
    showToast('Download failed. Please try again.', 'error');
  }
};

const showReceipt = (session) => {
  showToast(`Viewing details for session #${session.id}`);
};

// Map logic
function initMap() {
  if (leafletMap) {
    mapLoading.value = false;
    leafletMap.invalidateSize();
    return;
  }
  nextTick(() => {
    if (!mapRef.value) {
      mapLoading.value = false;
      mapError.value = true;
      return;
    }
    import('leaflet').then((L) => {
      L = L.default;
      delete L.Icon.Default.prototype._getIconUrl;
      L.Icon.Default.mergeOptions({
        iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
        iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
      });
      try {
        leafletMap = L.map(mapRef.value).setView([3.1579, 101.7116], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap',
        }).addTo(leafletMap);
        stations.value
          .filter((s) => s.lat != null && s.lng != null)
          .forEach((station) => {
            const marker = L.marker([station.lat, station.lng])
              .addTo(leafletMap)
              .on('click', () => { selectedStation.value = station; });
            marker.bindTooltip(station.name, { permanent: false, direction: 'top' });
            leafletMarkers.push(marker);
          });
        setTimeout(() => { leafletMap.invalidateSize(); }, 100);
        mapLoading.value = false;
      } catch (err) {
        mapError.value = true;
        mapLoading.value = false;
      }
    }).catch((err) => {
      mapError.value = true;
      mapLoading.value = false;
    });
  });
}

watch(activeTab, (tab) => {
  if (tab === 'map') {
    mapLoading.value = true;
    mapError.value = false;
    nextTick(() => {
      setTimeout(initMap, 150);
    });
  }
});

// Watch search to trigger refetch
watch(sessionSearch, () => {
  sessionPage.value = 1;
  fetchSessions(1);
});

onMounted(() => {
    fetchSessions();
    fetchVehicles();
    fetchWallet();
    fetchStations();
    fetchNotifications();
    fetchBilling();

    // Refresh every 30s
    setInterval(() => {
      fetchNotifications();
      if (!activeSession.value) fetchSessions(sessionPage.value);
    }, 30000);
});
</script>

<style scoped>
.animate-in {
  animation: animate-in 0.3s ease-out;
}
@keyframes animate-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Custom scrollbar for better look */
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #e4e4e7;
  border-radius: 10px;
}
.dark ::-webkit-scrollbar-thumb {
  background: #27272a;
}
</style>


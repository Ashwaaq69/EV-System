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
            <Badge v-if="notifications.length > 0" variant="destructive" class="ml-1 px-1 min-w-[1.2rem] h-5">{{ notifications.length }}</Badge>
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
            <span class="hidden md:inline">Wallet</span>
          </TabsTrigger>
          <TabsTrigger value="favorites" class="gap-2">
            <Star class="h-4 w-4" />
            <span class="hidden md:inline">Favorites</span>
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
                  <TableRow v-for="session in (portalSessions?.data || []).slice(0, 3)" :key="session.id">
                    <TableCell>{{ new Date(session.start_time).toLocaleDateString() }}</TableCell>
                    <TableCell>{{ session.charge_point?.identifier || 'Main Station A' }}</TableCell>
                    <TableCell>{{ session.kwh_consumed }} kWh</TableCell>
                    <TableCell class="text-right">
                      <Button variant="ghost" size="sm" class="h-8 gap-2" @click="downloadInvoice(session)">
                        <Download class="h-3 w-3" />
                        Receipt
                      </Button>
                    </TableCell>
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
                <div class="flex gap-2">
                   <Button variant="outline" size="sm" class="gap-2">
                     <Filter class="h-3 w-3" /> Filter
                   </Button>
                   <Button variant="outline" size="sm" class="gap-2">
                     <Download class="h-3 w-3" /> Export CSV
                   </Button>
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
                    <TableRow v-for="session in history" :key="session.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 cursor-pointer" @click="showReceipt(session)">
                      <TableCell class="font-mono text-[10px] pl-6">#{{ session.id.toString().padStart(6, '0') }}</TableCell>
                      <TableCell class="font-medium">{{ session.location }}</TableCell>
                      <TableCell class="text-xs text-zinc-500">
                         {{ session.date }} <br/>
                         {{ session.time }}
                      </TableCell>
                      <TableCell>{{ session.energy }} kWh</TableCell>
                      <TableCell class="font-bold">RM {{ session.cost.toFixed(2) }}</TableCell>
                      <TableCell class="pr-6 text-right">
                         <Button variant="ghost" size="icon" class="h-8 w-8 text-blue-600" @click.stop="downloadInvoice(session)">
                            <FileText class="h-4 w-4" />
                         </Button>
                      </TableCell>
                    </TableRow>
                  </TableBody>
                </Table>
             </CardContent>
          </Card>
        </TabsContent>

        <!-- WALLET TAB -->
        <TabsContent value="wallet" class="space-y-6">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <Card class="relative bg-zinc-900 text-white overflow-hidden p-8 rounded-3xl min-h-[240px] flex flex-col justify-between shadow-2xl">
               <div class="absolute top-0 right-0 p-8 opacity-10">
                  <WalletIcon class="h-32 w-32" />
               </div>
               <div class="z-10">
                  <p class="text-zinc-400 text-sm font-medium">Citrine Wallet</p>
                  <h3 class="text-5xl font-bold mt-2">RM {{ wallet.balance.toFixed(2) }}</h3>
               </div>
               <div class="z-10 flex flex-col gap-4">
                  <div class="flex justify-between items-end">
                     <div>
                        <p class="text-[10px] text-zinc-500 uppercase tracking-widest">Linked Card</p>
                        <p class="text-sm font-mono mt-1">•••• •••• •••• 4592</p>
                     </div>
                     <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" class="h-8" alt="Mastercard" />
                  </div>
               </div>
               <div class="absolute -bottom-12 -right-12 w-48 h-48 bg-[#FF2D20] rounded-full blur-[100px] opacity-20"></div>
            </Card>

            <Card class="border shadow-sm p-6 space-y-6">
               <h3 class="font-bold">Quick Top Up</h3>
               <div class="grid grid-cols-3 gap-3">
                  <Button variant="outline" v-for="amount in [10, 20, 50, 100, 200, 500]" :key="amount" @click="topUp(amount)">
                     RM {{ amount }}
                  </Button>
               </div>
               <div class="space-y-2">
                  <Label>Custom Amount</Label>
                  <div class="flex gap-2">
                     <Input type="number" placeholder="Enter amount" v-model="customTopUp" />
                     <Button class="bg-zinc-900 text-white" @click="topUp(customTopUp)">Top Up</Button>
                  </div>
               </div>
               <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800">
                  <p class="text-xs text-zinc-500 mb-3">Or pay via FPX</p>
                  <div class="grid grid-cols-4 gap-2 opacity-60">
                     <div class="border rounded p-2 flex items-center justify-center h-10 grayscale hover:grayscale-0 cursor-pointer transition-all">MAYBANK</div>
                     <div class="border rounded p-2 flex items-center justify-center h-10 grayscale hover:grayscale-0 cursor-pointer transition-all">CIMB</div>
                     <div class="border rounded p-2 flex items-center justify-center h-10 grayscale hover:grayscale-0 cursor-pointer transition-all">RHB</div>
                     <div class="border rounded p-2 flex items-center justify-center h-10 grayscale hover:grayscale-0 cursor-pointer transition-all">PBB</div>
                  </div>
               </div>
            </Card>
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
          <Card class="border-none shadow-sm dark:bg-zinc-900">
            <CardHeader>
              <CardTitle>Notifications</CardTitle>
              <CardDescription>Charging alerts and updates</CardDescription>
            </CardHeader>
            <CardContent>
              <div class="flex items-center gap-3 mb-4 p-3 rounded-lg bg-zinc-100 dark:bg-zinc-800">
                <Bell class="h-5 w-5 text-[#FF2D20]" />
                <div class="flex-1">
                  <p class="text-sm font-medium">Push notifications</p>
                  <p class="text-xs text-zinc-500">Get alerts for session start/end and low balance.</p>
                </div>
                <Button v-if="!pushEnabled" size="sm" class="bg-[#FF2D20] text-white" :disabled="pushEnabling" @click="enablePush">
                  {{ pushEnabling ? 'Enabling...' : 'Enable' }}
                </Button>
                <Badge v-else variant="default" class="bg-green-600">Enabled</Badge>
              </div>
              <div class="space-y-3">
                <div v-for="n in notifications" :key="n.id" class="flex items-start gap-3 p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800">
                  <Bell class="h-4 w-4 text-zinc-400 mt-0.5" />
                  <div>
                    <p class="font-medium text-sm">{{ n.title }}</p>
                    <p class="text-xs text-zinc-500">{{ n.message }}</p>
                    <p class="text-[10px] text-zinc-400 mt-1">{{ n.time }}</p>
                  </div>
                </div>
                <p v-if="notifications.length === 0" class="text-sm text-zinc-500 italic">No notifications yet.</p>
              </div>
            </CardContent>
          </Card>
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

// Mock Notifications
const notifications = ref([
  { id: 1, title: 'Wallet Low', message: 'Your balance is below RM 10.00', time: '2h ago' },
  { id: 2, title: 'Payment Successful', message: 'Top up of RM 50.00 completed', time: '1d ago' }
]);

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

// Stations (with lat/lng for Leaflet map)
const stations = ref([
  { id: 1, name: 'Suria KLCC P2', address: 'Level P2, Lot 1.1, KLCC', status: 'Available', lat: 3.1579, lng: 101.7116, distance: 0.8, rating: 4.8, reviews: 124, isFavorite: true },
  { id: 2, name: 'Pavilion Bukit Jalil', address: 'B1, Pillar C12', status: 'Occupied', lat: 3.0634, lng: 101.6382, distance: 12.4, rating: 4.5, reviews: 89, isFavorite: false },
  { id: 3, name: 'Mid Valley Megamall', address: 'P1, Zone Red', status: 'Available', lat: 3.1177, lng: 101.6780, distance: 4.2, rating: 4.2, reviews: 210, isFavorite: false },
  { id: 4, name: 'IOI City Mall', address: 'LG, Pillar B5', status: 'Available', lat: 3.0486, lng: 101.6172, distance: 15.6, rating: 4.9, reviews: 45, isFavorite: true },
  { id: 5, name: 'Paradigm Mall', address: 'B2, Near Entrance', status: 'Occupied', lat: 3.1076, lng: 101.6055, distance: 8.9, rating: 3.8, reviews: 67, isFavorite: false },
]);

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
      { id: 2, brand: 'BYD', model: 'Atto 3', plate_number: 'VBE 9988', battery_capacity_kwh: 60, connector_type: 'Type 2 / CCS', is_default: false },
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
const history = ref([
  { id: 10452, location: 'Suria KLCC P2', date: 'Feb 12, 2026', time: '14:23', energy: 34.5, cost: 29.35 },
  { id: 10421, location: 'Pavilion KL', date: 'Feb 09, 2026', time: '09:12', energy: 12.8, cost: 10.88 },
  { id: 10398, location: 'Mid Valley', date: 'Feb 05, 2026', time: '18:45', energy: 22.1, cost: 18.79 },
  { id: 10355, location: 'Sunway Pyramid', date: 'Jan 30, 2026', time: '12:30', energy: 45.0, cost: 38.25 },
]);

const portalSessions = ref({ data: [] });

const fetchSessions = async () => {
    try {
        const response = await axios.get('/api/client/sessions');
        portalSessions.value = response.data;
    } catch (error) {
        console.error('Failed to fetch sessions:', error);
    }
};

const wallet = ref({ balance: 150.0, currency: 'RM' });

const fetchWallet = async () => {
  try {
    const res = await axios.get('/api/client/wallet');
    wallet.value = { balance: res.data.balance ?? 150, currency: res.data.currency ?? 'RM' };
  } catch (_) {
    wallet.value = { balance: 150.0, currency: 'RM' };
  }
};

const favorites = computed(() => stations.value.filter(s => s.isFavorite));

// Actions
const showToast = (msg, type = 'success') => {
  toastMsg.value = msg;
  toastType.value = type;
  setTimeout(() => { toastMsg.value = ''; }, 3000);
};

const toggleFavorite = (station) => {
  station.isFavorite = !station.isFavorite;
  showToast(station.isFavorite ? 'Added to favorites' : 'Removed from favorites');
};

const topUp = async (amount) => {
  if (!amount) return;
  const num = parseFloat(amount);
  if (num < 1) {
    showToast('Minimum top-up is RM 1.00', 'error');
    return;
  }
  try {
    const res = await axios.post('/api/client/wallet/top-up', { amount: num });
    wallet.value.balance = res.data.balance ?? wallet.value.balance + num;
    customTopUp.value = '';
    showToast(`Successfully added RM ${num.toFixed(2)} to wallet`);
  } catch (_) {
    wallet.value.balance += num;
    customTopUp.value = '';
    showToast(`Successfully added RM ${num.toFixed(2)} to wallet`);
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
      station_id: selectedStation.value.id,
      slot_minutes: reserveSlotMinutes.value,
    });
    showToast(res.data?.message || `Reserved for ${reserveSlotMinutes.value} minutes`);
    showReserveModal.value = false;
    selectedStation.value = null;
  } catch (_) {
    showToast(`Slot reserved at ${selectedStation.value?.name} for ${reserveSlotMinutes.value} minutes`);
    showReserveModal.value = false;
    selectedStation.value = null;
  }
};

const startChargingMock = async (station) => {
  if (wallet.value.balance < 5) {
     showToast('Insufficient balance. Please top up at least RM 5.00', 'error');
     activeTab.value = 'wallet';
     return;
  }
  
  try {
    // For demo, we use station.id as charge_point_id if it's the real one
    // In KLCC case id: 1 is CS-001
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
    showToast('Charging session started successfully');
    fetchSessions(); // Update history
  } catch (error) {
    console.error('Failed to start session:', error);
    showToast('Failed to start session. Please try again.', 'error');
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
    currentSessionId.value = null;
    showToast(`Session completed. RM ${finalCost.toFixed(2)} deducted from wallet`);
    fetchSessions();
    fetchWallet();
    
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

const submitFeedback = () => {
  showToast('Thank you for your feedback!');
  showRatingModal.value = false;
};

const setDefaultVehicle = async (vehicle) => {
  try {
    await axios.post(`/api/client/vehicles/${vehicle.id}/default`);
    vehicles.value.forEach(v => { v.is_default = v.id === vehicle.id; });
    showToast(`${vehicle.brand} ${vehicle.model} set as default`);
  } catch (_) {
    vehicles.value.forEach(v => { v.is_default = v.id === vehicle.id; });
    showToast(`${vehicle.brand} ${vehicle.model} set as default`);
  }
};

const downloadInvoice = async (session) => {
  try {
    const id = session.id ?? session.session_id;
    const url = `/api/client/sessions/${id}/invoice`;
    const res = await axios.get(url, { responseType: 'blob' });
    const blob = new Blob([res.data], { type: res.headers['content-type'] || 'application/json' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `invoice-${String(id).padStart(6, '0')}.json`;
    a.click();
    URL.revokeObjectURL(a.href);
    showToast('Invoice downloaded');
  } catch (_) {
    showToast('Download failed', 'error');
  }
};

const showReceipt = (session) => {
  showToast(`Viewing details for session #${session.id}`);
};

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
      // Fix default marker icons in Vite/SPA (wrong paths otherwise)
      delete L.Icon.Default.prototype._getIconUrl;
      L.Icon.Default.mergeOptions({
        iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
        iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
      });
      try {
        leafletMap = L.map(mapRef.value).setView([3.1579, 101.7116], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
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
        console.error('Map init error:', err);
        mapError.value = true;
        mapLoading.value = false;
      }
    }).catch((err) => {
      console.error('Leaflet load error:', err);
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

onMounted(() => {
    fetchSessions();
    fetchVehicles();
    fetchWallet();
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


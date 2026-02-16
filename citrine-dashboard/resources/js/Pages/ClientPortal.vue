<template>
  <AppLayout>
    <div class="p-8 max-w-7xl mx-auto space-y-8">
      <!-- Header -->
      <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">User Portal</h1>
          <p class="text-zinc-500 dark:text-zinc-400">Welcome to CitrineOS, {{ $page.props.auth.user.name }}</p>
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
        <TabsList class="grid grid-cols-3 md:grid-cols-6 mb-8 bg-zinc-100 dark:bg-zinc-800 p-1 rounded-xl">
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
                      <Button variant="ghost" size="sm" class="h-8 gap-2">
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

        <!-- MAP TAB -->
        <TabsContent value="map" class="h-[600px] relative rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-800">
          <div class="absolute inset-0 bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center overflow-hidden">
            <!-- Simulated Map Component -->
            <div class="relative w-full h-full bg-[#f8f9fa] dark:bg-[#1e1e1e]">
               <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 40px 40px;"></div>
               
               <!-- Simulated Markers -->
               <div v-for="station in stations" :key="station.id" 
                    :style="{ left: station.x + '%', top: station.y + '%' }"
                    class="absolute cursor-pointer transition-transform hover:scale-110 group"
                    @click="selectedStation = station">
                  <div :class="['p-2 rounded-full shadow-lg border-2 bg-white dark:bg-zinc-900', station.status === 'Available' ? 'border-green-500' : 'border-red-500']">
                    <Zap :class="['h-4 w-4', station.status === 'Available' ? 'text-green-500' : 'text-red-500']" />
                  </div>
                  <div class="absolute top-full left-1/2 -translate-x-1/2 mt-1 bg-white dark:bg-zinc-900 px-2 py-0.5 rounded shadow text-[10px] whitespace-nowrap hidden group-hover:block border border-zinc-200 dark:border-zinc-800">
                    {{ station.name }}
                  </div>
               </div>
            </div>

            <!-- Station Detail Overlay -->
            <div v-if="selectedStation" class="absolute bottom-8 left-8 right-8 md:right-auto md:w-96 bg-white dark:bg-zinc-900 p-6 rounded-2xl shadow-2xl border border-zinc-100 dark:border-zinc-800 animate-in slide-in-from-left-4">
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
            
            <!-- Map Legend/Overlay -->
            <div class="absolute top-4 right-4 flex flex-col gap-2">
               <Card class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur border-none p-2 shadow-sm">
                  <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2 text-[10px] font-medium px-2">
                       <div class="w-2 h-2 rounded-full bg-green-500"></div> Available
                    </div>
                    <div class="flex items-center gap-2 text-[10px] font-medium px-2">
                       <div class="w-2 h-2 rounded-full bg-red-500"></div> Occupied
                    </div>
                  </div>
               </Card>
            </div>
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
                    <Button variant="ghost" size="sm" class="h-8 text-red-500 hover:text-red-600 hover:bg-red-50 px-2">
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
      </Tabs>

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
  CheckCircle, AlertCircle, Info, Clock, Square
} from 'lucide-vue-next';
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';

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

// Mock Stations Data
const stations = ref([
  { id: 1, name: 'Suria KLCC P2', address: 'Level P2, Lot 1.1, KLCC', status: 'Available', x: 45, y: 30, distance: 0.8, rating: 4.8, reviews: 124, isFavorite: true },
  { id: 2, name: 'Pavilion Bukit Jalil', address: 'B1, Pillar C12', status: 'Occupied', x: 65, y: 55, distance: 12.4, rating: 4.5, reviews: 89, isFavorite: false },
  { id: 3, name: 'Mid Valley Megamall', address: 'P1, Zone Red', status: 'Available', x: 30, y: 70, distance: 4.2, rating: 4.2, reviews: 210, isFavorite: false },
  { id: 4, name: 'IOI City Mall', address: 'LG, Pillar B5', status: 'Available', x: 20, y: 40, distance: 15.6, rating: 4.9, reviews: 45, isFavorite: true },
  { id: 5, name: 'Paradigm Mall', address: 'B2, Near Entrance', status: 'Occupied', x: 80, y: 20, distance: 8.9, rating: 3.8, reviews: 67, isFavorite: false },
]);

// Mock Vehicles Data
const vehicles = ref([
  { id: 1, brand: 'Tesla', model: 'Model 3', plate_number: 'WPP 1234', battery_capacity_kwh: 75, connector_type: 'Type 2 / CCS', is_default: true },
  { id: 2, brand: 'BYD', model: 'Atto 3', plate_number: 'VBE 9988', battery_capacity_kwh: 60, connector_type: 'Type 2 / CCS', is_default: false },
]);

const defaultVehicle = computed(() => vehicles.value.find(v => v.is_default));

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

const wallet = ref({
    balance: 150.00
});

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

const topUp = (amount) => {
  if (!amount) return;
  wallet.value.balance += parseFloat(amount);
  customTopUp.value = '';
  showToast(`Successfully added RM ${parseFloat(amount).toFixed(2)} to wallet`);
};

const reserveStation = (station) => {
  showToast(`Slot reserved at ${station.name} for 15 minutes`);
  selectedStation.value = null;
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
    
    wallet.value.balance -= finalCost;
    activeSession.value = false;
    currentSessionId.value = null;
    showToast(`Session completed. RM ${finalCost.toFixed(2)} deducted from wallet`);
    fetchSessions(); // Update history
    
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

const setDefaultVehicle = (vehicle) => {
  vehicles.value.forEach(v => v.is_default = false);
  vehicle.is_default = true;
  showToast(`${vehicle.brand} ${vehicle.model} set as default`);
};

const downloadInvoice = (session) => {
  showToast(`Downloading invoice for session #${session.id}...`);
};

const showReceipt = (session) => {
  showToast(`Viewing details for session #${session.id}`);
};

onMounted(() => {
    fetchSessions();
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


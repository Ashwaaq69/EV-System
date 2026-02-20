<template>
  <div id="standalone-root">
    <router-view></router-view>
  </div>
</template>

<script setup>
import { provide } from 'vue';

// Detailed Mock Data for Standalone Mode
const mockPage = {
  url: window.location.pathname,
  props: {
    auth: { user: { name: 'Demo Client', email: 'client@citrineos.com', role: 'client' } },
    // Dashboard Mocks
    onlineChargers: 12,
    offlineChargers: 3,
    chargers: {
      data: [
        { id: 1, name: 'Main Station A', location: 'Section 1', online: true },
        { id: 2, name: 'North Hub B', location: 'Section 4', online: true },
        { id: 3, name: 'East Plaza', location: 'Downtown', online: false },
      ],
      current_page: 1,
      last_page: 5,
      per_page: 10,
      total: 45
    },
    // Transaction Mocks
    sessions: {
      data: [
        { id: 101, user: 'John Doe', charger: 'Main Station A', location: 'Section 1', status: 'active', start_time: new Date().toISOString(), kwh: 14.5, cost: 5.80 },
        { id: 102, user: 'Jane Smith', charger: 'North Hub B', location: 'Section 4', status: 'closed', start_time: new Date(Date.now() - 3600000).toISOString(), kwh: 22.1, cost: 8.44 },
      ],
      current_page: 1,
      from: 1,
      to: 2,
      total: 154,
      per_page: 5
    }
  }
};

provide('$page', mockPage);
</script>

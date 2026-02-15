import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './Pages/Dashboard.vue';
import Analytics from './Pages/Analytics.vue';
import Transactions from './Pages/Transactions.vue';
import ClientPortal from './Pages/ClientPortal.vue';

const mockProps = {
  // Shared props
  auth: { user: { name: 'Demo Client', email: 'client@citrineos.com', role: 'client' } },
  // Dashboard props
  onlineChargers: 12,
  offlineChargers: 3,
  chargers: {
    data: [
      { id: 1, name: 'Main Station A', location: 'Section 1', online: true },
      { id: 2, name: 'North Hub B', location: 'Section 4', online: true },
      { id: 3, name: 'East Plaza', location: 'Downtown', online: false },
    ],
    current_page: 1, last_page: 5, per_page: 10, total: 45
  },
  // Transactions props
  sessions: {
    data: [
      { id: 101, user: 'John Doe', charger: 'Main Station A', location: 'Section 1', status: 'active', start_time: new Date().toISOString(), kwh: 14.5, cost: 5.80 },
    ],
    current_page: 1, from: 1, to: 1, total: 154, per_page: 5
  }
};

const routes = [
  { path: '/', component: ClientPortal, props: mockProps },
  { path: '/dashboard', component: Dashboard, props: mockProps },
  { path: '/analytics', component: Analytics, props: mockProps },
  { path: '/transactions', component: Transactions, props: mockProps },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;

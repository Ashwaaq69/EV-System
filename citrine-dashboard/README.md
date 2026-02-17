# 📊 Citrine Dashboard

The **Citrine Dashboard** is a premium administrative interface for the CitrineOS EV Charging System. Built with modern web technologies, it provides real-time insights, telemetry, and management capabilities for your charging network.

## ✨ Features

- **Live Monitoring**: Real-time telemetry stream from charging stations via OCPP.
- **Data Visualization**: Beautiful interactive charts for energy consumption, session duration, and revenue.
- **Geospatial Insights**: Leaflet-based maps showing station locations and real-time status.
- **Session Management**: Full control over active and historical charging sessions.
- **Responsive Design**: Fully optimized for mobile, tablet, and desktop viewing.

## 🛠️ Tech Stack

- **Framework**: [Laravel 11](https://laravel.com)
- **Frontend**: [Vue.js 3](https://vuejs.org) (Composition API)
- **Bridge**: [Inertia.js](https://inertiajs.com)
- **Components**: [Shadcn-vue](https://shadcn-vue.com) & [Lucide Icons](https://lucide.dev)
- **Styling**: [Tailwind CSS](https://tailwindcss.com)
- **Charts**: [Chart.js](https://www.chartjs.org)

## 🚀 Getting Started

### Installation

1. **Install PHP Dependencies**:
   ```bash
   composer install
   ```

2. **Install Node Dependencies**:
   ```bash
   npm install
   ```

3. **Environment Setup**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**:
   Update your `.env` file with your database credentials and run:
   ```bash
   php artisan migrate
   ```

### Running the Application

1. **Start the Vite Dev Server**:
   ```bash
   npm run dev
   ```

2. **Start the Laravel Server**:
   ```bash
   php artisan serve
   ```

## 📂 Project Structure

- `resources/js/Pages`: Vue components representing the dashboard pages.
- `resources/js/Components`: Reusable UI components (Shadcn-vue based).
- `app/Http/Controllers`: Logic for handling dashboard data and routing.
- `routes/web.php`: Defined routes for the dashboard.

---

Part of the [CitrineOS EV-System](../README.md).

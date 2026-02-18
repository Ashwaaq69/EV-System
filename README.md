# ⚡ CitrineOS EV-System

Welcome to the **CitrineOS EV-System**, a powerful, modular, and open-source Electric Vehicle (EV) charging station management system (CSMS). This repository integrates the core OCPP 2.0.1 logic with a modern, high-performance dashboard.

---

## 🏗️ Project Architecture

The system is composed of three primary modules:

| Module | Description | Tech Stack |
| :--- | :--- | :--- |
| **[CitrineOS Core](./citrineos-core)** | The heart of the system. Handles OCPP 2.0.1 message routing, validation, and station management. | Node.js, Fastify, TypeScript, WebSocket |
| **[Citrine Dashboard](./citrine-dashboard)** | A premium administrative dashboard for real-time monitoring and management. | Laravel, Inertia.js, Vue 3, Tailwind CSS |


---

## ✨ Key Features

- **✅ OCPP 2.0.1 Compliant**: Fully supports the latest OCPP standards with dynamic schema validation.
- **📊 Real-time Telemetry**: Live charging session monitoring and hardware communication streams.
- **🗺️ Interactive Maps**: Visualize your charging infrastructure with integrated Leaflet maps.
- **🔌 Modular Design**: Easily extendable architecture with decorators (`@AsHandler`, `@AsMessageEndpoint`).
- **🔐 Advanced Security**: Integrated security features for both station communication and administrative access.
- **🎨 Premium UI/UX**: A modern dashboard built with Shadcn-vue and Chart.js for beautiful data visualization.

---

## 🚀 Quick Start (Docker - Recommended)

The entire systems ecosystem is now fully connected and containerized. You can start all services (Core, Dashboard, Database, and Broker) with a single command.

### 1. Prerequisites
- **Docker Desktop** (with Compose enabled)
- **Node.js** (v22.11+) & **PHP 8.2+** (for local development)
- **Git**

### 2. Launching the System
```bash
# Navigate to the server directory
cd citrineos-core/Server

# Start all services in the background
docker-compose up --build -d
```

### 3. Accessing the System
Once the containers are healthy, you can access the various modules at:

| Service | URL | Description |
| :--- | :--- | :--- |
| **Admin Dashboard** | [http://localhost:8001](http://localhost:8001) | Premium management interface |
| **CitrineOS Core** | [http://localhost:8080](http://localhost:8080) | OCPP 2.0.1 Central System |
| **GraphQL API** | [http://localhost:8090](http://localhost:8090) | High-performance data access |
| **MinIO Console** | [http://localhost:9001](http://localhost:9001) | Object storage management |
| **RabbitMQ** | [http://localhost:15672](http://localhost:15672) | Message broker management |

---

## 🛠️ Infrastructure Overview
The system automatically orchestrates the following integrated services:
- **PostgreSQL (PostGIS)**: Spatial database for charging station tracking.
- **RabbitMQ**: Reliable message brokerage for real-time OCPP streams.
- **MinIO**: S3-compatible object storage for large-scale telemetry and certificates.
- **Hasura (GraphQL)**: Instant GraphQL APIs over the project database.

---

## 🏗️ Manual Installation (For Development)
If you prefer to run services individually without Docker:

#### 1. CitrineOS Core
```bash
cd citrineos-core
npm run install-all
npm run build
# Start the server (requires local RabbitMQ & Postgres)
cd Server
npm run start
```

#### 2. Citrine Dashboard
```bash
cd citrine-dashboard
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
# In a separate terminal
npm run dev
```
---

## 🛠️ Tech Stack & Dependencies

### **Backend (Core)**
- **Framework**: Fastify (Node.js)
- **Database**: PostgreSQL (via Sequelize) / SQLite for local dev.
- **Broker**: RabbitMQ / Kafka
- **Cache**: Redis / In-Memory

### **Frontend (Dashboard)**
- **Framework**: Vue 3 (Composition API)
- **Glue**: Inertia.js
- **Styling**: Tailwind CSS & Shadcn-vue
- **Charts**: Chart.js / Vue-chartjs
- **Maps**: Leaflet

---

## 📖 Documentation
Detailed documentation for each component can be found in their respective directories:
- [CitrineOS Core Docs](./citrineos-core/README.md)
- [Citrine Dashboard Docs](./citrine-dashboard/README.md)
- [Operator UI Docs](./citrineos-operator-ui/README.md)

---

## 🤝 Contributing
We welcome contributions! Please refer to the [Contributing Guide](https://github.com/citrineos/citrineos/blob/main/CONTRIBUTING.md) for more details.

---

## 📄 License
This project is licensed under the **Apache License 2.0**.
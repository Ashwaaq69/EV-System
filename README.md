# ⚡ CitrineOS EV-System

Welcome to the **CitrineOS EV-System**, a powerful, modular, and open-source Electric Vehicle (EV) charging station management system (CSMS). This repository integrates the core OCPP 2.0.1 logic with a modern, high-performance dashboard.

---

## 🏗️ Project Architecture

The system is composed of three primary modules:

| Module | Description | Tech Stack |
| :--- | :--- | :--- |
| **[CitrineOS Core](./citrineos-core)** | The heart of the system. Handles OCPP 2.0.1 message routing, validation, and station management. | Node.js, Fastify, TypeScript, WebSocket |
| **[Citrine Dashboard](./citrine-dashboard)** | A premium administrative dashboard for real-time monitoring and management. | Laravel, Inertia.js, Vue 3, Tailwind CSS |
| **[Operator UI](./citrineos-operator-ui)** | The standard operator interface for fine-grained control and configuration. | Refine, Vue/React, Hasura |

---

## ✨ Key Features

- **✅ OCPP 2.0.1 Compliant**: Fully supports the latest OCPP standards with dynamic schema validation.
- **📊 Real-time Telemetry**: Live charging session monitoring and hardware communication streams.
- **🗺️ Interactive Maps**: Visualize your charging infrastructure with integrated Leaflet maps.
- **🔌 Modular Design**: Easily extendable architecture with decorators (`@AsHandler`, `@AsMessageEndpoint`).
- **🔐 Advanced Security**: Integrated security features for both station communication and administrative access.
- **🎨 Premium UI/UX**: A modern dashboard built with Shadcn-vue and Chart.js for beautiful data visualization.

---

## 🚀 Quick Start

### 1. Prerequisites
- **Node.js** (v22.11+)
- **PHP** (8.2+) & **Composer**
- **Docker** (recommended for core services)

### 2. Setting Up CitrineOS Core
```bash
cd citrineos-core
npm run install-all
npm run build
# Start services (RabbitMQ, Postgres, etc.)
cd Server
docker-compose up -d
# Start the server
npm run start
```

### 3. Setting Up Citrine Dashboard
```bash
cd citrine-dashboard
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
# In a separate terminal
php artisan serve
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
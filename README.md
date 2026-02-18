# ⚡ CitrineOS EV-System (Malaysia Edition)

Welcome to the **CitrineOS EV-System**, a powerful, modular, and open-source Electric Vehicle (EV) charging station management system (CSMS). This repository integrates the core OCPP 2.0.1 logic with a modern, high-performance dashboard localized for the Malaysian market.

---

## 🏗️ Project Architecture

The system is composed of two primary modules working in harmony:

| Module | Description | Tech Stack |
| :--- | :--- | :--- |
| **[CitrineOS Core](./citrineos-core)** | The heart of the system. Handles OCPP 2.0.1 message routing, validation, and station management. | Node.js, Fastify, TypeScript, WebSocket |
| **[Citrine Dashboard](./citrine-dashboard)** | A premium administrative & client portal for real-time monitoring, billing, and management. | Laravel, Inertia.js, Vue 3, Tailwind CSS |

---

## ✨ Key Features

### 🏢 Admin Command Center
- **📈 Advanced Analytics**: Real-time tracking of **Revenue**, **Utilization**, and **Sustainability** (kWh delivered & CO2 tons saved).
- **� User Management**: Full CRUD capabilities for managing Admin and Client roles.
- **📡 Network Monitoring**: Live heartbeat and heatmaps of charging stations.

### � Client Portal & Smart Billing
- **👛 Virtual Wallet**: "Plug & Pay" experience with Quick Top-Up (RM 20, RM 50, RM 100).
- **💎 Subscription Tiers**: Localized plans (Bronze, Silver, Gold) with inclusive energy and Session Discounts.
- **� Dynamic Pricing**: Automated **Peak/Off-Peak** rate switching to balance grid load (20% Off-Peak discounts).
- **🏷️ Promo Codes**: Marketing tools for user acquisition (e.g., `WELCOME10`).
- **🧾 Automated Settlement**: Instant invoice generation with **8% SST** calculation.

### 🛠️ Hardware & Compliance
- **✅ OCPP 2.0.1 Compliant**: Built on the latest industry standards.
- **🗺️ Interactive Maps**: Leaflet-based station discovery with real-time status.

---

## 🇲🇾 Malaysian Localization
This implementation is specifically tailored for the Malaysian EV ecosystem:
- **Currency**: Malaysian Ringgit (RM).
- **Taxation**: Integrated 8% SST (Sales and Service Tax) logic.
- **Pricing**: Smart Peak/Off-Peak logic aligned with Malaysian energy consumption patterns.
- **Units**: Metric system (kWh, Tons of CO2).

---

## 🚀 Quick Start (Docker)

The entire systems ecosystem is fully connected and containerized.

### 1. Prerequisites
- **Docker Desktop** (with Compose enabled)
- **Git**

### 2. Launching the System
```bash
# Start all services (Dashboard, Core, DB, Broker, MinIO)
docker compose up --build -d
```

### 3. Accessing the Modules
Once the containers are healthy:

| Service | URL | Description |
| :--- | :--- | :--- |
| **Admin/Client Portal** | [http://localhost:8001](http://localhost:8001) | Management & User Interface |
| **CitrineOS Core** | [http://localhost:8080](http://localhost:8080) | OCPP Central System |
| **API Health Check** | [http://localhost:8080/health](http://localhost:8080/health) | System Health Monitor |
| **API Docs** | [http://localhost:8080/docs](http://localhost:8080/docs) | Developer Documentation |

---

## 🛠️ Infrastructure
- **PostgreSQL (PostGIS)**: Spatial database for station tracking.
- **RabbitMQ**: Reliable message brokerage for OCPP streams.
- **MinIO**: S3-compatible storage for logs and certificates.
- **Hasura**: Instant GraphQL API endpoint.

---

## 📄 License
This project is licensed under the **Apache License 2.0**.

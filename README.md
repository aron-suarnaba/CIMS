# CIMS | Integrated Network & Infrastructure Management

![Status](https://img.shields.io/badge/Status-Active-success?style=for-the-badge)
![Security](https://img.shields.io/badge/Security-HTTPS_Forced-blue?style=for-the-badge)
![Framework](https://img.shields.io/badge/Framework-Laravel-red?style=for-the-badge)

**CIMS** is a centralized platform designed for real-time inventory network monitoring, automated configuration management, and secure credential storage. It provides network administrators with a "single pane of glass" view into their entire infrastructure.



---

## 🚀 Core Modules

* **📡 Network Monitoring:** Real-time tracking of device uptime, latency, and performance metrics.
* **📦 Inventory Management:** A dynamic database of all network hardware, serial numbers, and physical locations.
* **⚙️ Configuration Management:** Automated backup of device configs and bulk deployment of CLI commands.
* **🔑 Credential Vault:** Secure, encrypted storage for SSH keys, SNMP strings, and administrative passwords.
* **📊 Reporting & Analytics:** Visualized data on network health and resource utilization.

---

## 🛠 Tech Stack

* **Backend:** Laravel (PHP)
* **Frontend:** Tailwind CSS / Vue.js or Alpine.js
* **Database:** PostgreSQL / MySQL
* **Monitoring Protocols:** SNMP, SSH, ICMP

---

## ⚙️ Installation & Setup

### 1. Clone the Repository
```bash
git clone [https://github.com/your-username/cims.git](https://github.com/your-username/cims.git)
cd cims
```

### 2. Install Dependencies
```bash
composer install
npm install && npm run build
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Security Setup
```bash
APP_ENV=production
```

### 5. Run Migrations
```bash
php artisan migrate --seed
```

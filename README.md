# AI Healthcare 🏥✨
**Advanced Healthcare System with Cybernetic Care**

AI Healthcare is a next-generation hospital management system featuring interactive portals, symptom-based AI triaging, blockchain-secured prescription records, and facial biometric verification.

Created by **Kaushik Ghosh**.

---

## 🚀 Key Features

* **Patient, Doctor & Admin Portals**: Dedicated portals for secure interaction, scheduling, and billing.
* **AI Triage System**: Rule-based medical assistant assessing urgency level, symptoms, and routing patients to targeted specialists.
* **Blockchain Prescription Ledger**: Cryptographically signed medical prescription records utilizing SHA-256 block hashing and chain integrity verification (`verifyChainIntegrity`).
* **Server-Side Facial Verification**: Restricts administrative entrypoints utilizing mathematical Euclidean distance calculations against 128-dimensional facial vectors.
* **Security Audit Center**: Real-time event logging (login actions, block generations, triage operations) tracking user agents and client IP addresses.

---

## 🛠️ Tech Stack

* **Backend**: PHP 7.x / 8.x
* **Database**: MySQL (PDO)
* **Frontend**: HTML5, Tailwind CSS, JavaScript, Font Awesome, Google Fonts
* **PDF Invoicing**: TCPDF Library

---

## ⚙️ Setup and Installation

### 1. Requirements
* XAMPP / WAMP / LAMP installed on local system.
* MySQL Server.

### 2. Database Configuration
1. Open XAMPP Control Panel and start **Apache** and **MySQL**.
2. Navigate to `http://localhost/phpmyadmin/`.
3. Create a new database named `myhmsdb`.
4. Import the SQL file located at:
   `C:/xampp/htdocs/Ai Healthcare/Database/myhmsdb.sql`

### 3. Server Deployment
1. Move the `Ai Healthcare` folder to your local server's document root (e.g. `C:/xampp/htdocs/`).
2. Run the application by navigating to:
   `http://localhost/Ai Healthcare/`

---

## 🔑 Default Administrator Credentials

Use the following credentials to access the Admin Panel:

* **Username**: `admin`
* **Password**: `admin123`

---

*Developed with ❤️ by Kaushik Ghosh.*
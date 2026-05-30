# 🍎 BiteBalance — Custom Fitness & Diet Tracker

> A secure, lightweight, and modern full-stack web application for personal nutrition planning, calorie tracking, and body composition management. Built with pure PHP (PDO) and an asynchronous vanilla JavaScript frontend — delivering a reactive, single-page experience without heavy framework overhead.

---

## 📋 Table of Contents

- [Key Features](#-key-features)
- [Tech Stack](#️-tech-stack--architecture)
- [Project Structure](#-project-structure)
- [Installation & Setup](#️-installation--setup)
- [Security Overview](#-security-overview)
- [License](#-license)

---

## 🚀 Key Features

### 👤 User Profile & Metrics Tracking

- **Dynamic Demographics** — Tracks and updates core user statistics including weight (kg), height (cm), age, and personalized account configurations.
- **Asynchronous Synchronization** — Profile changes save immediately via background `fetch` calls, keeping the UI fully in sync without page refreshes.

### 🛡️ Administrative Control Console

- **Role-Based Access Control (RBAC)** — Restricts all administrative actions exclusively to authorized `is_admin = 1` accounts via strict server-side validation.
- **Interactive User Management** — A live user control matrix featuring inline account record edits, real-time asynchronous DOM manipulation, and dynamic admin credential toggles.
- **Safe Archiving** — Uses an isolated archiving routine instead of hard deletes, protecting the structural continuity of user entries, compound dishes, and relational data layers from database constraint failures.

### 🔒 Security & Performance

- **Secure Password Handling** — Enforces `password_hash()` with `PASSWORD_DEFAULT` (Bcrypt) and validates existing hashes securely on updates.
- **Complete Payload Validation** — Strict backend checks prevent input duplication conflicts (usernames/emails) and restrict physical parameters to realistic thresholds.
- **Async Form Pipelines** — Built entirely on `FormData` + `fetch` API pipelines with intuitive responsive error handling via dynamic Bootstrap alerts.

---

## 🛠️ Tech Stack & Architecture

| Layer | Technology |
|---|---|
| **Backend** | PHP 8.x — session management, data sanitization, structured routing |
| **Database** | MySQL / MariaDB via PDO prepared statements (SQL Injection-safe) |
| **Frontend** | Vanilla JavaScript (ES6+), `fetch` API, event listener abstractions |
| **UI Framework** | Bootstrap 5.3.8, custom CSS variables, Bootstrap Icons |

---

## 📂 Project Structure

```
bitebalance/
├── js/
│   ├── dashboardManagement.js   # Async UI handlers for the main dashboard
│   ├── dishManagement.js        # Async UI handlers for meal and dish planning
│   ├── productManagement.js     # Async UI handlers for managing base food ingredients
│   └── userManagement.js        # Async form controllers, listeners & modal mappings
├── .gitignore                   # Git exclusion configurations
├── account.php                  # User Account Dashboard & Metrics Form
├── admin.php                    # Secured Administrator Management Control Panel
├── auth.php                     # Authentication state and global access verification
├── dashboardApi.php             # Async JSON API router for dashboard metrics
├── db.php                       # PDO database connection initialization
├── dishApi.php                  # Async JSON API router for dish/meal operations
├── dishes.php                   # View/management interface for compound dishes
├── fitness_tracker.sql          # Primary relational database schema dump
├── index.php                    # Application entry point / routing landing page
├── login.php                    # User authentication sign-in portal
├── productApi.php               # Async JSON API router for base food products
├── products.php                 # View/management interface for ingredients
├── README.md                    # Repository documentation
├── register.php                 # New account registration page
├── test-session.php             # Development utility for auditing active session states
├── userApi.php                  # Async JSON API router for profile/user administration
└── welcome.php                  # Onboarding / splash page for unauthenticated users
```

---

## ⚙️ Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/Jurajski00/BiteBalance.git
cd bitebalance
```

### 2. Initialize the Database

- Create a local database named `fitness_tracker`.
- Import the relational schema:

- Open `db.php` and configure your local connection credentials:

```php
$host = 'localhost';
$dbname = 'fitness_tracker';
$username = 'your_db_user';
$password = 'your_db_password';
```

### 3. Deploy to a Local Web Server

Move the project to your web root directory:

```bash
# XAMPP / MAMP
cp -r bitebalance/ /Applications/XAMPP/htdocs/

# Linux (Apache)
cp -r bitebalance/ /var/www/html/
```

Start Apache (or Nginx) and navigate to `http://localhost/bitebalance`.

### 4. Set Up the First Admin Account

To access the restricted admin panel (`admin.php`), manually promote a user account in your database:

```sql
UPDATE users SET is_admin = 1 WHERE username = 'your_username';
```

> ⚠️ **Security Note:** Limit admin access to trusted accounts only. The `is_admin` flag is validated server-side on every protected request.

---

## 🔐 Security Overview

BiteBalance is built with security as a first-class concern:

- **SQL Injection Prevention** — All database interactions use PDO prepared statements; raw user input never touches a query string.
- **Password Security** — Passwords are stored as Bcrypt hashes via `password_hash()` / `password_verify()`. Plaintext passwords are never stored or logged.
- **Server-Side Authorization** — RBAC is enforced at the PHP layer on every admin API call, independent of frontend state.
- **Input Validation** — All form submissions are validated for type, range, uniqueness, and format before any database write is performed.
- **Soft Deletes** — Archived records preserve referential integrity across relational tables.

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).

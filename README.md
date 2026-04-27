# 🎓 Internship Result Management System (COMP1044)

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4.svg?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1.svg?logo=mysql&logoColor=white)

A robust, secure, and visually polished web application built to streamline the management of student internship results, assessor assignments, and performance evaluations. Developed as the Group 4 coursework for **COMP1044**.

---

## ✨ Key Features

### 🔒 Core Architecture
* **Role-Based Access Control (RBAC):** Distinct secure dashboards and routing for **Admins** and **Assessors**.
* **Hardened Security:** Password hashing (`password_verify`), parameterized SQL queries (`bind_param`) to prevent SQL injection, and dynamic relative-path session routing.
* **Smart Bypasses:** Active sessions automatically bypass the login screen.

### 🎨 Modern UI/UX
* **Dark / Light Mode:** Fully persistent global theme toggle utilizing CSS variables and Local Storage.
* **Premium Aesthetics:** Responsive "frosted-glass" card layouts, edge-to-edge navigation, and custom Google Fonts (Inter).
* **Toast Notifications:** Custom, animated slide-in/fade-out notifications replacing disruptive browser alerts.
* **Custom Danger Modals:** Sleek, blurred-background confirmation overlays to prevent accidental data deletion.
* **Quality of Life:** Real-time JavaScript table filtering and a password visibility toggle.

### 👑 Admin Capabilities
* **Bulk CSV Import:** Upload hundreds of students instantly via `.csv` file parsing.
* **User Management:** Full CRUD (Create, Read, Update, Delete) capabilities for Student and Assessor records.
* **Internship Assignment:** Link students to specific companies and assign them to registered assessors.

### 📝 Assessor Capabilities
* **Personalized Dashboard:** View only assigned students with quick-glance status badges (Pending vs. Graded).
* **Evaluation Engine:** Input categorized marks with strict numerical validation to ensure data integrity.

---

## 🛠️ Technology Stack

* **Frontend:** HTML5, CSS3 (CSS Variables, Keyframe Animations), Vanilla JavaScript (ES6+).
* **Backend:** PHP (Procedural & Object-Oriented MySQLi).
* **Database:** MySQL.
* **No External Libraries:** All CSS and JS, including modals and animations, were written entirely from scratch to demonstrate fundamental web development mastery.

---

## 🚀 Installation & Setup

To run this application locally for grading or development, please follow these steps:

### 1. Prerequisites
Ensure you have a local server environment installed, such as **XAMPP**, **WAMP**, or **MAMP**.

### 2. Database Configuration
1. Open **phpMyAdmin** (usually `http://localhost/phpmyadmin`).
2. Create a new database named `comp1044` (or your preferred name).
3. Import the provided SQL structure and dummy data by uploading the **`comp1044.sql`** file found in the root directory.

### 3. File Setup
1. Clone this repository or extract the ZIP file into your server's root directory (e.g., `C:\xampp\htdocs\COMP1044_CW_G4`).
2. Open `includes/db_connect.php` and verify the database credentials match your local setup:
   ```php
   $servername = "localhost";
   $username = "root";       // Change if your local server uses a different user
   $password = "";           // Change if your local server has a password
   $dbname = "comp1044";     // Ensure this matches the DB you created
   ```

### 4. Access the Application
Open your web browser and navigate to `http://localhost/COMP1044_CW_G4` (adjust the URL if you renamed the folder during extraction).

---

## 🔑 Test Credentials

To test the system without needing to register new users, you can log in using the dummy data included in the SQL file:

### Administrator Account
```text
Username: admin1
Password: password123
```

### Assessor Accounts
```text
Username: assessor1   (or assessor2)
Password: password123
```

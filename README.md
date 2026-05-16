# 🐾 PetHaven - Pet Adoption System

A clean and professional pet adoption platform designed for students (BCA Science Project).

---

## 🔑 Admin Credentials
- **Username:** `admin`
- **Password:** `admin123`
*(Note: Use these credentials to access the Admin Panel at `/admin/dashboard.php`)*

---

## 🗄️ How to Check Your Database (Step-by-Step)

Follow these steps to view and verify your database using **XAMPP + phpMyAdmin**.

### ✅ Step 1: Start XAMPP
1. Open the **XAMPP Control Panel** (search for it in the Start menu).
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.
4. Both rows should turn **green** — this means they are running.

> ⚠️ If MySQL fails to start, check if port 3306 is in use by another program (like MySQL Workbench).

---

### ✅ Step 2: Open phpMyAdmin
1. Open your web browser (Chrome, Firefox, etc.).
2. Go to this URL:
   ```
   http://localhost/phpmyadmin
   ```
3. You will see the **phpMyAdmin dashboard** — no password is needed for XAMPP by default.

---

### ✅ Step 3: Find Your Database
1. In the **left panel**, look for a database named:
   ```
   pet_adoption_system
   ```
2. Click on it to expand it.
3. You should see these **4 tables**:
   | Table Name         | What It Stores                        |
   |--------------------|---------------------------------------|
   | `users`            | Registered users & admin accounts     |
   | `categories`       | Pet types (Dogs, Cats, Birds, etc.)   |
   | `pets`             | All pet listings with details & price |
   | `adoption_requests`| User adoption requests and status     |

> ❌ **If you DON'T see `pet_adoption_system`**, follow the **Setup Instructions** below first.

---

### ✅ Step 4: View Table Data
1. Click on any table name (e.g., `pets`) in the left panel.
2. Click the **Browse** tab at the top.
3. You will see all the rows of data stored in that table.

**Quick checks to verify everything is working:**
- `users` table → Should have at least one row with username = `admin`
- `pets` table → Should have 12 pets listed (Max, Bella, Luna, Rio, etc.)
- `categories` table → Should have 4 rows: Dogs, Cats, Birds, Rabbits
- `adoption_requests` table → Will be empty until a user submits a request

---

### ✅ Step 5: Run a SQL Query (Optional / Advanced)
1. Click on `pet_adoption_system` in the left panel.
2. Click the **SQL** tab at the top.
3. Type any query and press **Go**. Examples:

   **View all pets:**
   ```sql
   SELECT * FROM pets;
   ```

   **View all users:**
   ```sql
   SELECT username, email, role FROM users;
   ```

   **View pending adoption requests:**
   ```sql
   SELECT * FROM adoption_requests WHERE status = 'pending';
   ```

   **Count how many pets are available:**
   ```sql
   SELECT COUNT(*) AS total_available FROM pets WHERE status = 'available';
   ```

---

### ✅ Step 6: Check the DB Connection File
The database connection is configured in:
```
includes/db.php
```

Current settings:
| Setting       | Value                  |
|---------------|------------------------|
| Host          | `127.0.0.1`            |
| Database Name | `pet_adoption_system`  |
| Username      | `root`                 |
| Password      | *(empty by default)*   |

> ✅ These are the default XAMPP settings. No changes needed unless you customized your MySQL.

---

## 🚀 Setup Instructions (First-Time / Database Not Found)

If the database doesn't exist yet:

1. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
2. Click **New** (top of left panel) to create a new database.
3. Name it exactly: `pet_adoption_system` → Click **Create**.
4. Click the **Import** tab at the top.
5. Click **Choose File** → Navigate to your project folder:
   ```
   d:\DIKSHA\xamp\htdocs\PET_ADOPTION\database\pet_adoption.sql
   ```
6. Click **Go** (scroll down to find the button).
7. ✅ You should see a green success message. All 4 tables and sample data will be imported.

---

## 🌐 Accessing the Website
Once XAMPP is running and the database is set up:
```
http://localhost/PET_ADOPTION/
```

- **Home/Browse Pets:** `http://localhost/PET_ADOPTION/index.php`
- **Login:** `http://localhost/PET_ADOPTION/login.php`
- **Register:** `http://localhost/PET_ADOPTION/register.php`
- **Admin Panel:** `http://localhost/PET_ADOPTION/admin/dashboard.php`

---

## 📝 Software Requirement Specification (SRS)

### 1. Introduction
**PetHaven** is a web-based platform that facilitates the adoption of rescued pets. It bridges the gap between animal shelters and individuals looking for a companion.

### 2. Objectives
- To provide a platform for shelters to list available pets.
- To allow users to browse, search, and filter pets easily.
- To manage the adoption process through a secure request system.

### 3. Functional Requirements
#### A. User Module
- **Registration & Login**: Secure user authentication.
- **Browse Pets**: View pets categorized by Dogs, Cats, Birds, etc.
- **Search & Filter**: Search pets by name/breed and filter by category.
- **Adoption Request**: Logged-in users can submit requests for specific pets.
- **Profile Management**: Users can track the status of their requests.

#### B. Admin Module
- **Dashboard**: Real-time stats of pets, users, and requests.
- **Pet Management**: Add, update, or delete pet records.
- **Request Management**: Approve or Reject user adoption requests.
- **Category Management**: Organize pets into relevant categories.

### 4. Non-Functional Requirements
- **Security**: Password hashing using Bcrypt and protection against SQL injection via PDO.
- **Performance**: Fastest loading times with optimized PHP code.
- **Usability**: Responsive UI for both mobile and desktop views.

### 5. Database Design (MySQL)
- **Users**: Stores user credentials and roles (Admin/User).
- **Categories**: Stores pet types (Dogs, Cats, etc.).
- **Pets**: Stores detailed pet information (age, breed, gender, image, price).
- **Adoption Requests**: Stores links between users, pets, and their status.

---

## 📂 Project Structure
```
PET_ADOPTION/
├── admin/              → Management tools for administrators
├── assets/             → CSS styles and pet images
├── database/           → SQL file to setup the database (pet_adoption.sql)
├── includes/           → Shared components (Header, Footer, DB connection)
├── index.php           → Main homepage
├── login.php           → User login page
├── register.php        → User registration page
├── pet_details.php     → Detailed pet information page
├── profile.php         → User's adoption request history
└── logout.php          → Session logout
```

---

**Prepared for BCA Science Project Presentation** 🎓

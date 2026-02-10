# 🐾 PetHaven - Pet Adoption System

A comprehensive web-based pet adoption platform that connects rescued pets with loving families. PetHaven provides an intuitive interface for browsing available pets, submitting adoption requests, and managing the adoption process.

---

## 📋 Table of Contents

- [Project Overview](#project-overview)
- [Synopsis](#synopsis)
- [Key Features](#key-features)
- [Technologies Used](#technologies-used)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Installation & Setup](#installation--setup)
- [Usage](#usage)
- [User Roles](#user-roles)
- [File Descriptions](#file-descriptions)
- [API Endpoints](#api-endpoints)
- [Contributing](#contributing)
- [License](#license)

---

## 📖 Project Overview

**PetHaven** is a full-featured pet adoption management system designed to:
- Help rescue organizations and shelters manage their pet inventory
- Enable users to browse and search for available pets
- Facilitate adoption requests and track their status
- Provide admin functionality for managing pets, users, and adoption requests
- Create a seamless user experience for both adopters and administrators

The platform focuses on giving rescued pets a second chance at happiness by connecting them with suitable families in the community.

---

## 🎯 Synopsis

PetHaven is a web application built with PHP and MySQL that streamlines the pet adoption process. Users can register, browse pets across multiple categories (Dogs, Cats, Birds, Rabbits), and submit adoption requests with personalized messages. Administrators can manage the pet database, view adoption requests, approve or reject requests, and track system statistics.

The system emphasizes:
- **User-Friendly Interface**: Clean, modern design using custom CSS
- **Security**: Password hashing with PHP's PASSWORD_DEFAULT algorithm
- **Database Integrity**: Foreign keys and proper relational design
- **Scalability**: Modular code structure for easy maintenance and expansion

---

## ✨ Key Features

### For Users
- **User Authentication**
  - Secure registration with email and password
  - Login/logout functionality
  - Session management
  
- **Pet Browsing**
  - Browse all available pets
  - Filter by category (Dogs, Cats, Birds, Rabbits)
  - Search pets by name or breed
  - View detailed pet information including age, gender, color, behavior, and care patterns

- **Adoption Requests**
  - Submit adoption requests with personalized messages
  - Track adoption request status (pending, approved, rejected)
  - View request history in user profile

- **User Profile**
  - View all submitted adoption requests
  - Track adoption status
  - Monitor request history

### For Administrators
- **Admin Dashboard**
  - Overview statistics (total pets, users, pending requests)
  - View all adoption requests with user details
  - Approve or reject adoption requests
  - Update request statuses

- **Pet Management**
  - Add new pets to the system
  - View complete pet list
  - Manage pet categories
  - Update pet data and availability status
  - Track rescued vs. purchased pets

- **User Management**
  - Monitor registered users
  - View user details and activity

---

## 🛠️ Technologies Used

### Backend
- **PHP 7.x/8.x** - Server-side scripting
- **MySQL** - Relational database management

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Responsive styling with custom design system
- **JavaScript** - Client-side interactivity
- **Font Awesome 6.0** - Icon library
- **Google Fonts** - Typography (Outfit, Inter)

### Development Tools
- **PDO (PHP Data Objects)** - Database abstraction layer
- **Sessions** - User authentication and state management
- **AJAX** - For dynamic content loading (optional enhancement)

### Server Requirements
- **Apache/Nginx** - Web server
- **PHP 7.0+** - Server-side runtime
- **MySQL 5.7+** - Database server
- **XAMPP/Wamp/Lamp** - Development environment

---

## 📁 Project Structure

```
PET_ADOPTION/
├── index.php                  # Homepage with featured pets
├── pets.php                   # Pet listing page with search/filter
├── pet_details.php           # Individual pet details page
├── register.php              # User registration page
├── login.php                 # User login page
├── logout.php                # Logout handler
├── profile.php               # User profile with adoption requests
├── check_images.php          # Image validation/check utility
├── update_pets_data.php      # Pet data update handler
├── update_categories.php     # Category management handler
├── pets_dump.txt             # Sample pet data dump
│
├── admin/
│   ├── dashboard.php         # Admin dashboard with stats & requests
│   ├── add_pet.php           # Add new pet form
│   ├── pets_list.php         # View all pets list
│
├── includes/
│   ├── db.php                # Database connection (PDO)
│   ├── header.php            # Header/navigation component
│   ├── footer.php            # Footer component
│
├── database/
│   ├── setup.sql             # Database schema and initial data
│   ├── run_seed.php          # Database seeding script
│
├── assets/
│   ├── css/
│   │   └── style.css         # Main stylesheet with design system
│   └── images/
│       ├── category-dog.png
│       ├── category-cat.png
│       ├── category-bird.png
│       └── category-rabbit.png
│
└── README.md                 # This file
```

---

## 🗄️ Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Categories Table
```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    image VARCHAR(255)
);
```

### Pets Table
```sql
CREATE TABLE pets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category_id INT,
    breed VARCHAR(100),
    age VARCHAR(20),
    gender ENUM('Male', 'Female'),
    color VARCHAR(50),
    price DECIMAL(10, 2) DEFAULT 0.00,
    description TEXT,
    behavior TEXT,
    care_pattern TEXT,
    is_rescued BOOLEAN DEFAULT FALSE,
    image VARCHAR(255),
    status ENUM('available', 'adopted') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

### Adoption Requests Table
```sql
CREATE TABLE adoption_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    pet_id INT,
    message TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (pet_id) REFERENCES pets(id)
);
```

---

## 🚀 Installation & Setup

### Prerequisites
- XAMPP, WAMP, or LAMP stack installed
- MySQL server running
- PHP 7.0 or higher
- Web browser (Chrome, Firefox, Safari, Edge)

### Step 1: Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `pet_adoption_system`
3. Import the database schema:
   - Execute `database/setup.sql` to create all tables
   - This will also insert default pet categories

### Step 2: Project Installation
1. Clone/download the project to your web root:
   ```bash
   # For XAMPP
   cp -r PET_ADOPTION C:\xampp\htdocs\
   
   # For WAMP
   cp -r PET_ADOPTION C:\wamp\www\
   ```

2. Verify the database connection in `includes/db.php`:
   ```php
   $host = 'localhost';
   $db_name = 'pet_adoption_system';
   $username = 'root';
   $password = '';
   ```

### Step 3: Start the Application
1. Start Apache and MySQL services (XAMPP control panel)
2. Navigate to: `http://localhost/PET_ADOPTION`
3. Create an admin account or use default credentials

### Step 4: Admin Account Setup
1. Register a new user account
2. Update the user role to 'admin' in the database:
   ```sql
   UPDATE users SET role = 'admin' WHERE username = 'your_username';
   ```

---

## 💻 Usage

### For Regular Users

1. **Create Account**
   - Click "Register" on the homepage
   - Enter username, email, and password
   - Verify unique credentials and submit

2. **Browse Pets**
   - Navigate to "Adopt Now" or browse the catalog
   - Use filters to narrow by category (Dogs, Cats, Birds, Rabbits)
   - Search by pet name or breed

3. **View Pet Details**
   - Click on any pet card to see full details
   - Review behavioral traits, care requirements, age, gender, color
   - Read rescue status and pricing information

4. **Submit Adoption Request**
   - Click "Request to Adopt" on pet details page
   - Log in if not already signed in
   - Fill in adoption message
   - Submit request for admin review

5. **Track Request Status**
   - Visit your profile to see all adoption requests
   - Monitor status: Pending → Approved/Rejected
   - Stay updated on the adoption process

### For Administrators

1. **Access Admin Dashboard**
   - Log in with admin account
   - Navigate to admin dashboard
   - View system statistics

2. **Manage Adoption Requests**
   - Review pending adoption requests
   - View user information and messages
   - Approve or reject requests
   - Update request status

3. **Manage Pets**
   - Add new pets via "Add Pet" form
   - Update existing pet information
   - Mark pets as adopted
   - Manage pet categories

4. **Monitor System**
   - Track total pets in system
   - Monitor user registrations
   - View adoption analytics

---

## 👥 User Roles

### Regular User
- Browse and search available pets
- Submit adoption requests
- Track adoption request status
- Manage personal profile
- View adoption history

### Admin User
- Full CRUD operations on pets
- Full CRUD operations on categories
- Manage adoption requests (approve/reject)
- View system statistics
- Manage user accounts (view details)
- Access admin dashboard

---

## 📄 File Descriptions

| File | Purpose |
|------|---------|
| `index.php` | Homepage with hero section, featured pets, and categories |
| `pets.php` | Pet listing page with advanced search and filtering |
| `pet_details.php` | Detailed pet information and adoption request form |
| `register.php` | User registration with validation |
| `login.php` | User authentication with session management |
| `logout.php` | Session termination handler |
| `profile.php` | User profile showing adoption request history |
| `admin/dashboard.php` | Admin overview with statistics and request management |
| `admin/add_pet.php` | Pet addition form for administrators |
| `admin/pets_list.php` | Display all pets in database for management |
| `includes/db.php` | PDO database connection configuration |
| `includes/header.php` | Navigation header component |
| `includes/footer.php` | Footer component |
| `database/setup.sql` | Database schema and initialization |
| `assets/css/style.css` | Complete styling with CSS variables and responsive design |

---

## 🔌 API Endpoints

### Public Endpoints
```
GET     /index.php              - Homepage
GET     /pets.php              - Browse pets (with ?category=id, ?search=query)
GET     /pet_details.php?id=X  - Pet details
GET     /register.php          - Registration page
POST    /register.php          - Register user
GET     /login.php             - Login page
POST    /login.php             - Authenticate user
GET     /logout.php            - Logout user
```

### Protected Endpoints (Authentication Required)
```
GET     /profile.php           - User adoption requests
GET     /pet_details.php?id=X  - Pet details (with adoption form)
POST    /pet_details.php       - Submit adoption request
```

### Admin Endpoints (Admin Only)
```
GET     /admin/dashboard.php       - Admin dashboard
POST    /admin/dashboard.php       - Update adoption request status
GET     /admin/add_pet.php         - Add pet form
POST    /admin/add_pet.php         - Create new pet
GET     /admin/pets_list.php       - View all pets
```

---

## 🔒 Security Features

- **Password Hashing**: Uses `PASSWORD_DEFAULT` with bcrypt algorithm
- **SQL Injection Prevention**: PDO prepared statements throughout
- **XSS Protection**: `htmlspecialchars()` for output escaping
- **Session Management**: Secure session-based authentication
- **Role-Based Access Control**: Admin-only endpoints protected
- **CSRF Protection**: Form tokens can be implemented
- **Input Validation**: Trimming and type checking on all inputs

---

## 🎨 Design System

The application uses a modern design system with:
- **Color Palette**: Primary (#6C5CE7), text (#333), backgrounds (#f8f9fa)
- **Typography**: Outfit (headings), Inter (body text)
- **Spacing**: Consistent padding and margins using CSS variables
- **Shadows**: Subtle box shadows for depth
- **Border Radius**: 4px, 8px, 12px, 16px (configurable)
- **Responsive Design**: Mobile-first approach with breakpoints at 768px, 1024px, 1440px

---

## 🐛 Troubleshooting

### Database Connection Error
- Ensure MySQL server is running
- Verify credentials in `includes/db.php`
- Check database name: `pet_adoption_system`
- Import `database/setup.sql` if tables don't exist

### Session/Login Issues
- Ensure `session_start()` is called in files requiring authentication
- Check browser cookies are enabled
- Clear browser cache and cookies
- Verify PHP session path is writable

### 404 Errors
- Verify project directory path in URL
- Check file names are correct (case-sensitive on Linux)
- Ensure Apache rewrite module is enabled if using pretty URLs

### Admin Dashboard Not Accessible
- Verify user role is set to 'admin' in database
- Check session is properly maintained
- Clear cookies and log in again

---

## 🚀 Future Enhancements

- Email notifications for adoption status updates
- Payment processing integration
- Advanced user reviews and ratings
- Pet medical history tracking
- Adoption success stories/gallery
- Mobile application
- API for third-party integrations
- Advanced analytics dashboard
- Social media sharing
- Video pet profiles

---

## 📞 Support & Contact

For issues or questions regarding the Pet Adoption System:
- Review the troubleshooting section above
- Check database setup with phpMyAdmin
- Verify all files are properly uploaded
- Ensure PHP version compatibility

---

## 📄 License

This project is provided as-is for educational and non-commercial purposes. Please modify as needed for your specific use case.

---

## 👨‍💻 Version

**Version**: 1.0.0  
**Last Updated**: February 2026  
**Status**: Production Ready

---

## 🎯 Key Metrics

- **Database Tables**: 4 (Users, Categories, Pets, Adoption Requests)
- **User Roles**: 2 (User, Admin)
- **Pet Categories**: 4 (Dogs, Cats, Birds, Rabbits)
- **Responsive Breakpoints**: 3 (Mobile, Tablet, Desktop)
- **Security Features**: 5+ (Hashing, PDO, XSS Protection, Session Management, RBAC)

---

**Built with ❤️ for giving rescued pets a loving home**
-------------------------------------------------------------------------
ADMIN LOGIN:
username: admin
password: admin123

-- PetHaven Database Dump
-- Compatible with MySQL/MariaDB

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS adoption_requests;
DROP TABLE IF EXISTS pets;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin user (password: admin123)
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@pethaven.com', '$2y$10$8.D.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0', 'admin');

-- 2. Categories Table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    image VARCHAR(255)
);

INSERT INTO categories (id, name, image) VALUES 
(1, 'Dogs', 'assets/images/cat_dog.jpg'),
(2, 'Cats', 'assets/images/cat_cat.jpg'),
(3, 'Birds', 'assets/images/cat_bird.jpg'),
(4, 'Rabbits', 'assets/images/cat_rabbit.jpg'),
(5, 'Hamsters', 'assets/images/cat_hamster.jpg');

-- 3. Pets Table
CREATE TABLE pets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    breed VARCHAR(100),
    age VARCHAR(20),
    gender ENUM('Male', 'Female'),
    color VARCHAR(50),
    price DECIMAL(10, 2) DEFAULT 0.00,
    description TEXT,
    behavior TEXT DEFAULT 'Friendly',
    care_pattern TEXT DEFAULT 'Normal',
    is_rescued BOOLEAN DEFAULT FALSE,
    image VARCHAR(255),
    status ENUM('available', 'adopted') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

INSERT INTO pets (name, category_id, breed, age, gender, color, price, description, is_rescued, image) VALUES 
('Bella', 1, 'Golden Retriever', '2 years', 'Female', 'Gold', 0, 'Friendly Golden.', 1, 'assets/images/pet_bella.jpg'),
('Max', 1, 'German Shepherd', '3 years', 'Male', 'Black/Tan', 8000, 'Loyal German Shepherd.', 1, 'assets/images/pet_max.jpg'),
('Oliver', 2, 'Siamese', '1 year', 'Male', 'Cream', 2000, 'Vocal Siamese.', 1, 'assets/images/pet_oliver.jpg'),
('Tweety', 3, 'Canary', '2 years', 'Female', 'Yellow', 800, 'Yellow Canary.', 0, 'assets/images/pet_tweety.jpg'),
('BunBun', 4, 'Rex', '8 months', 'Male', 'Brown', 1000, 'Rex Rabbit.', 1, 'assets/images/pet_bunbun.jpg');

-- 4. Adoption Requests Table
CREATE TABLE adoption_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    pet_id INT,
    message TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE
);

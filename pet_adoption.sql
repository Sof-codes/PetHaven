-- PetHaven Master Database Setup (Simplified Version)
-- This script creates the database without unnecessary technical numeric IDs.

-- CREATE DATABASE IF NOT EXISTS pet_adoption_system;
-- USE pet_adoption_system;

-- 1. Users Table (Using username as PK)
CREATE TABLE IF NOT EXISTS users (
    username VARCHAR(50) PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Categories Table (Using name as PK)
CREATE TABLE IF NOT EXISTS categories (
    name VARCHAR(50) PRIMARY KEY,
    image VARCHAR(255)
);

-- 3. Pets Table (Using name as PK)
CREATE TABLE IF NOT EXISTS pets (
    name VARCHAR(100) PRIMARY KEY,
    category_name VARCHAR(50),
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
    FOREIGN KEY (category_name) REFERENCES categories(name)
);

-- 4. Adoption Requests Table (Simplified)
CREATE TABLE IF NOT EXISTS adoption_requests (
    user_username VARCHAR(50),
    pet_name VARCHAR(100),
    message TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_username, pet_name),
    FOREIGN KEY (user_username) REFERENCES users(username),
    FOREIGN KEY (pet_name) REFERENCES pets(name)
);

-- 5. Initial Data
INSERT IGNORE INTO categories (name, image) VALUES 
('Dogs', 'assets/images/pet_max.jpg'), 
('Cats', 'assets/images/cat_cats.jpg'), 
('Birds', 'assets/images/pet_rio.jpg'), 
('Rabbits', 'assets/images/cat_rabbit.jpg');

-- Default Admin Account (Password: admin123)
INSERT IGNORE INTO users (username, email, password, role) VALUES 
('admin', 'admin@pethaven.com', '$2y$10$jmLRr/zOsci8I7T/WHU5G.51O/WPECOmQbkfhtZ0usiCTmsHlTjsq', 'admin');

-- Sample Pets
INSERT IGNORE INTO pets (name, category_name, breed, age, gender, color, price, description, behavior, care_pattern, is_rescued, image, status) VALUES 
('Max', 'Dogs', 'Golden Retriever', '2 Years', 'Male', 'Golden', 850.00, 'Max is a refined, gentle giant. Found in a suburban park, he is incredibly loyal.', 'Great with kids, loves water and fetch.', 'Needs weekly brushing and daily walks.', TRUE, 'assets/images/pet_max.jpg', 'available'),
('Bella', 'Dogs', 'Labrador', '1 Year', 'Female', 'Black', 750.00, 'Bella is a bundle of energy. She loves long hikes and is very affectionate.', 'Extremely energetic and food-motivated.', 'Thrives on mental stimulation.', TRUE, 'assets/images/pet_bella.jpg', 'available'),
('Oliver', 'Dogs', 'Beagle', '3 Years', 'Male', 'Tri-color', 650.00, 'Oliver is a soulful explorer with a nose that never stops.', 'Very vocal and incredibly friendly.', 'Needs a secure fence.', FALSE, 'assets/images/pet_oliver_new.png', 'available'),
('Buster', 'Dogs', 'Beagle Mix', '4 Years', 'Male', 'White & Brown', 600.00, 'Buster is a calm and laid-back companion. He is a Beagle mix who enjoys the finer things in life, like sunny spots on the rug and occasional treats.', 'Low to moderate energy. He is perfectly content lounging around but enjoys short strolls in the park.', 'Easy-going and perfect for apartment living or first-time owners.', TRUE, 'assets/images/pet_buster.jpg', 'available'),
('Luna', 'Cats', 'Persian', '2 Years', 'Female', 'White', 1800.00, 'Luna is a regal beauty with a quiet and graceful presence.', 'Independent and prefers a calm home.', 'Requires daily grooming.', FALSE, 'assets/images/pet_luna_new.png', 'available'),
('Milo', 'Cats', 'Siamese', '2 Years', 'Male', 'Cream', 1100.00, 'Milo is an intelligent and talkative cat who follows you everywhere.', 'Very vocal and social.', 'Needs interactive toys.', TRUE, 'assets/images/pet_milo_new.png', 'available'),
('Whiskers', 'Cats', 'Maine Coon', '4 Years', 'Male', 'Grey Tabby', 1250.00, 'Whiskers is a "Gentle Giant." As a Maine Coon, he is much larger than the average house cat but has the sweetest temperament you could imagine.', 'Very relaxed and almost dog-like. He enjoys playing in water and is incredibly patient with other pets.', 'Due to his size and thick fur, he needs a large litter box and regular grooming.', TRUE, 'assets/images/pet_whiskers.jpg', 'available'),
('Rio', 'Birds', 'Macaw', '5 Years', 'Male', 'Red & Blue', 4500.00, 'Rio is a smart bird with a vocabulary of over 20 words.', 'Highly social and loud.', 'Needs a very large cage.', TRUE, 'assets/images/pet_rio.jpg', 'available'),
('Tweety', 'Birds', 'Yellow Canary', '1 Year', 'Female', 'Bright Yellow', 450.00, 'Tweety is a cheerful bird that brings music to any home.', 'Independent but loves to sing.', 'Requires a draft-free spot.', FALSE, 'assets/images/pet_tweety_new.png', 'available'),
('Charlie', 'Birds', 'Cockatiel', '2 Years', 'Male', 'Grey & Yellow', 600.00, 'Charlie is a friendly Cockatiel who loves to whistle movie themes. He is very social and enjoys sitting on shoulders.', 'Affectionate and curious. He loves head scratches and interacting with his human family.', 'Needs regular social time and a variety of shredding toys.', TRUE, 'assets/images/pet_charlie_new.png', 'available'),
('Bun Bun', 'Rabbits', 'Holland Lop', '8 Months', 'Female', 'Brown', 550.00, 'Bun Bun is tiny with adorable floppy ears. Loves being hand-fed greens.', 'Docile and very sweet.', 'Needs a spacious pen.', TRUE, 'assets/images/pet_bunbun.jpg', 'available'),
('Snowball', 'Rabbits', 'English Spot', '1 Year', 'Male', 'White with Black Spots', 500.00, 'Snowball is an energetic and athletic English Spot rabbit. He is known for his unique markings and his love for "binking" (happy hops).', 'Very active and fast. He loves to zoom around and explore every corner of his environment.', 'Requires plenty of floor time for exercise and chew-safe toys for dental health.', FALSE, 'assets/images/pet_snowball_new.png', 'available');

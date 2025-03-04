-- DATABASE
CREATE DATABASE pain_go;
USE pain_go;

-- USERS table
CREATE TABLE users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP
);

-- CATEGORIES table
CREATE TABLE categories (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    codecat CHAR(5) NOT NULL,
    libcat VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP
);

-- PRODUCTS table
CREATE TABLE products (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    price INT NOT NULL CHECK (price >= 5 AND price <= 5000),
    quantity INT NOT NULL CHECK (quantity >= 0),
    product_image text NOT NULL,
    category_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- TABLE COMMANDS (pour stocker une commande)
CREATE TABLE commands (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    status ENUM('en attente', 'en préparation', 'livré', 'annulé') DEFAULT 'en attente',
    total_price INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- TABLE COMMAND_ITEMS (pour stocker les produits d'une commande)
CREATE TABLE command_items (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    command_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    price INT NOT NULL, -- Prix de 1 produit * quantité
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (command_id) REFERENCES commands(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE
);


-- FEEDBACKS table
CREATE TABLE feedbacks (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
);
-- ============================================================
-- E-Commerce Migration Script
-- Run this once against your BA2A_PHP database
-- ============================================================

-- 1. Alter user table: drop user_class, add user_role
ALTER TABLE user
    DROP COLUMN IF EXISTS user_class;

ALTER TABLE user
    ADD COLUMN IF NOT EXISTS user_role VARCHAR(20) NOT NULL DEFAULT 'client';

-- 2. Category table
CREATE TABLE IF NOT EXISTS category (
    category_id   INT PRIMARY KEY AUTO_INCREMENT,
    name          VARCHAR(100) NOT NULL,
    slug          VARCHAR(100) NOT NULL UNIQUE,
    description   TEXT,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Product table
CREATE TABLE IF NOT EXISTS product (
    product_id   INT PRIMARY KEY AUTO_INCREMENT,
    category_id  INT DEFAULT NULL,
    name         VARCHAR(150) NOT NULL,
    description  TEXT,
    price        DECIMAL(10,2) NOT NULL,
    quantity     INT NOT NULL DEFAULT 0,
    image        VARCHAR(255),
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES category(category_id) ON DELETE SET NULL
);

-- 3b. Add category_id to product if table already existed without it
-- Note: If you get an error that the column already exists, you can ignore this section.
ALTER TABLE product
    ADD COLUMN category_id INT DEFAULT NULL;

ALTER TABLE product
    ADD CONSTRAINT fk_product_category
    FOREIGN KEY (category_id) REFERENCES category(category_id) ON DELETE SET NULL;

-- 4. Cart table
CREATE TABLE IF NOT EXISTS cart (
    cart_id    INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id    INT NOT NULL,
    quantity   INT NOT NULL DEFAULT 1,
    FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES user(user_id)       ON DELETE CASCADE
);

-- 5. Payment table
CREATE TABLE IF NOT EXISTS payment (
    payment_id  INT PRIMARY KEY AUTO_INCREMENT,
    user_id     INT NOT NULL,
    product_id  INT NOT NULL,
    quantity    INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status      VARCHAR(30) NOT NULL DEFAULT 'pending',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES user(user_id),
    FOREIGN KEY (product_id) REFERENCES product(product_id)
);

-- 6. Seed default categories
INSERT IGNORE INTO category (name, slug, description) VALUES
    ('Electronics',  'electronics',  'Gadgets, phones, computers and more'),
    ('Clothing',     'clothing',     'Fashion and apparel for all'),
    ('Food & Drink', 'food-drink',   'Fresh and packaged food products'),
    ('Home & Living','home-living',  'Furniture, decor and household items'),
    ('Sports',       'sports',       'Sporting goods and fitness equipment');

-- 7. Seed a default admin user (password: admin123)
-- Change email/password as needed. Skip if you already have an admin.
INSERT IGNORE INTO user (user_name, user_email, user_password, user_role)
VALUES ('Admin', 'admin@store.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Note: The hashed password above = "password". Change it after first login.

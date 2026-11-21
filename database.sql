CREATE DATABASE IF NOT EXISTS cafe_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cafe_management;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','kasir') NOT NULL DEFAULT 'kasir',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NULL,
  name VARCHAR(120) NOT NULL,
  cost_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  sell_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  invoice_no VARCHAR(40) NOT NULL UNIQUE,
  cashier_id INT NOT NULL,
  customer_name VARCHAR(100) DEFAULT 'Umum',
  subtotal DECIMAL(12,2) NOT NULL,
  payment DECIMAL(12,2) NOT NULL,
  change_amount DECIMAL(12,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cashier_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  product_name VARCHAR(120) NOT NULL,
  qty INT NOT NULL,
  price DECIMAL(12,2) NOT NULL,
  line_total DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS stock_movements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  movement_type ENUM('masuk','keluar','penyesuaian') NOT NULL,
  qty INT NOT NULL,
  note VARCHAR(255),
  user_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT IGNORE INTO categories(name) VALUES ('Kopi'),('Non-kopi'),('Teh'),('Makanan'),('Dessert');

INSERT INTO products(category_id,name,cost_price,sell_price,stock)
SELECT c.id, v.name, v.cost, v.sell, v.stock
FROM (
  SELECT 'Espresso' name, 'Kopi' category, 7000 cost, 18000 sell, 50 stock UNION ALL
  SELECT 'Americano','Kopi',8000,20000,50 UNION ALL
  SELECT 'Cappuccino','Kopi',11000,28000,40 UNION ALL
  SELECT 'Cafe Latte','Kopi',12000,30000,40 UNION ALL
  SELECT 'Caramel Latte','Kopi',14000,35000,35 UNION ALL
  SELECT 'Matcha Latte','Non-kopi',13000,32000,30 UNION ALL
  SELECT 'Chocolate','Non-kopi',11000,28000,30 UNION ALL
  SELECT 'Lemon Tea','Teh',6000,18000,40 UNION ALL
  SELECT 'Croissant','Makanan',10000,25000,25 UNION ALL
  SELECT 'Chicken Sandwich','Makanan',16000,35000,20 UNION ALL
  SELECT 'French Fries','Makanan',9000,22000,25 UNION ALL
  SELECT 'Cheesecake','Dessert',15000,32000,20
) v
JOIN categories c ON c.name = v.category
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.name = v.name);

-- ==========================================================
-- E-Customize Gifts Database Schema (MySQL)
-- Complete schema for MVP modules 1 to 10
-- Compatible with MySQL 5.7+, MySQL 8.0+, MariaDB, and XAMPP
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `gift_customizer_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gift_customizer_db`;

-- ----------------------------------------------------------
-- 1. Users Table (Authentication & User Management)
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` VARCHAR(64) UNIQUE NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('customer', 'admin') DEFAULT 'customer',
    `phone` VARCHAR(30) NULL,
    `address` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 2. Gift Categories Table
-- ----------------------------------------------------------
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(50) UNIQUE NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `icon` VARCHAR(20) DEFAULT '🎁',
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 3. Products Catalog Table
-- ----------------------------------------------------------
CREATE TABLE `products` (
    `id` VARCHAR(50) PRIMARY KEY,
    `category_slug` VARCHAR(50) NOT NULL,
    `category_name` VARCHAR(100) NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `rating` DECIMAL(3, 1) DEFAULT 5.0,
    `review_count` INT DEFAULT 0,
    `badge` VARCHAR(50) NULL,
    `image` VARCHAR(50) NOT NULL,
    `short_description` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `features` JSON NULL,
    `colors` JSON NULL,
    `default_text` JSON NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_slug`) REFERENCES `categories`(`slug`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 4. Customer Orders Table
-- ----------------------------------------------------------
CREATE TABLE `orders` (
    `id` VARCHAR(50) PRIMARY KEY,
    `user_id` VARCHAR(64) NULL,
    `customer_name` VARCHAR(100) NOT NULL,
    `customer_email` VARCHAR(150) NOT NULL,
    `customer_phone` VARCHAR(30) NOT NULL,
    `shipping_address` TEXT NOT NULL,
    `shipping_city` VARCHAR(100) NOT NULL,
    `shipping_zip` VARCHAR(20) NOT NULL,
    `payment_method` VARCHAR(50) DEFAULT 'Credit Card',
    `payment_status` ENUM('pending', 'paid', 'failed') DEFAULT 'paid',
    `order_status` ENUM('placed', 'in_production', 'shipped', 'delivered', 'cancelled') DEFAULT 'placed',
    `status_label` VARCHAR(50) DEFAULT 'Order Placed',
    `tracking_code` VARCHAR(50) NULL,
    `carrier` VARCHAR(50) DEFAULT 'FedEx Express',
    `subtotal` DECIMAL(10, 2) NOT NULL,
    `tax` DECIMAL(10, 2) NOT NULL,
    `discount` DECIMAL(10, 2) DEFAULT 0.00,
    `total` DECIMAL(10, 2) NOT NULL,
    `timeline` JSON NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 5. Order Items & Customization Configurations
-- ----------------------------------------------------------
CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` VARCHAR(50) NOT NULL,
    `product_id` VARCHAR(50) NOT NULL,
    `product_name` VARCHAR(150) NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `quantity` INT DEFAULT 1,
    `color_name` VARCHAR(50) NULL,
    `custom_photo_url` LONGTEXT NULL,
    `custom_name` VARCHAR(150) NULL,
    `custom_message` TEXT NULL,
    `custom_date` VARCHAR(50) NULL,
    `custom_font` VARCHAR(100) NULL,
    `custom_color` VARCHAR(30) NULL,
    `preview_image` LONGTEXT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 6. Product Reviews Table
-- ----------------------------------------------------------
CREATE TABLE `reviews` (
    `id` VARCHAR(50) PRIMARY KEY,
    `product_id` VARCHAR(50) NOT NULL,
    `user_name` VARCHAR(100) NOT NULL,
    `rating` INT NOT NULL,
    `verified` BOOLEAN DEFAULT TRUE,
    `title` VARCHAR(200) NOT NULL,
    `comment` TEXT NOT NULL,
    `created_at` DATE NOT NULL,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 7. Contact / Customer Support Messages Table
-- ----------------------------------------------------------
CREATE TABLE `messages` (
    `id` VARCHAR(50) PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `subject` VARCHAR(200) NOT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- SEED INITIAL DATA
-- ==========================================================

-- Seed Users (Password: password123)
-- Hash generated via password_hash('password123', PASSWORD_BCRYPT)
INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `phone`, `address`) VALUES
('usr_admin_001', 'Studio Administrator', 'admin@giftstudio.com', '$2y$10$.1UB4D5Y96f0slJ/RuudS.obXbmTokjE0FtiBSwjG/m8V9rLtoxNi', 'admin', '+1 (800) 555-4438', 'CraftGift Studio Headquarters, San Francisco, CA'),
('usr_demo_002', 'Sarah Jenkins', 'sarah@example.com', '$2y$10$.1UB4D5Y96f0slJ/RuudS.obXbmTokjE0FtiBSwjG/m8V9rLtoxNi', 'customer', '+1 (555) 234-5678', '742 Evergreen Terrace, Springfield, OR 97477');

-- Seed Categories
INSERT INTO `categories` (`slug`, `name`, `description`, `icon`, `sort_order`) VALUES
('mugs', 'Personalized Mugs', 'Durable ceramic mugs with wrap-around photos and custom text', '☕', 1),
('tshirts', 'Custom T-Shirts', '100% combed cotton apparel with vibrant chest photo and slogan print', '👕', 2),
('frames', 'Photo Frames', 'Nordic oak wood and tabletop frames with laser-engraved nameplates', '🖼️', 3),
('cushions', 'Custom Cushions', 'Ultra-soft micro-velvet throw pillows with edge-to-edge sublimation', '🛋️', 4),
('keychains', 'Crystal Keychains', 'Subsurface 3D laser-etched optical crystal keychains with LED lights', '🔑', 5),
('lamps', 'LED Acrylic Lamps', 'Warm glow laser-etched transparent night lights with wooden base', '💡', 6);

-- Seed Products
INSERT INTO `products` (`id`, `category_slug`, `category_name`, `name`, `price`, `rating`, `review_count`, `badge`, `image`, `short_description`, `description`, `features`, `colors`, `default_text`) VALUES
('ceramic-mug', 'mugs', 'Personalized Mugs', 'Ceramic Keepsake Mug (11oz)', 18.99, 4.9, 142, 'Bestseller ⭐', 'mug', 
 'Premium ultra-glossy ceramic mug with wraparound photo finish and personalized quote.',
 'Start every morning with love. Our custom 11oz ceramic mug is crafted from durable, high-grade white ceramic with a glossy finish. Microwave and dishwasher safe.',
 '["High-grade glossy ceramic (11oz / 325ml)", "Microwave and dishwasher safe", "Scratch-resistant vibrant dye-sublimation print", "Comfort-grip C-handle for effortless sipping"]',
 '[{"name": "Pure White", "hex": "#FFFFFF", "dark": false}, {"name": "Midnight Black", "hex": "#1E293B", "dark": true}, {"name": "Soft Blush", "hex": "#FCE7F3", "dark": false}, {"name": "Mint Sage", "hex": "#E6F4EA", "dark": false}]',
 '{"name": "Sophia & Liam", "message": "You make every morning brighter ☕", "date": "Est. Oct 2024"}'),

('custom-tshirt', 'tshirts', 'Custom T-Shirts', 'Custom Graphic Cotton T-Shirt', 22.50, 4.8, 118, 'Top Pick 🔥', 'tshirt',
 '100% premium ring-spun combed cotton t-shirt with vibrant direct-to-garment custom print.',
 'Wear your memories proudly. Crafted from breathable, heavyweight 180 GSM bio-washed cotton. Features reinforced double-needle stitching and high-resolution DTG printing.',
 '["100% Super Combed Bio-Washed Ring-Spun Cotton", "Durable Direct-to-Garment (DTG) fade-proof print", "Pre-shrunk fabric to prevent shrinking after wash", "Regular unisex fit with ribbed collar"]',
 '[{"name": "Jet Black", "hex": "#1E293B", "dark": true}, {"name": "Pure White", "hex": "#FFFFFF", "dark": false}, {"name": "Navy Blue", "hex": "#1E3A8A", "dark": true}, {"name": "Heather Grey", "hex": "#E2E8F0", "dark": false}]',
 '{"name": "Liam & Olivia", "message": "Made For Each Other ✨", "date": "Together Since 2021"}'),

('wooden-frame', 'frames', 'Photo Frames', 'Nordic Oak Tabletop Photo Frame', 29.50, 4.8, 98, 'Handcrafted', 'frame',
 'Natural oak wood frame with laser-engraved caption plate and archival photo print.',
 'Preserve your dearest family moments in timeless Scandinavian elegance. Each frame is individually hand-finished from sustainable European oak with beveled museum-grade mat board.',
 '["Solid European white oak with satin protective coat", "Ultra-clear shatterproof optical glass front", "Laser-engraved brushed metallic nameplate included", "Dual easel stand for vertical or horizontal tabletop display"]',
 '[{"name": "Natural Oak", "hex": "#D7A15C", "dark": false}, {"name": "Dark Walnut", "hex": "#5C381E", "dark": true}, {"name": "Ash White", "hex": "#F3F4F6", "dark": false}]',
 '{"name": "The Henderson Family", "message": "Where life begins and love never ends ❤️", "date": "Summer 2024"}'),

('velvet-cushion', 'cushions', 'Custom Cushions', 'Custom Velvet Throw Pillow (16x16")', 24.99, 4.7, 76, 'Cozy Pick', 'cushion',
 'Ultra-soft micro-velvet cushion with vibrant edge-to-edge sublimation print.',
 'Snuggle up with sweet memories. Crafted from plush, velvety soft fabric with a concealed zipper and hypoallergenic polyester fiber filling.',
 '["Luxurious soft-touch velvet polyester cover", "Includes plush virgin poly-fill pillow insert", "Hidden bottom zipper for easy machine washing", "Fade-resistant, baby-safe and pet-safe inks"]',
 '[{"name": "Soft Cream", "hex": "#FAF7F2", "dark": false}, {"name": "Pastel Slate", "hex": "#CBD5E1", "dark": false}, {"name": "Dusty Rose", "hex": "#E2B8B8", "dark": false}]',
 '{"name": "Home Sweet Home", "message": "Filled with memories and warm cuddles 🏡", "date": "Established 2023"}'),

('crystal-keychain', 'keychains', 'Crystal Keychains', 'Laser 3D Crystal Keychain', 14.99, 4.8, 64, 'Pocket Gift', 'keychain',
 'Precision subsurface laser-engraved K9 optical crystal with bright LED accent light.',
 'Keep your favorite loved ones or company logo right in your pocket. Top-grade K9 crystal with subsurface laser engraving that appears floating inside the glass.',
 '["Pure optical K9 crystal block (30x20x15mm)", "Built-in rotary LED light with replaceable battery", "Stainless steel heavy-duty key ring and chain", "Includes premium velvet gift pouch"]',
 '[{"name": "Clear Crystal", "hex": "#FFFFFF", "dark": false}, {"name": "Blue LED Accent", "hex": "#38BDF8", "dark": false}]',
 '{"name": "Best Dad Ever", "message": "Always by my side ❤️", "date": "2024"}'),

('acrylic-lamp', 'lamps', 'LED Acrylic Lamps', 'Glowing Acrylic LED Night Lamp', 34.00, 5.0, 215, 'Trending 🔥', 'lamp',
 'Crystal-clear acrylic plaque with soft warm LED illumination and custom laser etching.',
 'Turn your favorite photo into an enchanting warm night light. Precision laser-etched onto optical grade 5mm thick cast acrylic that slots into a solid beechwood LED pedestal.',
 '["5mm ultra-clear shatterproof cast acrylic plaque", "Solid beechwood base with hidden warm-white LED strip", "USB powered with inline on/off push button switch", "High-precision laser engraving that never fades or peels"]',
 '[{"name": "Warm Amber LED", "hex": "#FFE7B3", "dark": false}, {"name": "Pure White LED", "hex": "#E0F2FE", "dark": false}, {"name": "Romantic Pink LED", "hex": "#FCE7F3", "dark": false}]',
 '{"name": "Happy Anniversary", "message": "Forever my favorite chapter ✨", "date": "24th September"}');

-- Seed Sample Order
INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `shipping_city`, `shipping_zip`, `payment_method`, `payment_status`, `order_status`, `status_label`, `tracking_code`, `carrier`, `subtotal`, `tax`, `discount`, `total`, `timeline`) VALUES
('ORD-98421', 'usr_demo_002', 'Sarah Jenkins', 'sarah@example.com', '+1 (555) 234-5678', '742 Evergreen Terrace', 'Springfield, OR', '97477', 'Credit Card (•••• 4242)', 'paid', 'in_production', 'In Production', 'FX-98421008-US', 'FedEx Express', 48.49, 3.88, 5.00, 47.37, 
'[{"title": "Order Placed", "desc": "Custom gift specifications received and verified by design team.", "time": "Oct 18, 2026 - 10:15 AM", "done": true}, {"title": "Production & Laser Engraving", "desc": "Photo printing and precision laser calibration in workshop.", "time": "Oct 18, 2026 - 02:40 PM", "done": true}, {"title": "Handcrafted Quality Inspection", "desc": "Optical inspection for color accuracy, finish and packaging.", "time": "Estimated Today", "done": false}, {"title": "Handed to FedEx Express", "desc": "Tracking number FX-98421008-US active for dispatch.", "time": "Estimated Tomorrow", "done": false}, {"title": "Delivered to Sarah Jenkins", "desc": "Delivered to 742 Evergreen Terrace.", "time": "Estimated Oct 21", "done": false}]');

-- Seed Sample Order Item
INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `price`, `quantity`, `color_name`, `custom_name`, `custom_message`, `custom_date`, `custom_font`, `custom_color`) VALUES
('ORD-98421', 'ceramic-mug', 'Ceramic Keepsake Mug (11oz)', 18.99, 1, 'Pure White', 'Sophia & Liam', 'You make every morning brighter ☕', 'Est. Oct 2024', "'Playfair Display', serif", '#1E293B'),
('ORD-98421', 'wooden-frame', 'Nordic Oak Tabletop Photo Frame', 29.50, 1, 'Natural Oak', 'The Henderson Family', 'Where life begins and love never ends ❤️', 'Summer 2024', "'Playfair Display', serif", '#1E293B');

-- Seed Reviews
INSERT INTO `reviews` (`id`, `product_id`, `user_name`, `rating`, `verified`, `title`, `comment`, `created_at`) VALUES
('rev_001', 'ceramic-mug', 'Emily & Jack M.', 5, 1, 'Unbelievable quality and print sharpness!', 'Ordered this as a 1st anniversary surprise for my husband. The wrap-around photo print is crystal sharp and the glaze feels luxurious. Dishwasher test passed with flying colors!', '2026-10-10'),
('rev_002', 'custom-tshirt', 'Marcus Thorne', 5, 1, 'Super soft cotton and brilliant colors', 'The print on the t-shirt exceeded my expectations. The fabric is thick and comfortable, and our couple photo turned out razor sharp. Highly recommended!', '2026-10-12'),
('rev_003', 'wooden-frame', 'David Robinson', 5, 1, 'The brass plate laser engraving is flawless', 'Shipped in velvet-lined foam packaging. The oak grain has a warm natural sheen and the customized brass plate looks like an heirloom piece.', '2026-10-08');

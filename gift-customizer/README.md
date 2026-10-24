# 🎁 CraftGift Studio — Personalized E-Commerce Gift Platform (PHP Edition)

A full-featured personalized gift e-commerce web platform built with **PHP 8.3**, HTML5 Canvas 3D rendering, and JSON/MySQL database persistence.

---

## 🌟 Modules Implemented

1. **⭐ Live Customizer Studio (MVP Feature #6)** (`customizer.php`)
   - Drag & drop photo upload with zoom, pan, rotation, and photo filters.
   - Live text personalization: recipient name, custom message/quote, date subtitle.
   - Font switcher (*Playfair Display*, *Dancing Script*, *Caveat*, *Cinzel*, *Montserrat*, *Inter*).
   - Real-time HTML5 800×800 Canvas 3D mockup rendering for mugs, t-shirts, oak frames, velvet cushions, acrylic lamps, and crystal keychains.
   - Dual view: Realistic 3D Mockup vs Technical Safe-Zone Print Shop Template.
   - 1-click snapshot generation and "Save Customization & Add to Cart".

2. **🛍️ Shopping Cart** (`cart.php`)
   - Itemized view with customized thumbnail snapshots, custom message, font style, and unit pricing.
   - Real-time quantity adjustments, subtotal, and free shipping calculation.
   - Promo coupon discounts (e.g. `GIFT10` for 10% off, `WELCOME5` for $5 off).

3. **🔒 Secure Checkout** (`checkout.php`)
   - Shipping address & recipient contact collection.
   - Simulated payment processing (Credit/Debit Card or PayPal).
   - Automatically generates real-time Order ID (`ORD-XXXXXX`) and Tracking Number (`TRK-XXXXXXX-US`).

4. **🚚 Live Order Tracking** (`tracking.php`)
   - Visual 6-step progress stepper:
     1. Order Placed
     2. Customization Approved
     3. In Production
     4. Quality Inspection
     5. Shipped & In Transit
     6. Delivered
   - Real-time courier tracking number, ETA date, and full breakdown of customized items.

5. **📦 Product Detail & Reviews** (`product-detail.php`)
   - Gift specifications, material details, dimensions, and color variants.
   - "Customize This Gift Now 🎁" CTA linking directly to the live customizer.
   - Customer Reviews & Ratings section with average rating score and 5-star distribution.
   - "Write a Review" form allowing customers to publish ratings and feedback.

6. **📂 Gift Categories Catalog** (`categories.php`)
   - Filter by categories: Personalized Mugs, Custom T-Shirts, Photo Frames, Cushions, Acrylic Lamps, Crystal Keychains.
   - Live search filter and price/rating sorting.

7. **👤 User Authentication (Sign In & Sign Up)** (`login.php`, `register.php`, `logout.php`)
   - Secure BCrypt password hashing (`password_hash`, `password_verify`).
   - Session management (`$_SESSION['user_id']`, `$_SESSION['user_role']`).
   - 1-Click Demo Login credentials.

8. **📊 Customer Dashboard** (`dashboard.php`)
   - Recent orders history with live status badges.
   - 1-Click order tracking links.
   - Profile & default shipping address editor.

9. **👑 Administrator Panel** (`admin/index.php`, `admin/login.php`)
   - KPI metrics: Total Sales ($), Total Orders, In-Production Customizations, Total Customers.
   - Order management table: Inspect customer uploaded photos and engraved text.
   - Real-time Order Status updater (Order Placed &rarr; In Production &rarr; Shipped &rarr; Delivered).
   - View contact form inquiries.

10. **💬 Contact Us & Help Center** (`contact.php`)
    - Customer inquiry form saved to `data/messages.json`.
    - Support channels: Email, hotline, and studio workshop location.
    - Interactive FAQ Accordion.

---

## 🚀 How to Run the Application

### Method 1: Double-Click Batch File (Windows)
Double-click [`start_server.bat`](file:///C:/Users/Sanjeev%20Dhawan/.gemini/antigravity/scratch/gift-customizer/start_server.bat). It automatically starts the PHP development server and opens your browser to:
```
http://localhost:8000
```

### Method 2: Command Line
Open PowerShell or Command Prompt in this folder and run:
```powershell
php -S localhost:8000
```
Then visit **`http://localhost:8000`** in your browser.

---

## 🔑 Demo Login Accounts

| Role | Email | Password | Access |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@giftstudio.com` | `password123` | Full Admin Console &amp; Order Status Manager (`admin/index.php`) |
| **Customer** | `sarah@example.com` | `password123` | Customer Dashboard &amp; Order History (`dashboard.php`) |

---

## 📁 Project Architecture

```
gift-customizer/
├── index.php                 # Home page (bestsellers, categories, review highlights)
├── categories.php            # Gift categories catalog & search filter
├── product-detail.php        # Product page with reviews & customizer CTA
├── customizer.php            # ⭐ Live 3D Gift Customizer Studio (MVP #6)
├── cart.php                  # Shopping cart with coupon discount system
├── checkout.php              # Checkout simulation with order generation
├── tracking.php              # Real-time visual order tracking timeline
├── login.php                 # User Sign In
├── register.php              # User Sign Up
├── logout.php                # Session logout
├── dashboard.php             # Customer dashboard & order history
├── contact.php               # Contact Us & FAQ accordion
├── start_server.bat          # 1-Click launch script
├── admin/
│   ├── index.php             # Admin Dashboard & order status updater
│   └── login.php             # Admin portal login
├── api/
│   └── save-customization.php# REST API for customizer canvas sync
├── includes/
│   ├── config.php            # Data abstraction layer & session management
│   ├── db.php                # Database connection (MySQL with JSON fallback)
│   ├── header.php            # Global navigation bar & cart counter
│   └── footer.php            # Global footer & customer care links
├── css/
│   └── styles.css            # Responsive styles for all pages & customizer
├── js/
│   ├── products.js           # Product configurations & mockups
│   ├── customizer.js         # Canvas 3D rendering engine
│   ├── cart.js               # Cart persistence & calculations
│   └── app.js                # Customizer event handling
└── data/
    ├── products.json         # Products catalog database
    ├── users.json            # Users & passwords database
    ├── orders.json           # Order history & custom design payloads
    ├── reviews.json          # Customer ratings database
    └── messages.json         # Contact inquiries database
```

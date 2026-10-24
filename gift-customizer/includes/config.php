<?php
/**
 * Global Configuration & Data Abstraction Layer
 * Handles JSON database persistence, authentication sessions, and cart storage.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!ob_get_level()) {
    ob_start();
}

require_once __DIR__ . '/db.php';

define('APP_NAME', 'CraftGift Studio');
define('APP_TAGLINE', 'Personalized Custom Gifts Made With Love');
define('DATA_DIR', __DIR__ . '/../data');

// Ensure data folder exists
if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}

// -------------------------------------------------------------
// JSON Database Helpers
// -------------------------------------------------------------

function load_json($filename) {
    $path = DATA_DIR . '/' . $filename;
    if (!file_exists($path)) {
        return [];
    }
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function save_json($filename, $data) {
    $path = DATA_DIR . '/' . $filename;
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Product helpers
function get_products() {
    return load_json('products.json');
}

function get_product_by_id($id) {
    $products = get_products();
    foreach ($products as $p) {
        if ($p['id'] === $id) return $p;
    }
    return null;
}

// Order helpers
function get_orders() {
    return load_json('orders.json');
}

function get_order_by_id($id) {
    $orders = get_orders();
    foreach ($orders as $o) {
        if (strtoupper($o['id']) === strtoupper(trim($id))) return $o;
    }
    return null;
}

function save_order($new_order) {
    $orders = get_orders();
    array_unshift($orders, $new_order);
    save_json('orders.json', $orders);
    return $new_order;
}

function update_order_status($order_id, $new_status, $new_label = '') {
    $orders = get_orders();
    foreach ($orders as &$o) {
        if ($o['id'] === $order_id) {
            $o['status'] = $new_status;
            if ($new_label) $o['status_label'] = $new_label;
            $o['updated_at'] = date('Y-m-d H:i:s');
            save_json('orders.json', $orders);
            return true;
        }
    }
    return false;
}

// User helpers
function get_users() {
    return load_json('users.json');
}

function find_user_by_email($email) {
    $users = get_users();
    $email = strtolower(trim($email));
    foreach ($users as $u) {
        if (strtolower($u['email']) === $email) return $u;
    }
    return null;
}

function create_user($name, $email, $password, $phone = '', $address = '') {
    $users = get_users();
    $newUser = [
        'id' => 'usr_' . time() . '_' . substr(md5(uniqid()), 0, 4),
        'name' => trim($name),
        'email' => strtolower(trim($email)),
        'password' => password_hash($password, PASSWORD_BCRYPT),
        'role' => 'customer',
        'phone' => trim($phone),
        'address' => trim($address),
        'created_at' => date('Y-m-d H:i:s')
    ];
    $users[] = $newUser;
    save_json('users.json', $users);
    return $newUser;
}

// Review helpers
function get_reviews($product_id = null) {
    $reviews = load_json('reviews.json');
    if ($product_id) {
        return array_values(array_filter($reviews, function($r) use ($product_id) {
            return $r['product_id'] === $product_id;
        }));
    }
    return $reviews;
}

function save_review($rev) {
    $reviews = load_json('reviews.json');
    array_unshift($reviews, $rev);
    save_json('reviews.json', $reviews);
    return $rev;
}

// Contact Message helpers
function get_messages() {
    return load_json('messages.json');
}

function save_message($msg) {
    $messages = get_messages();
    array_unshift($messages, $msg);
    save_json('messages.json', $messages);
    return $msg;
}

// -------------------------------------------------------------
// Authentication & Session Helpers
// -------------------------------------------------------------

function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function is_admin() {
    return is_logged_in() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function current_user() {
    if (!is_logged_in()) return null;
    $users = get_users();
    foreach ($users as $u) {
        if ($u['id'] === $_SESSION['user_id']) return $u;
    }
    return null;
}

function require_login($redirect_to = 'login.php') {
    if (!is_logged_in()) {
        set_flash('error', 'Please log in to continue.');
        header("Location: $redirect_to");
        exit;
    }
}

function require_admin($redirect_to = '../login.php') {
    if (!is_admin()) {
        set_flash('error', 'Access denied. Administrator privileges required.');
        header("Location: $redirect_to");
        exit;
    }
}

// Flash Notifications
function set_flash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $msg];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// -------------------------------------------------------------
// Shopping Cart Helpers (PHP Session-Backed)
// -------------------------------------------------------------

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function get_cart() {
    return $_SESSION['cart'] ?? [];
}

function add_to_cart($item) {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $_SESSION['cart'][] = $item;
}

function update_cart_quantity($index, $qty) {
    if (isset($_SESSION['cart'][$index])) {
        if ($qty <= 0) {
            array_splice($_SESSION['cart'], $index, 1);
        } else {
            $_SESSION['cart'][$index]['quantity'] = $qty;
        }
    }
}

function remove_from_cart($index) {
    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
    }
}

function clear_cart() {
    $_SESSION['cart'] = [];
    unset($_SESSION['promo_discount']);
}

function get_cart_count() {
    $count = 0;
    foreach (get_cart() as $item) {
        $count += ($item['quantity'] ?? 1);
    }
    return $count;
}

function get_cart_subtotal() {
    $subtotal = 0;
    foreach (get_cart() as $item) {
        $price = $item['price'] ?? 0;
        $qty = $item['quantity'] ?? 1;
        $subtotal += ($price * $qty);
    }
    return $subtotal;
}

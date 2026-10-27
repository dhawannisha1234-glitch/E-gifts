<?php
require_once __DIR__ . '/config.php';
$currentUser = current_user();
$cartCount = get_cart_count();
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' . APP_NAME : APP_NAME . ' — ' . APP_TAGLINE ?></title>
    <!-- Google Web Fonts for Gift Personalization -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Cinzel:wght@600;700&family=Dancing+Script:wght@600;700&family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <!-- Announcement Bar -->
    <div class="announcement-bar">
        <span>✨ Free Gift Packaging &amp; Shipping on Orders Over ₹999 • 100% Live 3D Preview Guaranteed</span>
    </div>

    <!-- Header Navigation -->
    <header class="app-header">
        <div class="header-inner">
            <a href="index.php" class="brand-group">
                <div class="brand-icon">🎁</div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                        <span class="brand-name">CraftGift Studio</span>
                        <span class="brand-tag">PHP Edition</span>
                    </div>
                    <div style="font-size: 0.74rem; color: #64748B;">Personalized Gifts Crafted to Perfection</div>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="nav-links">
                <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Home</a>
                <a href="categories.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">Gift Categories</a>
                <a href="customizer.php" class="nav-link nav-highlight <?= basename($_SERVER['PHP_SELF']) == 'customizer.php' ? 'active' : '' ?>">⭐ Customize Studio</a>
                <a href="tracking.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'tracking.php' ? 'active' : '' ?>">Track Order</a>
                <a href="contact.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>">Contact Us</a>
                <?php if (is_admin()): ?>
                    <a href="admin/index.php" class="nav-link admin-nav-pill">Admin Panel</a>
                <?php endif; ?>
            </nav>

            <!-- User and Cart Actions -->
            <div class="header-actions">
                <?php if ($currentUser): ?>
                    <div class="user-menu-group">
                        <a href="dashboard.php" class="user-profile-link" title="My Dashboard">
                            <span class="user-avatar-badge"><?= strtoupper(substr($currentUser['name'], 0, 1)) ?></span>
                            <span class="user-name-text"><?= htmlspecialchars(explode(' ', $currentUser['name'])[0]) ?></span>
                        </a>
                        <a href="logout.php" class="logout-link" title="Logout">Sign Out</a>
                    </div>
                <?php else: ?>
                    <div class="auth-btns">
                        <a href="login.php" class="btn-text">Log In</a>
                        <a href="register.php" class="btn-signup">Sign Up</a>
                    </div>
                <?php endif; ?>

                <a href="cart.php" class="cart-btn" title="View Shopping Cart">
                    <span>🛒 Cart</span>
                    <span class="cart-count" id="headerCartCount"><?= $cartCount ?></span>
                </a>
            </div>
        </div>
    </header>

    <!-- Global Flash Notification -->
    <?php if ($flash): ?>
        <div class="flash-banner flash-<?= htmlspecialchars($flash['type']) ?>">
            <div class="flash-inner">
                <span><?= $flash['type'] === 'success' ? '✓' : '⚠️' ?></span>
                <span><?= htmlspecialchars($flash['message']) ?></span>
            </div>
        </div>
    <?php endif; ?>

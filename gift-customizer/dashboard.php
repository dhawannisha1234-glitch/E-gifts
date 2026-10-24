<?php
require_once __DIR__ . '/includes/config.php';
require_login();

$user = current_user();
$allOrders = get_orders();

// Filter orders belonging to this user (or show sample orders for demo)
$userOrders = array_filter($allOrders, function($o) use ($user) {
    return ($o['user_id'] ?? '') === $user['id'] || ($o['customer_email'] ?? '') === $user['email'];
});
if (empty($userOrders)) {
    // Show all demo orders for preview
    $userOrders = $allOrders;
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    $users = get_users();
    foreach ($users as &$u) {
        if ($u['id'] === $user['id']) {
            $u['phone'] = $phone;
            $u['address'] = $address;
            break;
        }
    }
    save_json('users.json', $users);
    set_flash('success', 'Profile and default shipping address updated.');
    header('Location: dashboard.php');
    exit;
}

$pageTitle = 'Customer Dashboard — ' . htmlspecialchars($user['name']);
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header-banner">
    <div class="page-header-inner">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div class="huge-avatar-circle">
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            </div>
            <div>
                <h1 class="page-title" style="margin-bottom: 0.2rem;">Hello, <?= htmlspecialchars($user['name']) ?>! 👋</h1>
                <p class="page-subtitle"><?= htmlspecialchars($user['email']) ?> • Member since <?= date('M Y', strtotime($user['created_at'] ?? 'now')) ?></p>
            </div>
        </div>
    </div>
</div>

<main class="section-container">
    
    <!-- Stats Row -->
    <div class="dashboard-stats-grid">
        <div class="stat-card">
            <div class="stat-icon-circle" style="background: #EEF2FF; color: #4F46E5;">📦</div>
            <div>
                <div class="stat-val"><?= count($userOrders) ?></div>
                <div class="stat-label">Total Orders Placed</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-circle" style="background: #FEF3C7; color: #D97706;">⚡</div>
            <div>
                <div class="stat-val">
                    <?= count(array_filter($userOrders, fn($o) => in_array($o['status'], ['placed', 'in_production']))) ?>
                </div>
                <div class="stat-label">In Production / Crafting</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-circle" style="background: #ECFDF5; color: #059669;">🎨</div>
            <div>
                <div class="stat-val">Active</div>
                <div class="stat-label">Customizer Studio Access</div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Tabs -->
    <div class="dashboard-layout">
        
        <!-- Left: My Orders -->
        <div class="dashboard-main-col">
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="card-title">My Personalized Gift Orders</h3>
                    <a href="customizer.php" class="btn-sm-primary" style="text-decoration: none;">+ Customize New Gift</a>
                </div>

                <?php if (empty($userOrders)): ?>
                    <div style="text-align: center; padding: 3rem 1rem; color: #64748B;">
                        <div style="font-size: 3rem; margin-bottom: 0.5rem;">🎁</div>
                        <h4 style="font-weight: 700; color: #1E293B;">No Orders Yet</h4>
                        <p style="font-size: 0.85rem; margin-top: 0.25rem;">Start by creating your first personalized keepsake mug or LED lamp!</p>
                        <a href="customizer.php" class="save-cta-btn" style="display: inline-flex; width: auto; margin-top: 1rem; text-decoration: none; padding: 0.65rem 1.25rem;">
                            Open Customizer Studio
                        </a>
                    </div>
                <?php else: ?>
                    <div class="orders-list-stack">
                        <?php foreach ($userOrders as $ord): ?>
                            <div class="order-summary-card">
                                <div class="order-summary-top">
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <span style="font-weight: 800; font-size: 1.05rem; color: #0F172A;"><?= htmlspecialchars($ord['id']) ?></span>
                                            <span class="badge-status-<?= htmlspecialchars($ord['status']) ?>">
                                                <?= htmlspecialchars($ord['status_label'] ?? ucfirst($ord['status'])) ?>
                                            </span>
                                        </div>
                                        <div style="font-size: 0.78rem; color: #64748B; margin-top: 0.2rem;">
                                            Placed on <?= date('M d, Y - h:i A', strtotime($ord['created_at'])) ?>
                                        </div>
                                    </div>

                                    <div style="text-align: right;">
                                        <div style="font-size: 1.15rem; font-weight: 800; color: #0F172A;">$<?= number_format(floatval($ord['total']), 2) ?></div>
                                        <a href="tracking.php?order_id=<?= urlencode($ord['id']) ?>" class="btn-track-pill">
                                            Live Tracker &rarr;
                                        </a>
                                    </div>
                                </div>

                                <div class="order-items-mini-list">
                                    <?php foreach ($ord['items'] as $it): ?>
                                        <div class="order-item-mini-row">
                                            <span style="font-weight: 700;"><?= htmlspecialchars($it['product_name'] ?? $it['productName'] ?? 'Customized Gift') ?> (x<?= $it['quantity'] ?? 1 ?>)</span>
                                            <span style="color: #4F46E5; font-size: 0.8rem;">
                                                Custom Name: "<?= htmlspecialchars($it['customization']['name'] ?? 'Personalized') ?>"
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Profile & Shipping Details -->
        <div class="dashboard-side-col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Default Shipping Info</h3>
                </div>

                <form action="dashboard.php" method="POST">
                    <input type="hidden" name="action" value="update_profile">

                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-input" value="<?= htmlspecialchars($user['name']) ?>" disabled style="background: #F1F5F9;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-input" value="<?= htmlspecialchars($user['email']) ?>" disabled style="background: #F1F5F9;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-input" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+1 (555) 000-0000">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Default Shipping Address</label>
                        <textarea name="address" rows="3" class="form-input" placeholder="742 Evergreen Terrace, Springfield, OR"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn-secondary" style="width: 100%; justify-content: center; font-weight: 700;">
                        Save Profile Changes
                    </button>
                </form>
            </div>
        </div>

    </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

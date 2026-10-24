<?php
require_once __DIR__ . '/../includes/config.php';
require_admin('login.php');

$orders = get_orders();
$reviews = get_reviews();
$messages = get_messages();
$users = get_users();

// Handle Order Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_order_status') {
    $orderId = trim($_POST['order_id'] ?? '');
    $newStatus = trim($_POST['new_status'] ?? '');
    
    $statusLabels = [
        'placed' => 'Order Placed',
        'in_production' => 'In Production',
        'shipped' => 'Shipped & In Transit',
        'delivered' => 'Delivered'
    ];

    $label = $statusLabels[$newStatus] ?? ucfirst($newStatus);

    if (update_order_status($orderId, $newStatus, $label)) {
        // Also update timeline
        $allOrders = get_orders();
        foreach ($allOrders as &$o) {
            if ($o['id'] === $orderId) {
                if (!isset($o['timeline'])) $o['timeline'] = [];
                // Update timeline steps
                if ($newStatus === 'in_production') {
                    $o['timeline'][1]['done'] = true;
                    $o['timeline'][2]['done'] = true;
                    $o['timeline'][2]['time'] = date('M d, Y - h:i A');
                } elseif ($newStatus === 'shipped') {
                    $o['timeline'][1]['done'] = true;
                    $o['timeline'][2]['done'] = true;
                    $o['timeline'][3]['done'] = true;
                    $o['timeline'][4]['done'] = true;
                    $o['timeline'][4]['time'] = date('M d, Y - h:i A');
                } elseif ($newStatus === 'delivered') {
                    foreach ($o['timeline'] as &$t) { $t['done'] = true; }
                    $o['timeline'][5]['time'] = date('M d, Y - h:i A');
                }
                break;
            }
        }
        save_json('orders.json', $allOrders);
        set_flash('success', "Order $orderId status updated to $label.");
    }
    header('Location: index.php');
    exit;
}

// Analytics calculations
$totalRevenue = array_reduce($orders, fn($sum, $o) => $sum + floatval($o['total'] ?? 0), 0);
$inProductionCount = count(array_filter($orders, fn($o) => in_array($o['status'], ['placed', 'in_production'])));
$customerCount = count(array_filter($users, fn($u) => ($u['role'] ?? '') !== 'admin'));

$pageTitle = 'Admin Management Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .admin-nav { background: #0F172A; color: white; padding: 0.85rem 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .admin-brand { display: flex; align-items: center; gap: 0.5rem; font-weight: 800; font-size: 1.15rem; color: white; text-decoration: none; }
        .admin-nav-actions { display: flex; align-items: center; gap: 1rem; }
        .admin-container { max-width: 1400px; margin: 1.5rem auto; padding: 0 1.5rem 3rem; }
        .admin-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem; }
        .admin-table-card { background: white; border: 1px solid #E2E8F0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .table-responsive { width: 100%; overflow-x: auto; }
        .admin-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; }
        .admin-table th { background: #F8FAFC; color: #475569; font-weight: 700; padding: 0.85rem 1rem; border-bottom: 1px solid #E2E8F0; }
        .admin-table td { padding: 0.85rem 1rem; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
        .admin-table tr:hover { background-color: #F8FAFC; }
        .status-select { padding: 0.35rem 0.6rem; border-radius: 6px; border: 1px solid #CBD5E1; font-size: 0.85rem; font-weight: 600; cursor: pointer; }
    </style>
</head>
<body style="background-color: #F1F5F9;">

    <!-- Admin Navigation Bar -->
    <header class="admin-nav">
        <a href="index.php" class="admin-brand">
            <span>🎁</span>
            <span>CraftGift Studio &bull; Admin Console</span>
        </a>
        <div class="admin-nav-actions">
            <span style="font-size: 0.85rem; color: #94A3B8;">Logged in as: <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong></span>
            <a href="../index.php" class="btn-secondary" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">View Live Store &rarr;</a>
            <a href="../logout.php" style="color: #EF4444; font-size: 0.85rem; font-weight: 600; text-decoration: none;">Sign Out</a>
        </div>
    </header>

    <div class="admin-container">
        
        <?php $flash = get_flash(); if ($flash): ?>
            <div class="flash-banner flash-<?= htmlspecialchars($flash['type']) ?>" style="margin-bottom: 1.5rem;">
                <div class="flash-inner">
                    <span><?= $flash['type'] === 'success' ? '✓' : '⚠️' ?></span>
                    <span><?= htmlspecialchars($flash['message']) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- KPI Metrics -->
        <div class="admin-stats-grid">
            <div class="stat-card">
                <div class="stat-icon-circle" style="background: #ECFDF5; color: #059669;">💰</div>
                <div>
                    <div class="stat-val">$<?= number_format($totalRevenue, 2) ?></div>
                    <div class="stat-label">Total Custom Sales</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-circle" style="background: #EEF2FF; color: #4F46E5;">📦</div>
                <div>
                    <div class="stat-val"><?= count($orders) ?></div>
                    <div class="stat-label">Total Orders Placed</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-circle" style="background: #FEF3C7; color: #D97706;">⚙️</div>
                <div>
                    <div class="stat-val"><?= $inProductionCount ?></div>
                    <div class="stat-label">Workshop In-Production</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-circle" style="background: #FCE7F3; color: #DB2777;">👥</div>
                <div>
                    <div class="stat-val"><?= $customerCount ?></div>
                    <div class="stat-label">Registered Customers</div>
                </div>
            </div>
        </div>

        <!-- Orders Management -->
        <div class="admin-table-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <div>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #0F172A;">All Customer Gift Orders</h3>
                    <p style="font-size: 0.85rem; color: #64748B;">Inspect uploaded custom photos, engraved messages, and update crafting/dispatch status.</p>
                </div>
                <span class="badge-chip" style="background: #EEF2FF; color: #4F46E5;"><?= count($orders) ?> Total Orders</span>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Customized Item Details</th>
                            <th>Total</th>
                            <th>Status Control</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $ord): ?>
                            <tr>
                                <td style="font-weight: 800; color: #4F46E5;">
                                    <?= htmlspecialchars($ord['id']) ?><br>
                                    <span style="font-size: 0.75rem; color: #64748B; font-weight: 400;"><?= date('M d, Y', strtotime($ord['created_at'])) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($ord['customer_name'] ?? 'Guest') ?></strong><br>
                                    <span style="font-size: 0.8rem; color: #64748B;"><?= htmlspecialchars($ord['customer_email'] ?? '') ?></span>
                                </td>
                                <td>
                                    <?php foreach ($ord['items'] as $item): ?>
                                        <div style="margin-bottom: 0.4rem;">
                                            <strong><?= htmlspecialchars($item['product_name'] ?? $item['productName'] ?? 'Gift') ?></strong> (x<?= $item['quantity'] ?? 1 ?>)<br>
                                            <span style="font-size: 0.8rem; color: #059669; font-weight: 600;">Engraved Name:</span> 
                                            <span style="font-size: 0.8rem;">"<?= htmlspecialchars($item['customization']['name'] ?? 'None') ?>"</span><br>
                                            <?php if (!empty($item['customization']['message'])): ?>
                                                <span style="font-size: 0.78rem; color: #64748B; font-style: italic;">“<?= htmlspecialchars($item['customization']['message']) ?>”</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <td style="font-weight: 800; font-size: 1rem; color: #0F172A;">
                                    $<?= number_format(floatval($ord['total']), 2) ?>
                                </td>
                                <td>
                                    <form action="index.php" method="POST" style="display: flex; gap: 0.4rem; align-items: center;">
                                        <input type="hidden" name="action" value="update_order_status">
                                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($ord['id']) ?>">
                                        <select name="new_status" class="status-select">
                                            <option value="placed" <?= ($ord['status'] ?? '') === 'placed' ? 'selected' : '' ?>>Order Placed</option>
                                            <option value="in_production" <?= ($ord['status'] ?? '') === 'in_production' ? 'selected' : '' ?>>In Production</option>
                                            <option value="shipped" <?= ($ord['status'] ?? '') === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                            <option value="delivered" <?= ($ord['status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                        </select>
                                        <button type="submit" class="btn-secondary" style="padding: 0.35rem 0.6rem; font-size: 0.78rem;">
                                            Update
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a href="../tracking.php?order_id=<?= urlencode($ord['id']) ?>" target="_blank" class="btn-secondary" style="font-size: 0.8rem; padding: 0.35rem 0.65rem;">
                                        View Tracking &rarr;
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Customer Inquiries & Messages Table -->
        <div class="admin-table-card">
            <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 1rem;">Customer Inquiries (Contact Form Submissions)</h3>
            
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name &amp; Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($messages)): ?>
                            <tr><td colspan="5" style="text-align: center; color: #64748B;">No inquiries yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($messages as $m): ?>
                                <tr>
                                    <td style="color: #64748B; font-size: 0.8rem;"><?= htmlspecialchars($m['created_at']) ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($m['name']) ?></strong><br>
                                        <a href="mailto:<?= htmlspecialchars($m['email']) ?>" style="font-size: 0.8rem; color: #4F46E5;"><?= htmlspecialchars($m['email']) ?></a>
                                    </td>
                                    <td style="font-weight: 600;"><?= htmlspecialchars($m['subject']) ?></td>
                                    <td style="max-width: 350px; font-size: 0.85rem; color: #334155;"><?= htmlspecialchars($m['message']) ?></td>
                                    <td>
                                        <span class="badge-status-delivered" style="font-size: 0.75rem;">
                                            <?= htmlspecialchars(ucfirst($m['status'] ?? 'new')) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>

<?php
$pageTitle = 'Order Tracking — Live Production & Delivery Status';
require_once __DIR__ . '/includes/header.php';

$searchOrderId = trim($_GET['order_id'] ?? '');
$order = null;

if (!empty($searchOrderId)) {
    $order = get_order_by_id($searchOrderId);
}

// If no order searched yet, default to the most recent sample order
if (!$order) {
    $allOrders = get_orders();
    if (!empty($allOrders)) {
        $order = $allOrders[0];
        $searchOrderId = $order['id'];
    }
}
?>

<div class="page-header-banner">
    <div class="page-header-inner">
        <span class="hero-badge">📦 Live Crafting &amp; Delivery Tracking</span>
        <h1 class="page-title">Order Status Tracker</h1>
        <p class="page-subtitle">Track your customized gift through design approval, workshop crafting, and door delivery.</p>
    </div>
</div>

<main class="section-container">
    
    <!-- Order Search Form -->
    <div class="track-search-card">
        <form action="tracking.php" method="GET" class="track-form-inline">
            <div style="flex: 1;">
                <label style="font-size: 0.85rem; font-weight: 700; color: #475569; display: block; margin-bottom: 0.35rem;">
                    Enter Your Order ID or Tracking Number:
                </label>
                <input type="text" name="order_id" value="<?= htmlspecialchars($searchOrderId) ?>" placeholder="e.g. ORD-739201" class="form-input" required>
            </div>
            <button type="submit" class="save-cta-btn" style="width: auto; padding: 0.75rem 2rem; margin-top: 1.4rem;">
                Track My Gift 🔍
            </button>
        </form>

        <div style="font-size: 0.8rem; color: #64748B; margin-top: 0.75rem;">
            💡 Quick Demo Orders to test: 
            <a href="tracking.php?order_id=ORD-739201" style="color: #4F46E5; font-weight: 600; text-decoration: underline;">ORD-739201 (In Production)</a> • 
            <a href="tracking.php?order_id=ORD-512498" style="color: #4F46E5; font-weight: 600; text-decoration: underline;">ORD-512498 (Delivered)</a>
        </div>
    </div>

    <?php if ($order): ?>
        
        <!-- Order Header Badge & Meta -->
        <div class="order-info-card">
            <div class="order-meta-grid">
                <div>
                    <span class="order-meta-label">Order Number</span>
                    <span class="order-meta-val text-primary"><?= htmlspecialchars($order['id']) ?></span>
                </div>
                <div>
                    <span class="order-meta-label">Tracking Number</span>
                    <span class="order-meta-val"><?= htmlspecialchars($order['tracking_number'] ?? 'TRK-PENDING') ?></span>
                </div>
                <div>
                    <span class="order-meta-label">Carrier</span>
                    <span class="order-meta-val"><?= htmlspecialchars($order['carrier'] ?? 'FedEx Custom') ?></span>
                </div>
                <div>
                    <span class="order-meta-label">Estimated Delivery</span>
                    <span class="order-meta-val text-success">📅 <?= htmlspecialchars($order['estimated_delivery'] ?? '3-5 Business Days') ?></span>
                </div>
            </div>

            <!-- Visual Progress Timeline -->
            <div class="tracking-timeline-box">
                <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>Workshop &amp; Delivery Progress</span>
                    <span class="badge-status-<?= htmlspecialchars($order['status']) ?>">
                        <?= htmlspecialchars($order['status_label'] ?? ucfirst($order['status'])) ?>
                    </span>
                </h3>

                <div class="timeline-stepper">
                    <?php 
                    $timeline = $order['timeline'] ?? [
                        ['step' => 'Order Placed', 'time' => $order['created_at'], 'done' => true, 'note' => 'Order verified.'],
                        ['step' => 'Customization Approved', 'time' => 'Complete', 'done' => true, 'note' => 'Design approved.'],
                        ['step' => 'In Production', 'time' => 'In Progress', 'done' => true, 'note' => 'Currently crafting.'],
                        ['step' => 'Quality Inspection', 'time' => 'Pending', 'done' => false, 'note' => 'Upcoming.'],
                        ['step' => 'Shipped', 'time' => 'Pending', 'done' => false, 'note' => 'Upcoming.'],
                        ['step' => 'Delivered', 'time' => 'Pending', 'done' => false, 'note' => 'Upcoming.']
                    ];
                    foreach ($timeline as $idx => $t): 
                    ?>
                        <div class="timeline-step <?= !empty($t['done']) ? 'step-completed' : '' ?>">
                            <div class="step-indicator">
                                <?= !empty($t['done']) ? '✓' : ($idx + 1) ?>
                            </div>
                            <div class="step-content">
                                <div class="step-title-text"><?= htmlspecialchars($t['step']) ?></div>
                                <div class="step-time"><?= htmlspecialchars($t['time']) ?></div>
                                <?php if (!empty($t['note'])): ?>
                                    <div class="step-note"><?= htmlspecialchars($t['note']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Customized Items In This Order -->
            <div class="order-items-box">
                <h4 style="font-weight: 800; margin-bottom: 1rem; font-size: 1.05rem;">Customized Items in This Order:</h4>

                <div class="order-items-grid">
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="order-item-detail-card">
                            <div class="order-item-thumb-box">
                                <?php if (!empty($item['thumbnail'])): ?>
                                    <img src="<?= htmlspecialchars($item['thumbnail']) ?>" alt="Customized Gift" class="order-item-img">
                                <?php else: ?>
                                    <div class="order-item-placeholder">🎁</div>
                                <?php endif; ?>
                            </div>

                            <div class="order-item-info-box">
                                <div style="font-weight: 800; font-size: 1rem; color: #0F172A;"><?= htmlspecialchars($item['product_name'] ?? $item['productName'] ?? 'Customized Gift') ?></div>
                                
                                <div class="custom-specs-badge-box">
                                    <div class="spec-line">
                                        <span class="spec-k">Recipient Name:</span> 
                                        <span class="spec-v">"<?= htmlspecialchars($item['customization']['name'] ?? 'N/A') ?>"</span>
                                    </div>
                                    <?php if (!empty($item['customization']['message'])): ?>
                                        <div class="spec-line">
                                            <span class="spec-k">Engraved Message:</span> 
                                            <span class="spec-v italic">“<?= htmlspecialchars($item['customization']['message']) ?>”</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['customization']['date'])): ?>
                                        <div class="spec-line">
                                            <span class="spec-k">Date / Subtitle:</span> 
                                            <span class="spec-v"><?= htmlspecialchars($item['customization']['date']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="spec-line">
                                        <span class="spec-k">Personalized Photo:</span> 
                                        <span class="spec-v text-success">✓ Attached &amp; Sent to Studio Workshop</span>
                                    </div>
                                </div>

                                <div style="margin-top: 0.5rem; font-size: 0.9rem; font-weight: 700; color: #4F46E5;">
                                    Qty: <?= $item['quantity'] ?? 1 ?> • $<?= number_format(floatval($item['price'] ?? $item['unitPrice'] ?? 0), 2) ?> each
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Shipping Address & Total -->
                <div class="order-footer-details">
                    <div>
                        <div style="font-weight: 700; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Delivery Address:</div>
                        <div style="font-size: 0.95rem; font-weight: 600; color: #0F172A; margin-top: 0.25rem;">
                            <?= htmlspecialchars($order['customer_name'] ?? '') ?><br>
                            <?= nl2br(htmlspecialchars($order['shipping_address'] ?? '')) ?><br>
                            📞 <?= htmlspecialchars($order['customer_phone'] ?? '') ?>
                        </div>
                    </div>

                    <div style="text-align: right;">
                        <div style="font-size: 0.9rem; color: #64748B;">Total Amount Paid:</div>
                        <div style="font-size: 1.6rem; font-weight: 800; color: #0F172A;">$<?= number_format(floatval($order['total'] ?? 0), 2) ?></div>
                        <div style="font-size: 0.75rem; color: #10B981; font-weight: 600;">✓ Payment Verified via <?= htmlspecialchars($order['payment_method'] ?? 'Card') ?></div>
                    </div>
                </div>

            </div>

        </div>

    <?php else: ?>
        <div class="empty-state-box">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">❓</div>
            <h3 style="font-size: 1.25rem; font-weight: 700;">Order Not Found</h3>
            <p style="color: #64748B; margin-top: 0.25rem;">We couldn't locate an order with number "<strong><?= htmlspecialchars($searchOrderId) ?></strong>". Please check your receipt email.</p>
        </div>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/includes/config.php';

$user = current_user();

// Handle form submission via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'place_order') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $customerEmail = trim($_POST['customer_email'] ?? '');
    $customerPhone = trim($_POST['customer_phone'] ?? '');
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? 'Credit Card');
    $itemsJson = $_POST['cart_items_payload'] ?? '[]';

    $items = json_decode($itemsJson, true);
    if (!is_array($items) || empty($items)) {
        // Fallback default sample customized item if cart empty
        $items = [
            [
                'product_id' => 'ceramic-mug',
                'product_name' => 'Ceramic Keepsake Mug (11oz)',
                'price' => 499,
                'quantity' => 1,
                'product_color' => '#FFFFFF',
                'customization' => [
                    'name' => 'Custom Gift Order',
                    'message' => 'Crafted with Love',
                    'date' => date('Y-m-d'),
                    'has_photo' => true
                ]
            ]
        ];
    }

    $subtotal = 0;
    foreach ($items as $it) {
        $price = floatval($it['unitPrice'] ?? $it['price'] ?? 499);
        $qty = intval($it['quantity'] ?? 1);
        $subtotal += ($price * $qty);
    }
    $shippingFee = $subtotal >= 999 ? 0.00 : 99.00;
    $total = $subtotal + $shippingFee;

    $orderId = 'ORD-' . strtoupper(substr(uniqid(), 7, 6));
    $trackingNum = 'TRK-' . rand(1000000, 9999999) . '-US';

    $newOrder = [
        'id' => $orderId,
        'user_id' => $user ? $user['id'] : 'guest_' . time(),
        'customer_name' => $customerName,
        'customer_email' => $customerEmail,
        'customer_phone' => $customerPhone,
        'shipping_address' => $shippingAddress,
        'status' => 'placed',
        'status_label' => 'Order Placed',
        'tracking_number' => $trackingNum,
        'carrier' => 'FedEx Express Custom',
        'estimated_delivery' => date('M d, Y', strtotime('+4 days')),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
        'payment_method' => $paymentMethod,
        'subtotal' => $subtotal,
        'shipping_fee' => $shippingFee,
        'discount' => 0.00,
        'total' => $total,
        'items' => $items,
        'timeline' => [
            [ 'step' => 'Order Placed', 'time' => date('M d, Y - h:i A'), 'done' => true, 'note' => 'Order confirmed and specifications logged.' ],
            [ 'step' => 'Customization Approved', 'time' => 'In Queue', 'done' => false, 'note' => 'High-res artwork sent to laser engraving queue.' ],
            [ 'step' => 'In Production', 'time' => 'Pending', 'done' => false, 'note' => 'Mug/plaque sublimation heat pressed.' ],
            [ 'step' => 'Quality Inspection', 'time' => 'Pending', 'done' => false, 'note' => 'Optical test and protective gift boxing.' ],
            [ 'step' => 'Shipped & In Transit', 'time' => 'Pending', 'done' => false, 'note' => 'Carrier pickup scheduled.' ],
            [ 'step' => 'Delivered', 'time' => 'Pending', 'done' => false, 'note' => 'Expected delivery by ' . date('M d, Y', strtotime('+4 days')) ]
        ]
    ];

    save_order($newOrder);
    set_flash('success', "Order $orderId placed successfully! Track your live crafting status below.");
    header("Location: tracking.php?order_id=$orderId");
    exit;
}

$pageTitle = 'Secure Checkout — Customized Gift Order';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header-banner" style="padding: 1.25rem 0;">
    <div class="page-header-inner">
        <span class="hero-badge">🔒 Encrypted 256-Bit Checkout</span>
        <h1 class="page-title">Finalize Your Personalized Order</h1>
    </div>
</div>

<main class="section-container">
    
    <div class="cart-page-layout">
        
        <!-- Left: Shipping & Billing Form -->
        <div class="cart-items-column">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">1. Shipping Information</h3>
                </div>

                <form id="checkoutForm" action="checkout.php" method="POST">
                    <input type="hidden" name="action" value="place_order">
                    <input type="hidden" name="cart_items_payload" id="cartPayloadInput">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="customer_name" class="form-input" value="<?= $user ? htmlspecialchars($user['name']) : 'Sarah Jenkins' ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address (for order updates) *</label>
                            <input type="email" name="customer_email" class="form-input" value="<?= $user ? htmlspecialchars($user['email']) : 'sarah@example.com' ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number *</label>
                        <input type="tel" name="customer_phone" class="form-input" value="<?= $user ? htmlspecialchars($user['phone'] ?? '') : '+1 (555) 234-5678' ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Street Address &amp; Suite/Apt *</label>
                        <input type="text" name="shipping_address" class="form-input" value="<?= $user ? htmlspecialchars($user['address'] ?? '') : '742 Evergreen Terrace, Springfield, OR 97477' ?>" required>
                    </div>

                    <div class="card-header" style="margin-top: 1.5rem;">
                        <h3 class="card-title">2. Payment Method (Demo Simulation)</h3>
                    </div>

                    <div class="form-group">
                        <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                            <label style="border: 2px solid #4F46E5; background: #EEF2FF; padding: 0.75rem 1rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; flex: 1;">
                                <input type="radio" name="payment_method" value="Credit Card" checked>
                                <span style="font-weight: 600;">💳 Credit / Debit Card</span>
                            </label>
                            <label style="border: 1px solid #CBD5E1; padding: 0.75rem 1rem; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; flex: 1;">
                                <input type="radio" name="payment_method" value="PayPal">
                                <span style="font-weight: 600;">🅿️ PayPal</span>
                            </label>
                        </div>
                    </div>

                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 1rem; margin-top: 1rem; font-size: 0.85rem; color: #64748B;">
                        🔒 <strong>Instant Order Simulation:</strong> This demo automatically assigns a live production tracker (<code>ORD-XXXXXX</code>) with status updates so you can test the Order Tracking feature immediately!
                    </div>

                    <button type="submit" class="save-cta-btn" style="margin-top: 1.5rem; padding: 1rem;">
                        Complete Order &amp; Start Tracking 🚀
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Order Preview Summary -->
        <div class="cart-summary-column">
            <div class="summary-card">
                <h3 class="summary-title">Order Items Preview</h3>
                <div id="checkoutItemsList" style="max-height: 250px; overflow-y: auto; margin-bottom: 1rem;">
                    <!-- Injected by JS -->
                </div>

                <div class="summary-rows">
                    <div class="summary-row">
                        <span class="summary-label">Subtotal</span>
                        <span class="summary-val" id="checkoutSubtotal">₹0</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Shipping</span>
                        <span class="summary-val text-success" id="checkoutShipping">FREE</span>
                    </div>
                    <div class="summary-row total-row">
                        <span class="total-label">Total to Pay</span>
                        <span class="total-amount" id="checkoutTotal">₹0</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

</main>

<script src="js/cart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const items = window.cartManager.items;
    const payloadInput = document.getElementById('cartPayloadInput');
    const itemsList = document.getElementById('checkoutItemsList');
    const subtotalEl = document.getElementById('checkoutSubtotal');
    const totalEl = document.getElementById('checkoutTotal');
    const form = document.getElementById('checkoutForm');

    payloadInput.value = JSON.stringify(items);

    if (items.length === 0) {
        itemsList.innerHTML = '<div style="font-size: 0.85rem; color: #64748B;">Custom item ready for checkout.</div>';
        subtotalEl.textContent = '₹499';
        totalEl.textContent = '₹499';
    } else {
        itemsList.innerHTML = '';
        items.forEach(it => {
            const el = document.createElement('div');
            el.style = 'display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.75rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;';
            el.innerHTML = `
                <img src="${it.thumbnail}" style="width: 50px; height: 50px; border-radius: 6px; object-fit: cover; border: 1px solid #E2E8F0;">
                <div style="flex: 1; font-size: 0.85rem;">
                    <div style="font-weight: 700; color: #0F172A;">${it.productName} (x${it.quantity})</div>
                    <div style="color: #4F46E5; font-size: 0.75rem;">"${it.customization.name || 'Personalized'}"</div>
                </div>
                <div style="font-weight: 700; font-size: 0.9rem;">₹${Math.round(it.unitPrice * it.quantity).toLocaleString('en-IN')}</div>
            `;
            itemsList.appendChild(el);
        });

        const subtotal = window.cartManager.getSubtotal();
        const total = window.cartManager.getTotal();
        subtotalEl.textContent = `₹${Math.round(subtotal).toLocaleString('en-IN')}`;
        totalEl.textContent = `₹${Math.round(total).toLocaleString('en-IN')}`;
    }

    form.addEventListener('submit', () => {
        // Clear client-side cart after placing order
        setTimeout(() => {
            window.cartManager.clearCart();
        }, 100);
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

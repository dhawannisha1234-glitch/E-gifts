<?php
require_once __DIR__ . '/includes/config.php';

// Handle coupon application
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply_coupon') {
    $code = strtoupper(trim($_POST['coupon_code'] ?? ''));
    if ($code === 'GIFT10') {
        $_SESSION['promo_discount'] = 0.10; // 10%
        $_SESSION['promo_code'] = 'GIFT10';
        set_flash('success', 'Coupon GIFT10 applied! 10% discount deducted.');
    } elseif ($code === 'WELCOME5') {
        $_SESSION['promo_discount'] = 5.00; // $5 fixed
        $_SESSION['promo_code'] = 'WELCOME5';
        set_flash('success', 'Coupon WELCOME5 applied! $5 discount deducted.');
    } else {
        set_flash('error', 'Invalid promo code. Try GIFT10 or WELCOME5.');
    }
    header('Location: cart.php');
    exit;
}

// Handle remove coupon
if (isset($_GET['remove_coupon'])) {
    unset($_SESSION['promo_discount']);
    unset($_SESSION['promo_code']);
    set_flash('success', 'Promo code removed.');
    header('Location: cart.php');
    exit;
}

$pageTitle = 'Your Shopping Cart — Customized Gifts';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header-banner">
    <div class="page-header-inner">
        <span class="hero-badge">🛒 Review Your Custom Order</span>
        <h1 class="page-title">Shopping Cart</h1>
        <p class="page-subtitle">Inspect your custom text, uploaded photos, and prepare for crafting.</p>
    </div>
</div>

<main class="section-container" id="cartAppContainer">
    
    <div class="cart-page-layout">
        
        <!-- Left: Cart Items List -->
        <div class="cart-items-column">
            
            <div class="cart-items-card">
                <div class="cart-table-header">
                    <span style="flex: 2;">Customized Item</span>
                    <span style="flex: 1; text-align: center;">Price</span>
                    <span style="flex: 1; text-align: center;">Quantity</span>
                    <span style="flex: 1; text-align: right;">Total</span>
                    <span style="width: 40px;"></span>
                </div>

                <div id="cartPageItemsList" class="cart-table-body">
                    <!-- Populated dynamically via JS from localStorage/session -->
                </div>
            </div>

            <div class="cart-extra-actions">
                <a href="categories.php" class="btn-secondary">
                    &larr; Continue Shopping
                </a>
                <button id="clearCartBtn" class="btn-secondary" style="color: #EF4444;">
                    🗑️ Clear Entire Cart
                </button>
            </div>

        </div>

        <!-- Right: Order Summary -->
        <div class="cart-summary-column">
            <div class="summary-card">
                <h3 class="summary-title">Order Summary</h3>

                <div class="summary-rows">
                    <div class="summary-row">
                        <span class="summary-label">Items Subtotal</span>
                        <span class="summary-val" id="pageSubtotal">$0.00</span>
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Standard Shipping</span>
                        <span class="summary-val text-success" id="pageShipping">FREE</span>
                    </div>

                    <div class="summary-row" id="discountRow" style="display: none;">
                        <span class="summary-label" style="color: #10B981;">Promo Discount (<span id="promoCodeLabel"></span>)</span>
                        <span class="summary-val" style="color: #10B981;" id="pageDiscount">-$0.00</span>
                    </div>

                    <div class="summary-row total-row">
                        <span class="total-label">Estimated Total</span>
                        <span class="total-amount" id="pageTotal">$0.00</span>
                    </div>
                </div>

                <!-- Promo Code Form -->
                <div class="promo-box">
                    <label class="promo-label">Have a Promo Code?</label>
                    <div class="promo-input-group">
                        <input type="text" id="couponInput" placeholder="Try GIFT10" class="form-input promo-input" style="text-transform: uppercase;">
                        <button type="button" id="applyCouponBtn" class="btn-promo">Apply</button>
                    </div>
                    <div class="promo-hint">💡 Tip: Use coupon <strong>GIFT10</strong> for 10% off today!</div>
                </div>

                <!-- Proceed to Checkout Button -->
                <a href="checkout.php" class="btn-checkout-primary" id="checkoutBtnMain">
                    <span>Proceed to Checkout 🔒</span>
                </a>

                <div class="guarantee-box">
                    <div class="guarantee-item">🛡️ <strong>100% Satisfaction Guarantee</strong></div>
                    <div class="guarantee-item">🚚 <strong>Safe Delivery Guarantee</strong></div>
                    <div class="guarantee-item">🔒 <strong>256-Bit SSL Encrypted Checkout</strong></div>
                </div>
            </div>
        </div>

    </div>

</main>

<script src="js/cart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const listEl = document.getElementById('cartPageItemsList');
    const subtotalEl = document.getElementById('pageSubtotal');
    const shippingEl = document.getElementById('pageShipping');
    const discountEl = document.getElementById('pageDiscount');
    const discountRow = document.getElementById('discountRow');
    const promoCodeLabel = document.getElementById('promoCodeLabel');
    const totalEl = document.getElementById('pageTotal');
    const checkoutBtn = document.getElementById('checkoutBtnMain');
    const clearBtn = document.getElementById('clearCartBtn');

    let appliedDiscountPercent = <?= isset($_SESSION['promo_discount']) ? floatval($_SESSION['promo_discount']) : 0 ?>;
    let promoCodeName = '<?= htmlspecialchars($_SESSION['promo_code'] ?? '') ?>';

    function renderCartPage() {
        const items = window.cartManager.items;

        if (items.length === 0) {
            listEl.innerHTML = `
                <div style="text-align: center; padding: 4rem 1rem;">
                    <div style="font-size: 3.5rem; margin-bottom: 0.5rem;">🛒</div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #1E293B;">Your Shopping Cart is Empty</h3>
                    <p style="color: #64748B; margin-top: 0.25rem;">Looks like you haven't customized any gifts yet!</p>
                    <a href="customizer.php" class="save-cta-btn" style="display: inline-flex; width: auto; margin-top: 1.25rem; padding: 0.75rem 1.5rem; text-decoration: none;">
                        Launch Customizer Studio 🎁
                    </a>
                </div>
            `;
            subtotalEl.textContent = '$0.00';
            shippingEl.textContent = '$0.00';
            totalEl.textContent = '$0.00';
            discountRow.style.display = 'none';
            checkoutBtn.style.opacity = '0.5';
            checkoutBtn.style.pointerEvents = 'none';
            return;
        }

        checkoutBtn.style.opacity = '1';
        checkoutBtn.style.pointerEvents = 'auto';

        listEl.innerHTML = '';
        items.forEach(item => {
            const row = document.createElement('div');
            row.className = 'cart-table-row';
            row.innerHTML = `
                <div class="cart-item-col-main" style="flex: 2; display: flex; gap: 1rem; align-items: center;">
                    <img src="${item.thumbnail}" alt="${item.productName}" class="cart-item-image">
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #0F172A;">${item.productName}</div>
                        <div style="font-size: 0.8rem; color: #4F46E5; margin-top: 0.2rem;">
                            <span style="background: #EEF2FF; padding: 0.1rem 0.4rem; border-radius: 4px; font-weight: 600;">Customized:</span> 
                            "${item.customization.name || 'Untitled'}"
                        </div>
                        ${item.customization.message ? `<div style="font-size: 0.76rem; color: #64748B; font-style: italic;">“${item.customization.message}”</div>` : ''}
                        <div style="font-size: 0.72rem; color: #94A3B8; margin-top: 0.2rem;">
                            Font: ${item.customization.font ? item.customization.font.split(',')[0].replace(/'/g, '') : 'Default'} 
                            • ${item.customization.hasPhoto ? '📷 Photo Included' : 'No Photo'}
                        </div>
                    </div>
                </div>

                <div style="flex: 1; text-align: center; font-weight: 600; color: #64748B;">
                    $${item.unitPrice.toFixed(2)}
                </div>

                <div style="flex: 1; display: flex; justify-content: center;">
                    <div class="qty-controls" style="border: 1px solid #CBD5E1; border-radius: 6px; display: inline-flex;">
                        <button class="qty-btn" onclick="updateItemQty('${item.id}', -1)">-</button>
                        <span class="qty-value">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateItemQty('${item.id}', 1)">+</button>
                    </div>
                </div>

                <div style="flex: 1; text-align: right; font-weight: 800; color: #0F172A; font-size: 1.05rem;">
                    $${(item.unitPrice * item.quantity).toFixed(2)}
                </div>

                <div style="width: 40px; text-align: right;">
                    <button onclick="removeItem('${item.id}')" title="Remove Item" style="background: none; border: none; font-size: 1.25rem; color: #94A3B8; cursor: pointer;">&times;</button>
                </div>
            `;
            listEl.appendChild(row);
        });

        // Calculate summary
        const subtotal = window.cartManager.getSubtotal();
        const shipping = subtotal >= 50 || subtotal === 0 ? 0 : 4.99;
        let discount = 0;
        if (appliedDiscountPercent > 0) {
            discount = subtotal * appliedDiscountPercent;
            discountRow.style.display = 'flex';
            promoCodeLabel.textContent = promoCodeName;
            discountEl.textContent = `-$${discount.toFixed(2)}`;
        } else {
            discountRow.style.display = 'none';
        }

        const total = Math.max(0, subtotal - discount + shipping);

        subtotalEl.textContent = `$${subtotal.toFixed(2)}`;
        shippingEl.textContent = shipping === 0 ? 'FREE' : `$${shipping.toFixed(2)}`;
        totalEl.textContent = `$${total.toFixed(2)}`;
    }

    window.updateItemQty = (id, delta) => {
        window.cartManager.updateQuantity(id, delta);
        renderCartPage();
    };

    window.removeItem = (id) => {
        window.cartManager.removeItem(id);
        renderCartPage();
    };

    clearBtn.addEventListener('click', () => {
        if (confirm('Are you sure you want to empty your cart?')) {
            window.cartManager.clearCart();
            renderCartPage();
        }
    });

    document.getElementById('applyCouponBtn').addEventListener('click', () => {
        const val = document.getElementById('couponInput').value.trim().toUpperCase();
        if (val === 'GIFT10') {
            appliedDiscountPercent = 0.10;
            promoCodeName = 'GIFT10 (10% OFF)';
            alert('Promo code GIFT10 applied! 10% discount has been applied.');
            renderCartPage();
        } else if (val === 'WELCOME5') {
            appliedDiscountPercent = 0.05;
            promoCodeName = 'WELCOME5 (5% OFF)';
            alert('Promo code WELCOME5 applied! 5% discount has been applied.');
            renderCartPage();
        } else {
            alert('Invalid promo code. Try entering GIFT10 or WELCOME5.');
        }
    });

    renderCartPage();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

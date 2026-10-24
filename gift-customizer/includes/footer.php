<?php
/**
 * Global Page Footer
 */
?>
    <!-- Global Footer -->
    <footer class="app-footer">
        <div class="footer-inner">
            <div class="footer-grid">
                
                <!-- Col 1: Brand Info -->
                <div class="footer-col brand-col">
                    <div class="footer-brand">
                        <span class="footer-brand-icon">🎁</span>
                        <span class="footer-brand-title">CraftGift Studio</span>
                    </div>
                    <p class="footer-desc">
                        Creating heartfelt moments that last forever. We craft premium personalized gifts, custom drinkware, LED acrylic lamps, and photo keepsakes with instant live 3D preview technology.
                    </p>
                    <div class="trust-pill">
                        <span>🛡️ 100% Satisfaction Guarantee</span>
                    </div>
                </div>

                <!-- Col 2: Gift Categories -->
                <div class="footer-col">
                    <h4 class="footer-heading">Gift Categories</h4>
                    <ul class="footer-links">
                        <li><a href="categories.php?cat=mugs">Personalized Mugs</a></li>
                        <li><a href="categories.php?cat=tshirts">Custom T-Shirts</a></li>
                        <li><a href="categories.php?cat=frames">Photo Frames</a></li>
                        <li><a href="categories.php?cat=cushions">Custom Cushions</a></li>
                        <li><a href="categories.php?cat=lamps">Acrylic LED Lamps</a></li>
                        <li><a href="categories.php?cat=keychains">Crystal Keychains</a></li>
                    </ul>
                </div>

                <!-- Col 3: Customer Care -->
                <div class="footer-col">
                    <h4 class="footer-heading">Customer Care</h4>
                    <ul class="footer-links">
                        <li><a href="tracking.php">Track Your Order</a></li>
                        <li><a href="customizer.php">Live Customizer Studio</a></li>
                        <li><a href="cart.php">Shopping Cart</a></li>
                        <li><a href="contact.php">Help &amp; Support</a></li>
                        <li><a href="contact.php#faqs">Customization FAQs</a></li>
                    </ul>
                </div>

                <!-- Col 4: Contact & Studio -->
                <div class="footer-col">
                    <h4 class="footer-heading">Get in Touch</h4>
                    <p class="footer-contact-item">📧 support@craftgiftstudio.com</p>
                    <p class="footer-contact-item">📞 +1 (800) 555-GIFT (4438)</p>
                    <p class="footer-contact-item">📍 452 Artisan Way, Studio 7, Design District</p>
                    <p class="footer-contact-item" style="color: #10B981; font-weight: 600;">Mon - Sat: 9:00 AM - 8:00 PM EST</p>
                </div>

            </div>

            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> CraftGift Studio. All rights reserved. Powered by PHP &amp; Modern Canvas Technology.</p>
                <div class="payment-badges">
                    <span>💳 Visa</span>
                    <span>💳 Mastercard</span>
                    <span>🅿️ PayPal</span>
                    <span>🔒 SSL 256-Bit Encrypted</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Global Cart Sync Script -->
    <script>
    // Sync cart badge count with localStorage if present
    document.addEventListener('DOMContentLoaded', () => {
        const localCart = localStorage.getItem('personalized_gift_cart_v1');
        if (localCart) {
            try {
                const items = JSON.parse(localCart);
                const count = items.reduce((sum, item) => sum + (item.quantity || 1), 0);
                const badge = document.getElementById('headerCartCount');
                if (badge && count > 0 && badge.textContent === '0') {
                    badge.textContent = count;
                }
            } catch (e) {}
        }
    });
    </script>
</body>
</html>

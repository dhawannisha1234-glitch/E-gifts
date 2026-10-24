<?php
require_once __DIR__ . '/includes/config.php';

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? 'General Inquiry');
    $message = trim($_POST['message'] ?? '');

    if (!empty($name) && !empty($email) && !empty($message)) {
        $msgData = [
            'id' => 'msg_' . time(),
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'new'
        ];
        save_message($msgData);
        $successMsg = 'Thank you! Your message has been sent to our artisan team. We will get back to you within 24 hours.';
        set_flash('success', $successMsg);
    } else {
        $errorMsg = 'Please fill out your name, email, and message.';
        set_flash('error', $errorMsg);
    }
}

$pageTitle = 'Contact Us & Customization Help';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header-banner">
    <div class="page-header-inner">
        <span class="hero-badge">💬 We're Here For You</span>
        <h1 class="page-title">Contact CraftGift Studio</h1>
        <p class="page-subtitle">Have questions about photo quality, custom engraving, or bulk orders? We'd love to help!</p>
    </div>
</div>

<main class="section-container">
    
    <div class="contact-grid-layout">
        
        <!-- Left: Contact Form -->
        <div class="contact-form-side">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Send Us a Message</h3>
                </div>

                <form action="contact.php" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Your Name *</label>
                            <input type="text" name="name" class="form-input" placeholder="e.g. Sarah Jenkins" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-input" placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Phone Number (Optional)</label>
                            <input type="tel" name="phone" class="form-input" placeholder="+1 (555) 000-0000" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Inquiry Subject *</label>
                            <select name="subject" class="form-input font-select" required>
                                <option value="Custom Design Question">Custom Design Question</option>
                                <option value="Order Status & Delivery">Order Status &amp; Delivery</option>
                                <option value="Bulk / Corporate Gift Order">Bulk / Corporate Gift Order</option>
                                <option value="Product Material / Quality">Product Material / Quality</option>
                                <option value="Other">Other Inquiry</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Your Message *</label>
                        <textarea name="message" rows="5" class="form-input" placeholder="Describe your question or gift idea in detail..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="save-cta-btn" style="padding: 0.85rem; width: 100%;">
                        Send Message 📨
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Studio Info Cards -->
        <div class="contact-info-side">
            <div class="info-card-pill">
                <div class="info-icon">📧</div>
                <div>
                    <div class="info-title">Email Us Directly</div>
                    <div class="info-desc">support@craftgiftstudio.com</div>
                    <div style="font-size: 0.75rem; color: #10B981; font-weight: 600; margin-top: 0.2rem;">⚡ Fast replies within 2-4 hours</div>
                </div>
            </div>

            <div class="info-card-pill">
                <div class="info-icon">📞</div>
                <div>
                    <div class="info-title">Customer Care Hotline</div>
                    <div class="info-desc">+1 (800) 555-GIFT (4438)</div>
                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.2rem;">Mon - Sat: 9:00 AM - 8:00 PM EST</div>
                </div>
            </div>

            <div class="info-card-pill">
                <div class="info-icon">📍</div>
                <div>
                    <div class="info-title">Workshop &amp; Studio</div>
                    <div class="info-desc">452 Artisan Way, Studio 7<br>Design District, NY 10012</div>
                </div>
            </div>

            <div class="info-card-pill" style="background: #FEF3C7; border-color: #FDE68A;">
                <div class="info-icon" style="background: #F59E0B; color: white;">💡</div>
                <div>
                    <div class="info-title" style="color: #92400E;">Need Instant Preview?</div>
                    <div class="info-desc" style="color: #78350F; font-size: 0.85rem;">
                        You don't need to wait for email proofs. Our <a href="customizer.php" style="font-weight: 700; text-decoration: underline;">Live Customizer Studio</a> renders your 3D gift in real time.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Frequently Asked Questions Accordion -->
    <section class="faq-section" id="faqs" style="margin-top: 4rem;">
        <div class="section-header" style="text-align: center; margin-bottom: 2rem;">
            <div style="margin: 0 auto;">
                <span class="hero-badge">Help Center</span>
                <h2 class="section-title">Frequently Asked Questions</h2>
                <p class="section-subtitle">Everything you need to know about our photo upload, engraving, and shipping.</p>
            </div>
        </div>

        <div class="faq-container">
            <details class="faq-item" open>
                <summary class="faq-question">How does photo upload and customization work?</summary>
                <div class="faq-answer">
                    Simply launch our Live Customizer Studio, click "Upload Photo" or drag-and-drop any image (JPG, PNG, WebP). You can pan, zoom, rotate, and apply artistic filters. Your preview is generated in real time on a realistic 3D mockup!
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">How long does custom production and delivery take?</summary>
                <div class="faq-answer">
                    Personalized gifts are precision laser-engraved or sublimation-printed within 24 to 48 hours. Standard courier delivery takes 3 to 5 business days, and you can track every stage live on our <a href="tracking.php" style="color: #4F46E5; font-weight: 600;">Order Tracking</a> page.
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">Are the custom ceramic mugs microwave and dishwasher safe?</summary>
                <div class="faq-answer">
                    Yes! We use high-heat dye-sublimation printing where the inks penetrate the ceramic glaze at molecular levels. They are 100% microwave safe and top-rack dishwasher safe for years without fading.
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">Can I preview my custom gift before buying?</summary>
                <div class="faq-answer">
                    Absolutely! Our HTML5 canvas engine renders both a realistic 3D lighting mockup and a technical print shop template with safe margin guidelines before you add the item to your cart.
                </div>
            </details>
        </div>
    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
$pageTitle = 'Personalized Gifts Studio & Live Customizer';
require_once __DIR__ . '/includes/header.php';
$products = get_products();
$reviews = get_reviews();
?>

<!-- Hero Banner Section -->
<section class="hero-section">
    <div class="hero-inner">
        <div class="hero-content">
            <span class="hero-badge">⭐ MVP Feature #6: Live Gift Customizer</span>
            <h1 class="hero-title">Turn Treasured Memories Into <span class="gradient-text">Heartfelt Gifts</span></h1>
            <p class="hero-subtitle">
                Upload your favorite photo, add a heartfelt name or engraved message, and watch your personalized gift come to life with our instant real-time 3D preview.
            </p>
            <div class="hero-actions">
                <a href="customizer.php" class="btn-primary-large">
                    <span>✨ Launch Customizer Studio</span>
                </a>
                <a href="categories.php" class="btn-outline-large">
                    <span>Explore Gift Categories</span>
                </a>
            </div>
            <div class="hero-features">
                <div class="feature-item"><span>🚀</span> Same-Day Production</div>
                <div class="feature-item"><span>📷</span> Crisp HD Photo Print</div>
                <div class="feature-item"><span>🛡️</span> 100% Love-It Guarantee</div>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-card-stack">
                <div class="hero-preview-badge">🔥 Bestseller</div>
                <img src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=600&q=80" alt="Personalized Ceramic Mug" class="hero-image">
                <div class="hero-card-caption">
                    <div style="font-weight: 700; font-size: 0.95rem;">Classic Ceramic Keepsake Mug</div>
                    <div style="color: #64748B; font-size: 0.8rem;">Custom photo &amp; romantic engraved quote</div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                        <span style="font-weight: 800; color: #4F46E5;">₹499</span>
                        <a href="customizer.php?product=ceramic-mug" class="btn-sm-primary">Customize 🎁</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Order Tracking Search Bar -->
<section class="quick-track-section">
    <div class="quick-track-box">
        <div class="quick-track-text">
            <strong>📦 Already ordered a custom gift?</strong> Track production &amp; delivery in real time:
        </div>
        <form action="tracking.php" method="GET" class="quick-track-form">
            <input type="text" name="order_id" placeholder="Enter Order # (e.g. ORD-739201)" class="quick-track-input" required>
            <button type="submit" class="quick-track-btn">Track Order</button>
        </form>
    </div>
</section>

<!-- Gift Categories Section -->
<section class="section-container">
    <div class="section-header">
        <div>
            <h2 class="section-title">Explore Gift Categories</h2>
            <p class="section-subtitle">Curated collections tailored for every special milestone and relationship.</p>
        </div>
        <a href="categories.php" class="view-all-link">View All Categories &rarr;</a>
    </div>

    <div class="category-cards-grid">
        <a href="categories.php?cat=mugs" class="cat-card">
            <div class="cat-icon-circle" style="background: #FFE4E6; color: #E11D48;">☕</div>
            <h3 class="cat-name">Personalized Mugs</h3>
            <p class="cat-count">Ceramic Mugs, Magic Mugs &amp; Tumblers</p>
        </a>

        <a href="categories.php?cat=tshirts" class="cat-card">
            <div class="cat-icon-circle" style="background: #E0E7FF; color: #4338CA;">👕</div>
            <h3 class="cat-name">Custom T-Shirts</h3>
            <p class="cat-count">100% Cotton Bio-Washed Apparel</p>
        </a>

        <a href="categories.php?cat=frames" class="cat-card">
            <div class="cat-icon-circle" style="background: #DCFCE7; color: #15803D;">🖼️</div>
            <h3 class="cat-name">Photo Frames</h3>
            <p class="cat-count">Nordic Oak Tabletop &amp; Brass Engraved</p>
        </a>

        <a href="categories.php?cat=cushions" class="cat-card">
            <div class="cat-icon-circle" style="background: #FEF3C7; color: #D97706;">🛋️</div>
            <h3 class="cat-name">Custom Cushions</h3>
            <p class="cat-count">Plush Velvet Throw Pillows</p>
        </a>

        <a href="categories.php?cat=keychains" class="cat-card">
            <div class="cat-icon-circle" style="background: #E0F2FE; color: #0284C7;">🔑</div>
            <h3 class="cat-name">Crystal Keychains</h3>
            <p class="cat-count">3D Laser Subsurface Etching with LED</p>
        </a>

        <a href="categories.php?cat=lamps" class="cat-card">
            <div class="cat-icon-circle" style="background: #FDF4FF; color: #A855F7;">💡</div>
            <h3 class="cat-name">Acrylic LED Lamps</h3>
            <p class="cat-count">Ambient Glow Warm Night Lights</p>
        </a>
    </div>
</section>

<!-- Bestselling Customizable Gifts -->
<section class="section-container bg-alt">
    <div class="section-header">
        <div>
            <h2 class="section-title">Trending Customizable Gifts</h2>
            <p class="section-subtitle">Select any gift to preview and customize live with your photo and custom text.</p>
        </div>
        <a href="categories.php" class="view-all-link">Browse Full Catalog &rarr;</a>
    </div>

    <div class="product-cards-grid">
        <?php foreach (array_slice($products, 0, 4) as $p): ?>
            <div class="product-card">
                <?php if (!empty($p['badge'])): ?>
                    <span class="product-card-badge"><?= htmlspecialchars($p['badge']) ?></span>
                <?php endif; ?>
                
                <div class="product-card-visual">
                    <!-- Dynamic product preview icon/graphic -->
                    <div class="product-visual-circle">
                        <?php if ($p['id'] == 'ceramic-mug'): ?>☕
                        <?php elseif ($p['id'] == 'wooden-frame'): ?>🖼️
                        <?php elseif ($p['id'] == 'acrylic-lamp'): ?>✨
                        <?php else: ?>🛋️
                        <?php endif; ?>
                    </div>
                </div>

                <div class="product-card-body">
                    <div class="product-card-category"><?= htmlspecialchars($p['category_name']) ?></div>
                    <h3 class="product-card-title">
                        <a href="product-detail.php?id=<?= urlencode($p['id']) ?>">
                            <?= htmlspecialchars($p['name']) ?>
                        </a>
                    </h3>
                    
                    <div class="product-card-rating">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                        <span class="rating-num"><?= $p['rating'] ?></span>
                        <span class="reviews-count">(<?= $p['review_count'] ?> reviews)</span>
                    </div>

                    <p class="product-card-desc"><?= htmlspecialchars($p['short_description']) ?></p>

                    <div class="product-card-footer">
                        <div class="product-card-price"><?= format_price($p['price']) ?></div>
                        <div class="product-card-actions">
                            <a href="product-detail.php?id=<?= urlencode($p['id']) ?>" class="btn-card-detail" title="View Details">Details</a>
                            <a href="customizer.php?product=<?= urlencode($p['id']) ?>" class="btn-card-customize">
                                Customize 🎨
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- How Customization Works: 4 Simple Steps -->
<section class="section-container">
    <div class="section-header" style="text-align: center; margin-bottom: 2.5rem;">
        <div style="margin: 0 auto;">
            <span class="hero-badge">Simple 4-Step Process</span>
            <h2 class="section-title">How Your Custom Gift is Created</h2>
            <p class="section-subtitle">No design experience required. Our interactive customizer guides you every step.</p>
        </div>
    </div>

    <div class="steps-grid">
        <div class="step-card">
            <div class="step-badge">Step 1</div>
            <div class="step-icon">🎁</div>
            <h3 class="step-title">Choose Your Gift</h3>
            <p class="step-desc">Pick from our premium collection of mugs, wooden frames, LED acrylic lamps, and plush velvet cushions.</p>
        </div>

        <div class="step-card">
            <div class="step-badge">Step 2</div>
            <div class="step-icon">📸</div>
            <h3 class="step-title">Upload Your Photo</h3>
            <p class="step-desc">Drag and drop your favorite picture. Pan, zoom, rotate, or apply artistic vintage and black &amp; white filters.</p>
        </div>

        <div class="step-card">
            <div class="step-badge">Step 3</div>
            <div class="step-icon">✍️</div>
            <h3 class="step-title">Add Names &amp; Quotes</h3>
            <p class="step-desc">Type your recipient's name, anniversary date, or a heartfelt quote in romantic cursive or royal engraved fonts.</p>
        </div>

        <div class="step-card">
            <div class="step-badge">Step 4</div>
            <div class="step-icon">🚚</div>
            <h3 class="step-title">Instant 3D Preview &amp; Ship</h3>
            <p class="step-desc">Review your creation on a realistic 3D canvas, add to cart, and track your personalized order straight to your door!</p>
        </div>
    </div>
</section>

<!-- Customer Reviews & Ratings Showcase -->
<section class="section-container bg-alt" id="reviews">
    <div class="section-header">
        <div>
            <h2 class="section-title">Loved by Thousands of Gift Givers</h2>
            <p class="section-subtitle">Read verified customer reviews and ratings from across the country.</p>
        </div>
        <div class="overall-rating-chip">
            <span class="stars">⭐⭐⭐⭐⭐</span>
            <strong>4.9 / 5.0 Average Rating</strong>
        </div>
    </div>

    <div class="reviews-grid">
        <?php foreach ($reviews as $rev): ?>
            <div class="review-card">
                <div class="review-header">
                    <div>
                        <div class="review-author"><?= htmlspecialchars($rev['user_name']) ?></div>
                        <div class="review-meta">
                            <span class="verified-badge">✓ Verified Buyer</span>
                            <span>• <?= htmlspecialchars($rev['date']) ?></span>
                        </div>
                    </div>
                    <div class="review-stars">
                        <?= str_repeat('⭐', $rev['rating']) ?>
                    </div>
                </div>
                <h4 class="review-title">"<?= htmlspecialchars($rev['title']) ?>"</h4>
                <p class="review-comment"><?= htmlspecialchars($rev['comment']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="cta-banner">
    <div class="cta-inner">
        <h2 class="cta-title">Ready to Design Something Unforgettable?</h2>
        <p class="cta-desc">Launch the live studio now and see your personalized gift before placing your order.</p>
        <a href="customizer.php" class="btn-primary-large" style="background: white; color: #4F46E5; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
            Start Customizing Now 🎨
        </a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

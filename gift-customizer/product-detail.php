<?php
require_once __DIR__ . '/includes/config.php';

$productId = $_GET['id'] ?? 'ceramic-mug';
$product = get_product_by_id($productId);

if (!$product) {
    set_flash('error', 'Gift item not found.');
    header('Location: categories.php');
    exit;
}

// Handle new review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_review') {
    $userName = trim($_POST['user_name'] ?? '');
    $rating = intval($_POST['rating'] ?? 5);
    $title = trim($_POST['title'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    if (!empty($userName) && !empty($title) && !empty($comment)) {
        $newReview = [
            'id' => 'rev_' . time(),
            'product_id' => $product['id'],
            'user_name' => $userName,
            'rating' => max(1, min(5, $rating)),
            'verified' => true,
            'title' => $title,
            'comment' => $comment,
            'date' => date('Y-m-d')
        ];
        save_review($newReview);
        set_flash('success', 'Thank you! Your verified review has been published.');
        header('Location: product-detail.php?id=' . urlencode($product['id']) . '#reviews-section');
        exit;
    } else {
        set_flash('error', 'Please fill in all review fields.');
    }
}

$pageTitle = $product['name'] . ' — Customization & Reviews';
require_once __DIR__ . '/includes/header.php';

$productReviews = get_reviews($product['id']);
$allProducts = get_products();
$relatedProducts = array_filter($allProducts, fn($p) => $p['id'] !== $product['id']);
?>

<div class="breadcrumb-container">
    <div class="breadcrumb-inner">
        <a href="index.php">Home</a> &rsaquo;
        <a href="categories.php">Categories</a> &rsaquo;
        <a href="categories.php?cat=<?= urlencode($product['category']) ?>"><?= htmlspecialchars($product['category_name']) ?></a> &rsaquo;
        <span><?= htmlspecialchars($product['name']) ?></span>
    </div>
</div>

<main class="section-container">
    
    <!-- Product Detail Layout (2 Columns) -->
    <div class="product-detail-grid">
        
        <!-- Left: Product Visual Presentation -->
        <div class="product-gallery-side">
            <div class="detail-visual-box">
                <div class="detail-visual-icon">
                    <?php if ($product['id'] == 'ceramic-mug'): ?>☕
                    <?php elseif ($product['id'] == 'wooden-frame'): ?>🖼️
                    <?php elseif ($product['id'] == 'acrylic-lamp'): ?>✨
                    <?php elseif ($product['id'] == 'crystal-keychain'): ?>💎
                    <?php elseif ($product['id'] == 'steel-tumbler'): ?>🥤
                    <?php else: ?>🛋️
                    <?php endif; ?>
                </div>
                <div class="detail-live-tag">
                    <span>⚡ Ready for Instant 3D Customization</span>
                </div>
            </div>

            <!-- Features Bullet List -->
            <div class="product-specs-box">
                <h4 style="font-weight: 700; margin-bottom: 0.75rem;">Premium Highlights:</h4>
                <ul class="specs-list">
                    <?php foreach ($product['features'] as $feat): ?>
                        <li><span style="color: #10B981; font-weight: bold; margin-right: 0.4rem;">✓</span> <?= htmlspecialchars($feat) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Right: Purchase & Customizer Launch Controls -->
        <div class="product-info-side">
            <span class="cat-pill"><?= htmlspecialchars($product['category_name']) ?></span>
            <h1 class="detail-product-title"><?= htmlspecialchars($product['name']) ?></h1>
            
            <div class="detail-rating-row">
                <span class="stars">⭐⭐⭐⭐⭐</span>
                <span class="rating-bold"><?= $product['rating'] ?></span>
                <a href="#reviews-section" class="review-link">(<?= count($productReviews) ?> Verified Reviews)</a>
                <?php if (!empty($product['badge'])): ?>
                    <span class="badge-chip"><?= htmlspecialchars($product['badge']) ?></span>
                <?php endif; ?>
            </div>

            <div class="detail-price-box">
                <span class="detail-price"><?= format_price($product['price']) ?></span>
                <span class="detail-price-sub">Free Photo Upload &amp; Custom Engraving Included</span>
            </div>

            <p class="detail-description"><?= htmlspecialchars($product['description']) ?></p>

            <!-- Available Product Colors -->
            <div class="detail-options-group">
                <label class="detail-label">Available Product Colors:</label>
                <div class="detail-color-list">
                    <?php foreach ($product['colors'] as $idx => $col): ?>
                        <div class="color-item" title="<?= htmlspecialchars($col['name']) ?>">
                            <span class="color-circle" style="background-color: <?= htmlspecialchars($col['hex']) ?>; <?= $col['hex'] === '#FFFFFF' ? 'border: 1px solid #CBD5E1;' : '' ?>"></span>
                            <span class="color-name"><?= htmlspecialchars($col['name']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Big Customizer Launch CTA -->
            <div class="detail-cta-box">
                <a href="customizer.php?product=<?= urlencode($product['id']) ?>" class="btn-launch-customizer">
                    <span>⭐ Customize This Gift with Photo &amp; Message</span>
                </a>
                <p class="customizer-subtext">Click above to open the real-time 3D studio where you can drag &amp; drop your photo, test fonts, and preview before ordering.</p>
            </div>

            <!-- Delivery & Assurance Perks -->
            <div class="perks-row">
                <div class="perk-item">
                    <span class="perk-icon">🚚</span>
                    <div>
                        <div class="perk-title">Fast Crafting</div>
                        <div class="perk-desc">Customized &amp; dispatched in 24-48 hrs</div>
                    </div>
                </div>
                <div class="perk-item">
                    <span class="perk-icon">🔒</span>
                    <div>
                        <div class="perk-title">Safe Packaging</div>
                        <div class="perk-desc">Bubble wrapped gift-ready presentation</div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Customer Reviews & Ratings Section -->
    <section class="reviews-section" id="reviews-section">
        <div class="section-header">
            <div>
                <h2 class="section-title">Customer Reviews &amp; Ratings</h2>
                <p class="section-subtitle">Read feedback from customers who personalized this specific gift.</p>
            </div>
            <button onclick="document.getElementById('writeReviewModal').style.display='flex'" class="btn-write-review">
                ✍️ Write a Review
            </button>
        </div>

        <!-- Rating Summary Score Card -->
        <div class="rating-overview-card">
            <div class="rating-score-col">
                <div class="huge-score"><?= $product['rating'] ?></div>
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <div style="font-size: 0.85rem; color: #64748B; margin-top: 0.25rem;">Based on <?= count($productReviews) ?> ratings</div>
            </div>
            
            <div class="rating-bars-col">
                <div class="bar-row">
                    <span>5 Stars</span>
                    <div class="bar-track"><div class="bar-fill" style="width: 88%;"></div></div>
                    <span>88%</span>
                </div>
                <div class="bar-row">
                    <span>4 Stars</span>
                    <div class="bar-track"><div class="bar-fill" style="width: 10%;"></div></div>
                    <span>10%</span>
                </div>
                <div class="bar-row">
                    <span>3 Stars</span>
                    <div class="bar-track"><div class="bar-fill" style="width: 2%;"></div></div>
                    <span>2%</span>
                </div>
                <div class="bar-row">
                    <span>2 Stars</span>
                    <div class="bar-track"><div class="bar-fill" style="width: 0%;"></div></div>
                    <span>0%</span>
                </div>
                <div class="bar-row">
                    <span>1 Star</span>
                    <div class="bar-track"><div class="bar-fill" style="width: 0%;"></div></div>
                    <span>0%</span>
                </div>
            </div>
        </div>

        <!-- Reviews List -->
        <div class="reviews-list">
            <?php if (empty($productReviews)): ?>
                <div style="text-align: center; padding: 2rem; color: #64748B;">
                    No reviews yet for this product. Be the first to personalize and review it!
                </div>
            <?php else: ?>
                <?php foreach ($productReviews as $rev): ?>
                    <div class="review-item-card">
                        <div class="review-item-top">
                            <div>
                                <div class="review-user-name"><?= htmlspecialchars($rev['user_name']) ?></div>
                                <div class="review-date-meta">
                                    <span class="verified-tag">✓ Verified Buyer</span>
                                    <span>• Reviewed on <?= htmlspecialchars($rev['date']) ?></span>
                                </div>
                            </div>
                            <div class="review-stars-display">
                                <?= str_repeat('⭐', $rev['rating']) ?>
                            </div>
                        </div>
                        <h4 class="review-headline">"<?= htmlspecialchars($rev['title']) ?>"</h4>
                        <p class="review-body"><?= htmlspecialchars($rev['comment']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Modal for Submitting a Review -->
    <div id="writeReviewModal" class="modal-overlay" style="display: none;">
        <div class="modal-card" style="max-width: 550px; text-align: left;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800;">Write a Review for <?= htmlspecialchars($product['name']) ?></h3>
                <button type="button" onclick="document.getElementById('writeReviewModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>

            <form action="product-detail.php?id=<?= urlencode($product['id']) ?>" method="POST" class="review-form">
                <input type="hidden" name="action" value="add_review">

                <div class="form-group">
                    <label class="form-label">Your Rating (1 to 5 Stars)</label>
                    <select name="rating" class="form-input" required>
                        <option value="5">⭐⭐⭐⭐⭐ 5 Stars - Exceptional</option>
                        <option value="4">⭐⭐⭐⭐ 4 Stars - Very Good</option>
                        <option value="3">⭐⭐⭐ 3 Stars - Average</option>
                        <option value="2">⭐⭐ 2 Stars - Below Average</option>
                        <option value="1">⭐ 1 Star - Disappointed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Your Full Name</label>
                    <input type="text" name="user_name" class="form-input" placeholder="e.g. Sarah Jenkins" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Review Headline / Title</label>
                    <input type="text" name="title" class="form-input" placeholder="e.g. Crisp print and arrived in 3 days!" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Your Experience / Feedback</label>
                    <textarea name="comment" rows="4" class="form-input" placeholder="Tell us how the custom photo and engraved text looked..." required></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.25rem;">
                    <button type="button" onclick="document.getElementById('writeReviewModal').style.display='none'" class="btn-secondary">Cancel</button>
                    <button type="submit" class="save-cta-btn" style="width: auto; padding: 0.75rem 1.5rem;">Submit Review</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Related Gifts -->
    <div style="margin-top: 3.5rem;">
        <h3 class="section-title">Other Gifts You Might Like</h3>
        <div class="product-cards-grid" style="margin-top: 1.25rem;">
            <?php foreach (array_slice($relatedProducts, 0, 3) as $rp): ?>
                <div class="product-card">
                    <div class="product-card-visual">
                        <div class="product-visual-circle">
                            <?php if ($rp['id'] == 'ceramic-mug'): ?>☕
                            <?php elseif ($rp['id'] == 'wooden-frame'): ?>🖼️
                            <?php elseif ($rp['id'] == 'acrylic-lamp'): ?>✨
                            <?php else: ?>🛋️
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="product-card-body">
                        <div class="product-card-category"><?= htmlspecialchars($rp['category_name']) ?></div>
                        <h4 class="product-card-title"><a href="product-detail.php?id=<?= urlencode($rp['id']) ?>"><?= htmlspecialchars($rp['name']) ?></a></h4>
                        <div class="product-card-footer">
                            <div class="product-card-price"><?= format_price($rp['price']) ?></div>
                            <a href="customizer.php?product=<?= urlencode($rp['id']) ?>" class="btn-card-customize">Customize</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

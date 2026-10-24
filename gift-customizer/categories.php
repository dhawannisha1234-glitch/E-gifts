<?php
$pageTitle = 'Personalized Gift Categories & Catalog';
require_once __DIR__ . '/includes/header.php';

$allProducts = get_products();
$selectedCat = $_GET['cat'] ?? 'all';
$searchQuery = trim($_GET['q'] ?? '');
$sortBy = $_GET['sort'] ?? 'featured';

// Filter products
$filteredProducts = array_filter($allProducts, function($p) use ($selectedCat, $searchQuery) {
    // Category filter
    if ($selectedCat !== 'all' && $p['category'] !== $selectedCat) {
        return false;
    }
    // Search query
    if (!empty($searchQuery)) {
        $q = strtolower($searchQuery);
        $matchName = strpos(strtolower($p['name']), $q) !== false;
        $matchDesc = strpos(strtolower($p['short_description']), $q) !== false;
        $matchCat = strpos(strtolower($p['category_name']), $q) !== false;
        if (!$matchName && !$matchDesc && !$matchCat) return false;
    }
    return true;
});

// Sort
if ($sortBy === 'price_asc') {
    usort($filteredProducts, fn($a, $b) => $a['price'] <=> $b['price']);
} elseif ($sortBy === 'price_desc') {
    usort($filteredProducts, fn($a, $b) => $b['price'] <=> $a['price']);
} elseif ($sortBy === 'rating') {
    usort($filteredProducts, fn($a, $b) => $b['rating'] <=> $a['rating']);
}

$categoriesList = [
    'all' => '🌟 All Gifts',
    'mugs' => '☕ Personalized Mugs',
    'tshirts' => '👕 Custom T-Shirts',
    'frames' => '🖼️ Photo Frames',
    'cushions' => '🛋️ Custom Cushions',
    'keychains' => '🔑 Crystal Keychains',
    'lamps' => '💡 LED Lamps'
];
?>

<div class="page-header-banner">
    <div class="page-header-inner">
        <span class="hero-badge">Browse By Category</span>
        <h1 class="page-title">Personalized Gift Categories</h1>
        <p class="page-subtitle">Find the perfect personalized treasure for any occasion, relationship, or celebration.</p>
    </div>
</div>

<main class="section-container">
    
    <!-- Filter, Search and Sort Bar -->
    <div class="catalog-toolbar">
        
        <!-- Category Pills -->
        <div class="cat-filter-tabs">
            <?php foreach ($categoriesList as $key => $label): ?>
                <a href="categories.php?cat=<?= urlencode($key) ?><?= !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '' ?>" 
                   class="cat-tab-btn <?= $selectedCat === $key ? 'active' : '' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Search & Sort Controls -->
        <div class="search-sort-group">
            <form action="categories.php" method="GET" class="catalog-search-form">
                <?php if ($selectedCat !== 'all'): ?>
                    <input type="hidden" name="cat" value="<?= htmlspecialchars($selectedCat) ?>">
                <?php endif; ?>
                <input type="text" name="q" placeholder="Search gifts..." value="<?= htmlspecialchars($searchQuery) ?>" class="search-input">
                <button type="submit" class="search-submit-btn">🔍</button>
            </form>

            <form action="categories.php" method="GET" class="sort-form">
                <input type="hidden" name="cat" value="<?= htmlspecialchars($selectedCat) ?>">
                <?php if (!empty($searchQuery)): ?>
                    <input type="hidden" name="q" value="<?= htmlspecialchars($searchQuery) ?>">
                <?php endif; ?>
                <select name="sort" onchange="this.form.submit()" class="sort-select">
                    <option value="featured" <?= $sortBy === 'featured' ? 'selected' : '' ?>>Sort: Featured</option>
                    <option value="price_asc" <?= $sortBy === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sortBy === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="rating" <?= $sortBy === 'rating' ? 'selected' : '' ?>>Customer Rating</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Results Count -->
    <div class="results-header">
        <div>
            Showing <strong><?= count($filteredProducts) ?></strong> customizable gift<?= count($filteredProducts) !== 1 ? 's' : '' ?>
            <?php if (!empty($searchQuery)): ?>
                matching "<em><?= htmlspecialchars($searchQuery) ?></em>"
            <?php endif; ?>
        </div>
        <?php if ($selectedCat !== 'all' || !empty($searchQuery)): ?>
            <a href="categories.php" class="clear-filters-link">✕ Clear All Filters</a>
        <?php endif; ?>
    </div>

    <!-- Product Grid -->
    <?php if (empty($filteredProducts)): ?>
        <div class="empty-catalog-state">
            <div style="font-size: 3.5rem; margin-bottom: 0.75rem;">🔍</div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #1E293B;">No gifts found</h3>
            <p style="color: #64748B; margin-top: 0.25rem;">Try modifying your search query or selecting a different category.</p>
            <a href="categories.php" class="btn-primary-large" style="display: inline-block; margin-top: 1.25rem;">View All Gifts</a>
        </div>
    <?php else: ?>
        <div class="product-cards-grid">
            <?php foreach ($filteredProducts as $p): ?>
                <div class="product-card">
                    <?php if (!empty($p['badge'])): ?>
                        <span class="product-card-badge"><?= htmlspecialchars($p['badge']) ?></span>
                    <?php endif; ?>

                    <div class="product-card-visual">
                        <div class="product-visual-circle">
                            <?php if ($p['id'] == 'ceramic-mug'): ?>☕
                            <?php elseif ($p['id'] == 'wooden-frame'): ?>🖼️
                            <?php elseif ($p['id'] == 'acrylic-lamp'): ?>✨
                            <?php elseif ($p['id'] == 'crystal-keychain'): ?>💎
                            <?php elseif ($p['id'] == 'steel-tumbler'): ?>🥤
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
                            <div class="product-card-price">$<?= number_format($p['price'], 2) ?></div>
                            <div class="product-card-actions">
                                <a href="product-detail.php?id=<?= urlencode($p['id']) ?>" class="btn-card-detail">Details</a>
                                <a href="customizer.php?product=<?= urlencode($p['id']) ?>" class="btn-card-customize">
                                    Customize 🎨
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

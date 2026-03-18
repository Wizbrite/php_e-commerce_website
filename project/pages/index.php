<?php
session_start();
include "../database/connection.php";
// Fetch up to 8 products (most recent first)
$featured = mysqli_query($conn, "SELECT p.*, c.name AS cat_name FROM product p LEFT JOIN category c ON p.category_id = c.category_id ORDER BY p.created_at DESC LIMIT 8");
// Cart count
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $cr = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS cnt FROM cart WHERE user_id=$uid"));
    $cart_count = $cr['cnt'] ?? 0;
}
// Fetch categories for hero chips
$hero_cats = mysqli_query($conn, "SELECT * FROM category ORDER BY name ASC");
$hero_cats_arr = [];
while ($hc = mysqli_fetch_assoc($hero_cats)) $hero_cats_arr[] = $hc;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopX — Premium Online Store</title>
    <meta name="description" content="Discover amazing products at unbeatable prices. Shop the latest collection on ShopX.">
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Nav -->
    <nav class="nav">
        <div class="logo"><h1>Shop<span style="color:var(--primary-color)">X</span></h1></div>
        
        <!-- Mobile Toggle -->
        <button class="menu-toggle" id="menuToggle" aria-label="Toggle Menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="navigate" id="navbarNav">
            <a href="index.php" class="active">Home</a>
            <a href="products.php">Shop</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="cart.php" class="cart-badge" title="Cart">
                    <i class="fa-solid fa-cart-shopping"></i> Cart
                    <?php if ($cart_count > 0): ?>
                        <span class="badge"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <a href="dashboard.php">Dashboard</a>
                <?php endif; ?>
                <a href="../controller/logout.php" style="color: var(--danger); font-weight: 600;">Logout</a>
            <?php else: ?>
                <a href="signup.php" class="sign-up">Sign Up</a>
                <a href="login.php" style="font-weight: 600;">Login</a>
            <?php endif; ?>
        </div>
    </nav>
    
    <!-- Mobile Overlay -->
    <div class="nav-overlay" id="navOverlay"></div>

    <!-- Hero -->
    <div class="hero">
        <span class="hero-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> New Collection Available</span>
        <h2>Discover <span>Premium</span><br>Products Online</h2>
        <p>Shop thousands of curated products, delivered fast. Quality you can trust, prices you'll love.</p>
        <div class="hero-actions">
            <a href="products.php" class="btn btn-primary" style="padding: 0.85rem 2rem; font-size: 1rem;">
                Shop Now <i class="fa-solid fa-arrow-right"></i>
            </a>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="signup.php" class="btn btn-outline" style="padding: 0.85rem 2rem; font-size: 1rem;">
                    Create Account
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Category Chips -->
    <?php if (!empty($hero_cats_arr)): ?>
    <div style="background: var(--surface); border-bottom: 1px solid var(--border-color); padding: 1rem 2rem;">
        <div style="max-width:1280px; margin:0 auto; display:flex; gap:0.65rem; flex-wrap:wrap; align-items:center;">
            <span style="font-size:0.8rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-right:0.5rem;">Shop by:</span>
            <?php foreach ($hero_cats_arr as $hc): ?>
                <a href="products.php?cat=<?= urlencode($hc['slug']) ?>" class="cat-pill">
                    <i class="fa-solid fa-tag"></i> <?= htmlspecialchars($hc['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Featured Products -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">Latest Products</h2>
            <a href="products.php" class="btn btn-outline btn-sm">View All <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <?php if (mysqli_num_rows($featured) > 0): ?>
        <div class="products-grid">
            <?php while ($p = mysqli_fetch_assoc($featured)): 
                $in_stock = $p['quantity'] > 0;
            ?>
            <div class="product-card" style="<?= !$in_stock ? 'opacity: 0.7;' : '' ?>">
                <div class="product-img-wrap">
                    <?php if (!empty($p['image'])): ?>
                        <img src="../assets/image/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="<?= !$in_stock ? 'filter: grayscale(1);' : '' ?>">
                    <?php else: ?>
                        <div class="product-img-placeholder" style="color:var(--text-muted);"><i class="fa-solid fa-bag-shopping"></i></div>
                    <?php endif; ?>
                    <span class="product-badge <?= !$in_stock ? 'out' : '' ?>">
                        <?= $in_stock ? 'In Stock' : 'Sold Out' ?>
                    </span>
                    <?php if (!empty($p['cat_name'])): ?>
                        <span style="position:absolute;bottom:8px;left:8px;background:rgba(0,0,0,0.65);color:#fff;font-size:0.7rem;font-weight:600;padding:0.2rem 0.6rem;border-radius:50px;backdrop-filter:blur(4px);">
                            <i class="fa-solid fa-tag"></i> <?= htmlspecialchars($p['cat_name']) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                    <div class="product-desc"><?= htmlspecialchars($p['description']) ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                        <?= $in_stock ? $p['quantity'] . ' items available' : 'Out of stock' ?>
                    </div>
                    <div class="product-footer">
                        <span class="product-price"><?= number_format($p['price']) ?> FCFA</span>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($in_stock): ?>
                                <form method="POST" action="../controller/cartcontroller.php">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-plus"></i> Add to Cart
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-outline btn-sm" disabled style="cursor: not-allowed;">
                                    <i class="fa-solid fa-ban"></i> Sold Out
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline btn-sm">
                                <i class="fa-solid fa-right-to-bracket"></i> Login to Buy
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="icon"><i class="fa-solid fa-box-open" style="font-size: 3rem; color: var(--text-muted);"></i></div>
                <h3>No products yet</h3>
                <p>Check back soon — we're stocking up!</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Features Redesign -->
    <div class="section" style="background: rgba(108, 99, 255, 0.015); border-top: 1px solid var(--border-color);">
        <div style="text-align: center; margin-bottom: 4rem;">
            <div class="hero-badge" style="margin-bottom: 1rem;">WHY CHOOSE US</div>
            <h2 class="section-title">Elevate Your Shopping Experience</h2>
            <p style="color: var(--text-muted); margin-top: 0.75rem; font-size: 1.1rem; max-width: 600px; margin-inline: auto;">
                We combine premium quality with world-class service to bring you the best online store in Cameroon.
            </p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon-wrap"><i class="fa-solid fa-truck-fast"></i></div>
                <h3>Free Shipping</h3>
                <p>Lightning-fast delivery on all orders over 30,000 FCFA nationwide.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon-wrap"><i class="fa-solid fa-shield-halved"></i></div>
                <h3>Secure Payment</h3>
                <p>100% protected transactions with military-grade encryption.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon-wrap"><i class="fa-solid fa-rotate-left"></i></div>
                <h3>Easy Returns</h3>
                <p>Hassle-free 30-day return policy on all eligible products.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon-wrap"><i class="fa-solid fa-headset"></i></div>
                <h3>24/7 Support</h3>
                <p>Dedicated customer care team available around the clock to help.</p>
            </div>
        </div>
    </div>

    <footer style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.85rem; border-top: 1px solid var(--border-color);">
        &copy; <?= date('Y') ?> ShopX. All rights reserved.
    </footer>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const navbarNav = document.getElementById('navbarNav');
        const navOverlay = document.getElementById('navOverlay');

        function toggleMenu() {
            navbarNav.classList.toggle('active');
            navOverlay.classList.toggle('active');
            menuToggle.innerHTML = navbarNav.classList.contains('active') 
                ? '<i class="fa-solid fa-xmark"></i>' 
                : '<i class="fa-solid fa-bars"></i>';
            document.body.style.overflow = navbarNav.classList.contains('active') ? 'hidden' : '';
        }

        menuToggle.addEventListener('click', toggleMenu);
        navOverlay.addEventListener('click', toggleMenu);
    </script>
</body>
</html>
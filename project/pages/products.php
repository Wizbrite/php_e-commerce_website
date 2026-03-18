<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include "../database/connection.php";

// Cart count
$uid = intval($_SESSION['user_id']);
$cr  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) AS cnt FROM cart WHERE user_id=$uid"));
$cart_count = $cr['cnt'] ?? 0;

// Fetch all categories for filter bar
$all_cats = mysqli_query($conn, "SELECT * FROM category ORDER BY name ASC");
$cats_list = [];
while ($ct = mysqli_fetch_assoc($all_cats)) $cats_list[] = $ct;

// Search & category filter
$search   = isset($_GET['q'])   ? mysqli_real_escape_string($conn, $_GET['q'])   : '';
$cat_slug = isset($_GET['cat']) ? mysqli_real_escape_string($conn, $_GET['cat']) : '';

$conditions = [];
if ($search)   $conditions[] = "(p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
if ($cat_slug) $conditions[] = "c.slug = '$cat_slug'";
$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

$products = mysqli_query($conn,
    "SELECT p.*, c.name AS cat_name, c.slug AS cat_slug
     FROM product p
     LEFT JOIN category c ON p.category_id = c.category_id
     $where
     ORDER BY p.created_at DESC");

// Resolve active category name for heading
$active_cat_name = '';
if ($cat_slug) {
    foreach ($cats_list as $ct) {
        if ($ct['slug'] === $cat_slug) { $active_cat_name = $ct['name']; break; }
    }
}

$name  = htmlspecialchars($_SESSION['user_name']);
$init  = strtoupper(substr($name, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop — ShopX</title>
    <meta name="description" content="Browse all products in our store.">
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <nav class="nav">
        <div class="logo"><h1>Shop<span style="color:var(--primary-color)">X</span></h1></div>
        
        <!-- Mobile Toggle -->
        <button class="menu-toggle" id="menuToggle" aria-label="Toggle Menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="navigate" id="navbarNav">
            <a href="index.php">Home</a>
            <a href="products.php" class="active">Shop</a>
            <a href="cart.php" class="cart-badge">
                <i class="fa-solid fa-cart-shopping"></i> Cart
                <?php if ($cart_count > 0): ?>
                    <span class="badge"><?= $cart_count ?></span>
                <?php endif; ?>
            </a>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="dashboard.php">Dashboard</a>
            <?php endif; ?>
            <a href="../controller/logout.php" style="color: var(--danger); font-weight: 600;">
                <i class="fa-solid fa-door-open"></i> Logout
            </a>
        </div>
    </nav>
    
    <!-- Mobile Overlay -->
    <div class="nav-overlay" id="navOverlay"></div>

    <div class="section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
            <div>
                <h1 class="section-title"><?= $active_cat_name ?: 'All Products' ?></h1>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">
                    Welcome back, <?= $name ?>! Find something you'll love.
                </p>
            </div>
            <!-- Search -->
            <form method="GET" class="search-form-wrap">
                <?php if ($cat_slug): ?><input type="hidden" name="cat" value="<?= htmlspecialchars($cat_slug) ?>"><?php endif; ?>
                <div class="search-input-group">
                    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                           placeholder="Search products..." class="search-input">
                    <button type="submit" class="btn btn-primary search-btn"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                </div>
                <?php if ($search || $cat_slug): ?>
                    <a href="products.php" class="btn btn-outline">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Category Filter Bar -->
        <?php if (!empty($cats_list)): ?>
        <div class="category-filter-bar">
            <a href="products.php<?= $search ? '?q='.urlencode($search) : '' ?>" class="cat-pill <?= !$cat_slug ? 'active' : '' ?>">
                <i class="fa-solid fa-grid-2"></i> All
            </a>
            <?php foreach ($cats_list as $ct): ?>
                <a href="products.php?cat=<?= urlencode($ct['slug']) ?><?= $search ? '&q='.urlencode($search) : '' ?>"
                   class="cat-pill <?= $ct['slug'] === $cat_slug ? 'active' : '' ?>">
                    <i class="fa-solid fa-tag"></i> <?= htmlspecialchars($ct['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['cart_error'])): ?>
            <div class="alert-error"><?= htmlspecialchars($_SESSION['cart_error']) ?></div>
            <?php unset($_SESSION['cart_error']); ?>
        <?php endif; ?>

        <?php if (mysqli_num_rows($products) > 0): ?>
        <div class="products-grid">
            <?php while ($p = mysqli_fetch_assoc($products)):
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
                        <?= $in_stock ? ($p['quantity'] < 5 ? 'Low Stock' : 'In Stock') : 'Sold Out' ?>
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
                    <div class="product-footer">
                        <div>
                            <div class="product-price"><?= number_format($p['price']) ?> FCFA</div>
                            <div class="product-stock" style="color: <?= $p['quantity'] < 5 ? 'var(--danger)' : 'var(--text-muted)' ?>">
                                <?= $in_stock ? $p['quantity'] . ' available' : 'Out of stock' ?>
                            </div>
                        </div>
                        <?php if ($in_stock): ?>
                        <form method="POST" action="../controller/cartcontroller.php" style="display:flex;gap:0.4rem;align-items:center;">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?= $p['quantity'] ?>"
                                   style="width:52px;padding:0.35rem;background:var(--surface);border:1px solid var(--border-color);border-radius:6px;color:var(--text-main);font-size:0.85rem;text-align:center;">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-cart-plus"></i> Add</button>
                        </form>
                        <?php else: ?>
                            <button class="btn btn-outline btn-sm" disabled style="cursor: not-allowed;"><i class="fa-solid fa-ban"></i> Sold Out</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                <h3><?= $search ? 'No products found' : 'No products yet' ?></h3>
                <p><?= $search ? "Try a different search term." : "Check back soon!" ?></p>
                <?php if ($search): ?>
                    <a href="products.php" class="btn btn-primary">Browse All</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer style="text-align:center;padding:2rem;color:var(--text-muted);font-size:0.85rem;border-top:1px solid var(--border-color);">
        &copy; <?= date('Y') ?> ShopX. All rights reserved.
    </footer>

    <script>
        const menuToggle  = document.getElementById('menuToggle');
        const navbarNav   = document.getElementById('navbarNav');
        const navOverlay  = document.getElementById('navOverlay');

        function toggleMenu() {
            navbarNav.classList.toggle('active');
            navOverlay.classList.toggle('active');
            menuToggle.innerHTML = navbarNav.classList.contains('active') 
                ? '<i class="fa-solid fa-xmark"></i>' 
                : '<i class="fa-solid fa-bars"></i>';
            document.body.style.overflow = navbarNav.classList.contains('active') ? 'hidden' : '';
        }

        if (menuToggle) menuToggle.addEventListener('click', toggleMenu);
        if (navOverlay) navOverlay.addEventListener('click', toggleMenu);
    </script>
</body>
</html>

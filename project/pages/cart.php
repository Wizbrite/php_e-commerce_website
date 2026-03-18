<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include "../database/connection.php";

$uid = intval($_SESSION['user_id']);

// Fetch cart with product details
$items = mysqli_query($conn,
    "SELECT c.cart_id, c.quantity AS cart_qty, p.product_id, p.name, p.price, p.quantity AS stock, p.image
     FROM cart c
     JOIN product p ON c.product_id = p.product_id
     WHERE c.user_id = $uid");

$total = 0;
$rows  = [];
$has_stock_error = false;
while ($r = mysqli_fetch_assoc($items)) {
    $r['subtotal'] = $r['price'] * $r['cart_qty'];
    $r['stock_ok'] = ($r['cart_qty'] <= $r['stock']);
    if (!$r['stock_ok']) $has_stock_error = true;
    $total += $r['subtotal'];
    $rows[] = $r;
}

$cart_count = count($rows);
$name = htmlspecialchars($_SESSION['user_name']);
$init = strtoupper(substr($name, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart — ShopX</title>
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
            <a href="products.php">Shop</a>
            <a href="cart.php" class="cart-badge active">
                <i class="fa-solid fa-cart-shopping"></i> Cart
                <?php if ($cart_count > 0): ?>
                    <span class="badge"><?= $cart_count ?></span>
                <?php endif; ?>
            </a>
            <a href="../controller/logout.php" style="color: var(--danger); font-weight: 600;">
                <i class="fa-solid fa-door-open"></i> Logout
            </a>
        </div>
    </nav>
    
    <!-- Mobile Overlay -->
    <div class="nav-overlay" id="navOverlay"></div>

    <div class="section">
        <h1 class="section-title" style="margin-bottom: 1.75rem;"><i class="fa-solid fa-cart-shopping"></i> My Cart</h1>

        <?php if (isset($_SESSION['order_error'])): ?>
            <div class="alert-error"><?= htmlspecialchars($_SESSION['order_error']) ?></div>
            <?php unset($_SESSION['order_error']); ?>
        <?php endif; ?>

        <?php if (empty($rows)): ?>
            <div class="empty-state">
                <div class="icon"><i class="fa-solid fa-cart-flatbed"></i></div>
                <h3>Your cart is empty</h3>
                <p>Add some products to get started!</p>
                <a href="products.php" class="btn btn-primary">Browse Products</a>
            </div>
<?php else: ?>
        <div class="cart-layout">
            <!-- Cart Items -->
            <div>
                <div class="table-card">
                    <div class="table-header">
                        <h3>Cart Items (<?= $cart_count ?>)</h3>
                        <form method="POST" action="../controller/cartcontroller.php" onsubmit="return confirm('Clear your entire cart?')">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash-can"></i> Clear Cart</button>
                        </form>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rows as $item): ?>
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:0.75rem;">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="../assets/image/<?= htmlspecialchars($item['image']) ?>"
                                                  style="width:48px;height:48px;border-radius:8px;object-fit:cover;">
                                        <?php else: ?>
                                            <div style="width:48px;height:48px;border-radius:8px;background:var(--surface);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:var(--text-muted);"><i class="fa-solid fa-bag-shopping"></i></div>
                                        <?php endif; ?>
                                        <span style="font-weight:600;color:var(--text-main);"><?= htmlspecialchars($item['name']) ?></span>
                                        <?php if (!$item['stock_ok']): ?>
                                            <div style="font-size:0.75rem;color:var(--danger);font-weight:600;"><i class="fa-solid fa-triangle-exclamation"></i> Only <?= $item['stock'] ?> left</div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td data-label="Price" style="color: var(--text-muted);"><?= number_format($item['price']) ?> FCFA</td>
                                <td data-label="Quantity">
                                    <form method="POST" action="../controller/cartcontroller.php" style="display:flex;gap:0.4rem;align-items:center;">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                        <input type="number" name="quantity" value="<?= $item['cart_qty'] ?>"
                                               min="1" max="<?= $item['stock'] ?>"
                                               style="width:60px;padding:0.35rem;background:var(--surface);border:1px solid var(--border-color);border-radius:6px;color:var(--text-main);font-size:0.875rem;text-align:center;">
                                        <button type="submit" class="btn btn-outline btn-sm"><i class="fa-solid fa-rotate"></i></button>
                                    </form>
                                </td>
                                <td data-label="Subtotal" style="font-weight:700;color:var(--primary-color);"><?= number_format($item['subtotal']) ?> FCFA</td>
                                <td data-label="Action">
                                    <form method="POST" action="../controller/cartcontroller.php">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i> Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="products.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Continue Shopping</a>
            </div>

            <!-- Order Summary -->
            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal (<?= $cart_count ?> items)</span>
                    <span><?= number_format($total) ?> FCFA</span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span style="color: var(--success);">Free</span>
                </div>
                <div class="summary-row">
                    <span>Tax (0%)</span>
                    <span>0 FCFA</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span><?= number_format($total) ?> FCFA</span>
                </div>
                <?php if ($has_stock_error): ?>
                    <div style="margin-top:1.25rem;padding:0.75rem;background:rgba(239,68,68,0.1);border:1px solid var(--danger);border-radius:8px;color:var(--danger);font-size:0.85rem;font-weight:600;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Some items have insufficient stock. Please reduce quantities.
                    </div>
                <?php else: ?>
                    <a href="checkout.php" class="btn btn-primary"
                       style="width:100%;justify-content:center;margin-top:1.25rem;padding:0.85rem;">
                        Proceed to Checkout <i class="fa-solid fa-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>
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

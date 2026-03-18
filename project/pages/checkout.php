<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include "../database/connection.php";

$uid = intval($_SESSION['user_id']);

$items = mysqli_query($conn,
    "SELECT c.cart_id, c.quantity AS cart_qty, p.product_id, p.name, p.price, p.image
     FROM cart c
     JOIN product p ON c.product_id = p.product_id
     WHERE c.user_id = $uid");

if (mysqli_num_rows($items) === 0) {
    header("Location: cart.php");
    exit();
}

$rows = [];
$total = 0;
while ($r = mysqli_fetch_assoc($items)) {
    $r['subtotal'] = $r['price'] * $r['cart_qty'];
    $total += $r['subtotal'];
    $rows[] = $r;
}

$name = htmlspecialchars($_SESSION['user_name']);
$email = htmlspecialchars($_SESSION['user_email']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — ShopX</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <nav class="nav">
        <div class="logo"><h1>Shop<span style="color:var(--primary-color)">X</span></h1></div>
        <div class="navigate">
            <a href="index.php">Home</a>
            <a href="products.php">Shop</a>
            <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart</a>
            <a href="../controller/logout.php" style="color: var(--danger); font-weight: 600;">
                <i class="fa-solid fa-door-open"></i> Logout
            </a>
        </div>
    </nav>

    <div class="section" style="max-width: 900px;">
        <h1 class="section-title" style="margin-bottom: 1.75rem;">Checkout</h1>

        <div class="cart-layout" style="grid-template-columns: 1fr 320px;">
            <!-- Left: Delivery Info + Order Items -->
            <div>
                <!-- Delivery Details (display-only, could connect to a shipping module) -->
                <div class="table-card" style="margin-bottom: 1.5rem; padding: 1.5rem;">
                    <h3 style="font-size:1rem; font-weight:700; color:var(--text-main); margin-bottom:1.25rem;">
                        <i class="fa-solid fa-truck-ramp-box"></i> Delivery Information
                    </h3>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="input-group">
                            <label>Full Name</label>
                            <input type="text" value="<?= $name ?>" readonly
                                   style="background:var(--surface);border:1px solid var(--border-color);padding:0.7rem 1rem;border-radius:8px;color:var(--text-muted);font-size:0.9rem;font-family:Inter,sans-serif;cursor:not-allowed;">
                        </div>
                        <div class="input-group">
                            <label>Email</label>
                            <input type="text" value="<?= $email ?>" readonly
                                   style="background:var(--surface);border:1px solid var(--border-color);padding:0.7rem 1rem;border-radius:8px;color:var(--text-muted);font-size:0.9rem;font-family:Inter,sans-serif;cursor:not-allowed;">
                        </div>
                    </div>
                </div>

                <!-- Order Items Review -->
                <div class="table-card">
                    <div class="table-header"><h3>Order Review</h3></div>
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rows as $item): ?>
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:0.65rem;">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="../assets/image/<?= htmlspecialchars($item['image']) ?>"
                                                 style="width:40px;height:40px;border-radius:8px;object-fit:cover;">
                                        <?php else: ?>
                                            <div style="width:40px;height:40px;border-radius:8px;background:var(--surface);display:flex;align-items:center;justify-content:center;color:var(--text-muted);"><i class="fa-solid fa-image"></i></div>
                                        <?php endif; ?>
                                        <span style="font-weight:600;color:var(--text-main);"><?= htmlspecialchars($item['name']) ?></span>
                                    </div>
                                </td>
                                <td><?= $item['cart_qty'] ?></td>
                                <td style="color:var(--text-muted);"><?= number_format($item['price']) ?> FCFA</td>
                                <td style="font-weight:700;color:var(--primary-color);"><?= number_format($item['subtotal']) ?> FCFA</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Summary + Place Order -->
            <div class="cart-summary">
                <h3>Payment Summary</h3>
                <div class="summary-row"><span>Subtotal</span><span><?= number_format($total) ?> FCFA</span></div>
                <div class="summary-row"><span>Shipping</span><span style="color:var(--success);">Free</span></div>
                <div class="summary-row"><span>Tax</span><span>0 FCFA</span></div>
                <div class="summary-row total"><span>Total</span><span><?= number_format($total) ?> FCFA</span></div>

                <div style="margin-top:1.5rem;padding:1rem;background:var(--surface);border-radius:8px;border:1px solid var(--border-color);">
                    <div style="font-size:0.8rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Payment Method</div>
                    <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.9rem;cursor:pointer;">
                        <input type="radio" checked style="accent-color:var(--primary-color);"> Cash on Delivery
                    </label>
                </div>

                <form method="POST" action="../controller/paymentcontroller.php" style="margin-top:1rem;">
                    <button type="submit" class="btn btn-primary"
                            style="width:100%;justify-content:center;padding:0.9rem;font-size:1rem;">
                        <i class="fa-solid fa-circle-check"></i> Place Order — <?= number_format($total) ?> FCFA
                    </button>
                </form>
                <a href="cart.php" style="display:block;text-align:center;margin-top:0.75rem;font-size:0.85rem;color:var(--text-muted);">← Back to Cart</a>
            </div>
        </div>
    </div>

    <footer style="text-align:center;padding:2rem;color:var(--text-muted);font-size:0.85rem;border-top:1px solid var(--border-color);">
        &copy; <?= date('Y') ?> ShopX. All rights reserved.
    </footer>
</body>
</html>

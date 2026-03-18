<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include "../database/connection.php";

$uid = intval($_SESSION['user_id']);
// Fetch the latest orders for this user
$orders = mysqli_query($conn,
    "SELECT pay.payment_id, pay.quantity, pay.total_price, pay.status, pay.created_at, p.name
     FROM payment pay
     JOIN product p ON pay.product_id = p.product_id
     WHERE pay.user_id = $uid
     ORDER BY pay.created_at DESC LIMIT 20");

$name = htmlspecialchars($_SESSION['user_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed — ShopX</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @keyframes pop {
            0%   { transform: scale(0.5); opacity: 0; }
            80%  { transform: scale(1.1); }
            100% { transform: scale(1);   opacity: 1; }
        }
        .success-icon { animation: pop 0.5s ease forwards; }
    </style>
</head>
<body>
    <nav class="nav">
        <div class="logo"><h1>Shop<span style="color:var(--primary-color)">X</span></h1></div>
        <div class="navigate">
            <a href="index.php">Home</a>
            <a href="products.php">Shop</a>
            <a href="../controller/logout.php" style="color: var(--danger); font-weight: 600;">
                <i class="fa-solid fa-door-open"></i> Logout
            </a>
        </div>
    </nav>

    <div class="success-page">
        <div style="width:100%;max-width:600px;">
            <div class="success-card">
                <div class="success-icon" style="font-size: 3.5rem; color: var(--success); margin-bottom: 1rem;"><i class="fa-solid fa-circle-check"></i></div>
                <h2>Order Placed!</h2>
                <p>Thank you, <strong style="color:var(--text-main)"><?= $name ?></strong>!<br>
                   Your order has been confirmed. We'll process it right away.</p>
                <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                    <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                    <a href="index.php" class="btn btn-outline">Back to Home</a>
                </div>
            </div>

            <!-- Recent Orders -->
            <?php if (mysqli_num_rows($orders) > 0): ?>
            <div class="table-card" style="margin-top:2rem;">
                <div class="table-header"><h3>Your Recent Orders</h3></div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($o = mysqli_fetch_assoc($orders)): ?>
                        <tr>
                            <td style="color:var(--text-muted);font-size:0.8rem;"><?= $o['payment_id'] ?></td>
                            <td style="font-weight:600;color:var(--text-main);"><?= htmlspecialchars($o['name']) ?></td>
                            <td><?= $o['quantity'] ?></td>
                            <td style="font-weight:700;color:var(--primary-color);"><?= number_format($o['total_price']) ?> FCFA</td>
                            <td><span class="status-pill success"><?= htmlspecialchars($o['status']) ?></span></td>
                            <td style="color:var(--text-muted);font-size:0.8rem;"><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
session_start();
include "../database/connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

// Fetch cart items
$cart_items = mysqli_query($conn,
    "SELECT c.cart_id, c.quantity, p.product_id, p.name, p.price, p.quantity AS stock
     FROM cart c
     JOIN product p ON c.product_id = p.product_id
     WHERE c.user_id = $user_id");

if (mysqli_num_rows($cart_items) === 0) {
    $_SESSION['order_error'] = "Your cart is empty.";
    header("Location: ../pages/cart.php");
    exit();
}

// Final Stock Check (Safety prevent race conditions)
$rows = [];
while ($r = mysqli_fetch_assoc($cart_items)) {
    if ($r['quantity'] > $r['stock']) {
        $_SESSION['order_error'] = "Insufficient stock for " . $r['name'] . ". Please update your cart.";
        header("Location: ../pages/cart.php");
        exit();
    }
    $rows[] = $r;
}

// Insert payment records and reduce stock
foreach ($rows as $item) {
    $p_id       = $item['product_id'];
    $qty        = $item['quantity'];
    $total      = $item['price'] * $qty;

    mysqli_query($conn, "INSERT INTO payment (user_id, product_id, quantity, total_price, status)
                         VALUES ($user_id, $p_id, $qty, $total, 'paid')");

    // Reduce product stock
    mysqli_query($conn, "UPDATE product SET quantity = quantity - $qty WHERE product_id = $p_id");
}

// Clear user's cart
mysqli_query($conn, "DELETE FROM cart WHERE user_id=$user_id");

header("Location: ../pages/order_success.php");
exit();
?>

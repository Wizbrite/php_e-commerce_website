<?php
session_start();
include "../database/connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$action  = $_POST['action'] ?? '';

if ($action === 'add') {
    $product_id = intval($_POST['product_id']);
    $qty        = max(1, intval($_POST['quantity'] ?? 1));

    // Check product stock
    $prod = mysqli_fetch_assoc(mysqli_query($conn, "SELECT quantity FROM product WHERE product_id=$product_id"));
    if (!$prod || $prod['quantity'] < 1) {
        $_SESSION['cart_error'] = "Product is out of stock.";
        header("Location: ../pages/products.php");
        exit();
    }

    // Check if already in cart
    $existing = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT cart_id, quantity FROM cart WHERE user_id=$user_id AND product_id=$product_id"));
    if ($existing) {
        $new_qty = $existing['quantity'] + $qty;
        mysqli_query($conn, "UPDATE cart SET quantity=$new_qty WHERE cart_id=" . $existing['cart_id']);
    } else {
        mysqli_query($conn, "INSERT INTO cart (product_id, user_id, quantity) VALUES ($product_id, $user_id, $qty)");
    }
    header("Location: ../pages/cart.php");
    exit();

} elseif ($action === 'update') {
    $cart_id = intval($_POST['cart_id']);
    $qty     = max(1, intval($_POST['quantity']));
    mysqli_query($conn, "UPDATE cart SET quantity=$qty WHERE cart_id=$cart_id AND user_id=$user_id");
    header("Location: ../pages/cart.php");
    exit();

} elseif ($action === 'remove') {
    $cart_id = intval($_POST['cart_id']);
    mysqli_query($conn, "DELETE FROM cart WHERE cart_id=$cart_id AND user_id=$user_id");
    header("Location: ../pages/cart.php");
    exit();

} elseif ($action === 'clear') {
    mysqli_query($conn, "DELETE FROM cart WHERE user_id=$user_id");
    header("Location: ../pages/cart.php");
    exit();

} else {
    header("Location: ../pages/cart.php");
    exit();
}
?>

<?php
session_start();
include "../database/connection.php";

// Admin-only gate
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price       = floatval($_POST['price']);
    $quantity    = intval($_POST['quantity']);
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : 'NULL';
    $image       = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext       = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename  = 'product_' . time() . '.' . $ext;
        $dest      = '../assets/image/' . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            $image = $filename;
        }
    }

    $cat_val = is_numeric($category_id) ? $category_id : 'NULL';
    $sql = "INSERT INTO product (name, description, price, quantity, image, category_id)
            VALUES ('$name', '$description', $price, $quantity, '$image', $cat_val)";
    mysqli_query($conn, $sql);
    header("Location: ../pages/dashboard.php?success=Product+added");
    exit();

} elseif ($action === 'edit') {
    $id          = intval($_POST['product_id']);
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price       = floatval($_POST['price']);
    $quantity    = intval($_POST['quantity']);
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $cat_set     = $category_id ? ", category_id=$category_id" : ", category_id=NULL";

    // Check for new image
    $image_set = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . time() . '.' . $ext;
        $dest     = '../assets/image/' . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            $image_set = ", image = '$filename'";
        }
    }

    $sql = "UPDATE product SET name='$name', description='$description', price=$price, quantity=$quantity $image_set $cat_set
            WHERE product_id=$id";
    mysqli_query($conn, $sql);
    header("Location: ../pages/dashboard.php?success=Product+updated");
    exit();

} elseif ($action === 'delete') {
    $id = intval($_POST['product_id']);
    mysqli_query($conn, "DELETE FROM product WHERE product_id=$id");
    header("Location: ../pages/dashboard.php?success=Product+deleted");
    exit();

} else {
    header("Location: ../pages/dashboard.php");
    exit();
}
?>

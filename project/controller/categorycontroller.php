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
    $name        = mysqli_real_escape_string($conn, trim($_POST['name']));
    $slug        = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
    $description = mysqli_real_escape_string($conn, trim($_POST['description'] ?? ''));

    // Ensure unique slug
    $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM category WHERE slug='$slug'"));
    if ($check['c'] > 0) {
        $slug = $slug . '-' . time();
    }

    mysqli_query($conn, "INSERT INTO category (name, slug, description) VALUES ('$name', '$slug', '$description')");
    header("Location: ../pages/dashboard.php?tab=categories&success=Category+added");
    exit();

} elseif ($action === 'edit') {
    $id          = intval($_POST['category_id']);
    $name        = mysqli_real_escape_string($conn, trim($_POST['name']));
    $slug        = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
    $description = mysqli_real_escape_string($conn, trim($_POST['description'] ?? ''));

    // Ensure unique slug (excluding current)
    $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM category WHERE slug='$slug' AND category_id != $id"));
    if ($check['c'] > 0) {
        $slug = $slug . '-' . time();
    }

    mysqli_query($conn, "UPDATE category SET name='$name', slug='$slug', description='$description' WHERE category_id=$id");
    header("Location: ../pages/dashboard.php?tab=categories&success=Category+updated");
    exit();

} elseif ($action === 'delete') {
    $id = intval($_POST['category_id']);
    // Products with this category will have their category_id set to NULL (ON DELETE SET NULL)
    mysqli_query($conn, "DELETE FROM category WHERE category_id=$id");
    header("Location: ../pages/dashboard.php?tab=categories&success=Category+deleted");
    exit();

} else {
    header("Location: ../pages/dashboard.php?tab=categories");
    exit();
}
?>

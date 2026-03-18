<?php
session_start();
include "../database/connection.php";

if (isset($_POST["submit"])) {
    $email    = mysqli_real_escape_string($conn, $_POST["user_email"]);
    $password = $_POST["user_password"];

    $sql    = "SELECT * FROM user WHERE user_email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['user_password'])) {
            $_SESSION['user_id']    = $user['user_id'];
            $_SESSION['user_name']  = $user['user_name'];
            $_SESSION['user_email'] = $user['user_email'];
            $_SESSION['user_role']  = $user['user_role'];

            // Role-based redirect
            if ($user['user_role'] === 'admin') {
                header("Location: ../pages/dashboard.php");
            } else {
                header("Location: ../pages/index.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Incorrect email or password.";
            header("Location: ../pages/login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "No account found with this email.";
        header("Location: ../pages/login.php");
        exit();
    }
} else {
    header("Location: ../pages/login.php");
    exit();
}
?>

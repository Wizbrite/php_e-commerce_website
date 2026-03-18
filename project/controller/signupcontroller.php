<?php
session_start();
include "../database/connection.php";

if(isset($_POST["submit"])){
    $name     = mysqli_real_escape_string($conn, $_POST["user_name"]);
    $email    = mysqli_real_escape_string($conn, $_POST["user_email"]);
    $password = $_POST["user_password"];

    $hash_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT user_id FROM user WHERE user_email = '$email'");
    if(mysqli_num_rows($check) > 0){
        $_SESSION['error'] = "An account with this email already exists.";
        header("Location: ../pages/signup.php");
        exit();
    }

    $sql_insert = "INSERT INTO user (user_name, user_email, user_password, user_role)
                   VALUES ('$name', '$email', '$hash_password', 'client')";

    if(mysqli_query($conn, $sql_insert)){
        $user_id = mysqli_insert_id($conn);
        $_SESSION['user_id']    = $user_id;
        $_SESSION['user_name']  = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role']  = 'client';
        header("Location: ../pages/index.php");
        exit();
    } else {
        $_SESSION['error'] = "Error creating account: " . mysqli_error($conn);
        header("Location: ../pages/signup.php");
        exit();
    }
} else {
    header("Location: ../pages/signup.php");
    exit();
}
?>
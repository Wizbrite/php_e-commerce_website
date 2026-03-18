<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — ShopX</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="main">
        <div class="container">
            <div class="card">
                <div style="text-align:center; font-size:2.5rem; color:var(--primary-color); margin-bottom:1rem;"><i class="fa-solid fa-circle-user"></i></div>
                <h2>Welcome Back</h2>
                <?php
                    session_start();
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                        unset($_SESSION['error']);
                    }
                ?>
                <form action="../controller/logincontroller.php" method="POST">
                    <div class="data">
                        <div class="input-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="user_email" placeholder="name@example.com" required>
                        </div>
                        <div class="input-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="user_password" placeholder="••••••••" required>
                        </div>
                        <button type="submit" name="submit"><i class="fa-solid fa-right-to-bracket"></i> Sign In</button>
                    </div>
                </form>
                <div class="auth-footer">
                    Don't have an account? <a href="signup.php">Create one</a>
                </div>
                <div class="auth-footer" style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--text-muted);">
                    Admin? Use your admin credentials to access the dashboard.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - ShopX</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="main">
        <div class="container">
            <div class="card">
                <div style="text-align:center; font-size:2.5rem; color:var(--primary-color); margin-bottom:1rem;"><i class="fa-solid fa-user-plus"></i></div>
                <h2>Create Account</h2>
                <?php
                    session_start();
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                        unset($_SESSION['error']);
                    }
                ?>
                <form method="post" action="../controller/signupcontroller.php">
                    <div class="data">
                        <div class="input-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="user_name" placeholder="Your Name" required>
                        </div>

                        <div class="input-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="user_email" placeholder="name@example.com" required>
                        </div>

                        <div class="input-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="user_password" placeholder="••••••••" required>
                        </div>

                        <button type="submit" name="submit"><i class="fa-solid fa-user-check"></i> Create Account</button>
                    </div>
                </form>
                <div class="auth-footer">
                    Already have an account? <a href="login.php">Sign In</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

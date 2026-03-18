


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../assets/css/index.css">
</head>
<body>
    <nav class="nav">
        <div class="logo"><h1>D<span style="color:blue;">a</span>tax</h1></div>
        <div class="navigate">
            <a href="index.php">Home</a>
            <a href="#">About us</a>
            <a href="#">Contact</a>
            <a href="#" class="active">Greetings</a>
            <button class="sign-up">Sign up</button>
        </div>
    </nav>
    <div class="main">
        <div class="container">
            <div class="card">
                <?php
                if(isset($_POST["submit"])){
                    echo "<div class='greet'>Welcome, " . htmlspecialchars($_POST["user_name"]) . "!</div>";
                } else {
                    echo "<div class='greet'>Please enter the correct credentials!</div>";
                    echo "<div class='auth-footer'><a href='dashboard.php'>Go to your dashboard</a></div>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
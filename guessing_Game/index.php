<?php
// Start the session to persist variables across requests
session_start();

// Define the range for the random number
$min = 1;
$max = 100;
$message = "";

// Check if the game has started (i.e., if a number has been set in the session)
if (!isset($_SESSION['randomNumber'])) {
    // If not, generate a new random number and initialize the guess count
    $_SESSION['randomNumber'] = rand($min, $max);
    $_SESSION['guessCount'] = 0;
    $message = "Welcome to the guessing game! I'm thinking of a number between $min and $max.";
}

// Process the user's guess when the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['guess'])) {
    // Sanitize and validate the user input
    $userGuess = filter_input(INPUT_POST, 'guess', FILTER_SANITIZE_NUMBER_INT);
    
    if (filter_var($userGuess, FILTER_VALIDATE_INT) !== false && $userGuess >= $min && $userGuess <= $max) {
        $_SESSION['guessCount']++; // Increment the guess count
        $randomNumber = $_SESSION['randomNumber'];
        
        if ($userGuess == $randomNumber) {
            $message = "Congratulations! You guessed the correct number ($randomNumber) in " . $_SESSION['guessCount'] . " attempts. <a href='index.php?playagain=true'>Play again?</a>";
            // Reset the game on win
            unset($_SESSION['randomNumber']);
            unset($_SESSION['guessCount']);
        } elseif ($userGuess < $randomNumber) {
            $message = "Your guess of $userGuess is too low. Try a higher number.";
        } else {
            $message = "Your guess of $userGuess is too high. Try a lower number.";
        }
    } else {
        $message = "Invalid guess. Please enter a number between $min and $max.";
    }
}

// Handle the "play again" request
if (isset($_GET['playagain']) && $_GET['playagain'] == 'true') {
    unset($_SESSION['randomNumber']);
    unset($_SESSION['guessCount']);
    // Redirect to the same page to restart the game cleanly
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Number Guessing Game</title>
    <style>
        body { font-family: sans-serif; max-width: 500px; margin: 20px auto; padding: 20px; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .info { background-color: #cce5ff; color: #004085; border-color: #b8daff; }
        .danger { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .form{display: flex; flex-direction: column; gap: 30px;}
        #guess{padding: 8px; font-size: 16px; border: 1px solid #ccc; border-radius: 4px;}
        .guess{padding: 8px; background-color: #076cd8; border: none; color: white; font-size: 16px; border-radius: 4px; cursor: pointer;}
        .guess:hover{background-color: #005bb5;}
        .guess:active{background-color: #004494;}
    </style>
</head>
<body>
    <h1>Guess the Number</h1>

    <?php if ($message): ?>
        <div class="message <?php 
            if (strpos($message, 'Congratulations') !== false) echo 'success';
            elseif (strpos($message, 'low') !== false || strpos($message, 'high') !== false) echo 'info';
            else echo 'danger';
        ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (!isset($_SESSION['randomNumber'])): ?>
        <!-- Game over or just started, show play again link -->
        <p><a href='index.php?playagain=true'>Start a new game</a></p>
    <?php else: ?>
        <!-- Game in progress, show the form -->
        <form method="POST" action="index.php" class="form">
            <label for="guess">Enter a number between <?php echo $min; ?> and <?php echo $max; ?>:</label>
            <input type="number" id="guess" name="guess" min="<?php echo $min; ?>" max="<?php echo $max; ?>" required autofocus>
            <button type="submit" class="guess">Guess</button>
        </form>
    <?php endif; ?>
</body>
</html>

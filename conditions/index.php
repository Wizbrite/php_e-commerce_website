<?php


$age = 20;
if ($age < 10) {
    echo "You are a baby.";
} else if ($age >10 && $age < 15) {
    echo "You are still a child.";
} else if( $age > 15 && $age < 18) {
    echo "You are a teen.";
} else {
    echo "You are an adult.";
}

switch ($age) {
    case 5:
        echo "<br>You are a baby.";
        break;
    case 10:
        echo "<br>You are still a child.";
        break;
    case 20:
        echo "<br>You are a young adult.";
        break;
    default:
        echo "<br>invalid age.";
        break;
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>
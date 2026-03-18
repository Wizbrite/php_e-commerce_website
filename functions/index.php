<?php
function display(){
    echo "This is my first function";
}

function parameter($name){
    echo "Hello, ${name}!";
}

function adder($num1, $num2){
    return $num1 + $num2;
}

echo adder(5,10);
echo "<br>";
echo date("Y-m-d H:i:s");
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
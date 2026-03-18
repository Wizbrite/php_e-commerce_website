
 <?php
    // $student1 = "John Doe";
    // $student2 = "Jane Smith";
    // $student3 = "Alice Johnson";
    // $student4 = "Bob Brown";
    // $student5 = "Charlie Davis";

    // echo "a small story about $student1, $student2, $student3, $student4, and $student5.";
    // echo "<br>Once upon a time, ${student1}, ${student2}, ${student3}, ${student4}, and ${student5} were all students at a school. 
    // <br> ${student1} told ${student2} to tell ${student3} that ${student4} killed ${student5}"    
    $students = array("John Doe", "Jane Smith", "Alice Johnson", "Bob Brown", "Charlie Davis");
    echo "a small story about $students[0], $students[1], $students[2], $students[3], and $students[4].";
    echo "<br>Once upon a time, ${students[0]}, ${students[1]}, ${students[2]}, ${students[3]}, and ${students[4]} were all students at a school. 
    <br> ${students[0]} told ${students[1]} to tell ${students[2]} that ${students[3]} killed ${students[4]}";

    echo "<br>";
    echo count($students);

    $color = ["red","blue","gray","black"];
    for($i=0; $i < count($color); $i++){
        echo "<br>$color[$i]";
    }
    echo "<br>";
    foreach($color as $color){
        echo "<br>$color";
    }

    $njini = ["name" => "Njini", "age" => 20, "course" => "Computer Science"];
    echo "<br>Name: " . $njini["name"];

    $students =[
        ["amida",20],
        ["Joyce",18],
        ["Alexia",19]
    ];

    foreach($students as $student){
        echo "<br>$student[0] is $student[1] years old.";
    }

    in_array("amida", $students);//checks if amida is in the students array

    $country = [
        ["Kenya", "Nairobi"],
        ["Uganda", "Kampala"],
        ["Tanzania", "Dodoma"]
    ];

    foreach($country as $c){
        echo "<br> $c[0] is an African Country and its capital is $c[1]";
    }
    ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta student="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
   
</body>
</html>
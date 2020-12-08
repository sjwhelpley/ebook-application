<?php
    // Connect to database
    $host = "pacu.cs.pitt.edu";
    $username = "sjw86";
    $password = "Student_4179356";
    $dbname = "sjw86";

    $connection = mysqli_connect($host, $username, $password, $dbname);
    
    if(!$connection) {
        die("Connection error: " . mysqli_connect_error());
    }

    // API key
    $apiKey = "AIzaSyBApj6HqcuhyEY1rU6hp31385oO2s6HBw4";
?>
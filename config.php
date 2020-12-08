<?php
    // Connect to database
    // Use your own credentials
    $host = "pacu.cs.pitt.edu";
    $username = "";
    $password = "";
    $dbname = "";

    $connection = mysqli_connect($host, $username, $password, $dbname);
    
    if(!$connection) {
        die("Connection error: " . mysqli_connect_error());
    }

    // API key
    // Get your own Google API key here
    $apiKey = "";
?>
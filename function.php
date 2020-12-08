<?php
    // Function to execute query and check for error
    function executeQuery($connection, $query) {
        $result = mysqli_query($connection, $query);
        if(!$result) {
            die("Query failed." . mysqli_error($connection));
        }
        return $result;
    }
?>
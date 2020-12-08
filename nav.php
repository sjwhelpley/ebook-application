<?php
    // https://getbootstrap.com/docs/4.0/components/navbar/
    // Navigation menu on top of all pages
    echo "<nav class='navbar navbar-expand-lg navbar-light bg-light'>
        <a class='navbar-brand' href='home.php'>eBooks</a>
        <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarNavDropdown' aria-controls='navbarNavDropdown' aria-expanded='false' aria-label='Toggle navigation'>
            <span class='navbar-toggler-icon'></span>
        </button>
        <div class='collapse navbar-collapse' id='navbarNavDropdown'>
            <ul class='navbar-nav'>
                <li class='nav-item active'>
                    <a class='nav-link' href='home.php'>Search <span class='sr-only'>(current)</span></a>
                </li>";

    // Button says log-in or Profile dropdown based on current user status
    if ($_SESSION["loggedin"]) {
        echo "<li class='nav-item dropdown'>
                <a class='nav-link dropdown-toggle' href='#' id='navbarDropdownMenuLink' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                Hello {$_SESSION["username"]}
                </a>
                <div class='dropdown-menu' aria-labelledby='navbarDropdownMenuLink'>
                    <a class='dropdown-item' href='checkedout.php'>Checked out items</a>
                    <a class='dropdown-item' href='toreadlist.php'>To Read List</a>
                    <a class='dropdown-item' href='myreviews.php'>My Reviews</a>
                    <form class='nav-form dropdown-item' method='POST'><input type='submit' name='log-out' id='log-out' class='btn btn-primary' value='Log Out' /></form>
                </div>
            </li>";
    }
    echo "</ul></div></nav>";

    // If press log out button, log out and send to login page
    if($_POST && isset($_POST['log-out'])) {
        $_SESSION['loggedin'] = false;
        header('location: login.php');
    }
?>
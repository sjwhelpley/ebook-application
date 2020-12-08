<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Register</title>
        <meta name="description" content="eBooks Register">
        <meta name="author" content="Samantha Whelpley">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        <link href="style.css" rel="stylesheet" type="text/css">
    </head>

    <body>
        <?php  
            // Set up necessary files & sessions
            session_start();
            require 'config.php';
            
            include 'function.php';

            // Initialize fields for account information & error messages
            $fnameError = $lnameError = $emailError = $usernameError = $passwordError = "";

            $_SESSION["loggedin"] = false;
            $_SESSION["user_id"] = "";
            $_SESSION["username"] = "";  

            // If register form has been submitted
            if(isset($_POST["register"])) {
                $errors = false;
                $fname = $_POST["fname"];
                $lname = $_POST["lname"];
                $email = $_POST["email"];
                $username = $_POST["username"];
                $password = $_POST["password"];

                // Patterns for validating data
                $namePattern = "/^[a-zA-Z' -]+$/"; // any combination of letters, apostrophes, spaces, and dashes
                $usernamePattern = "/^[a-zA-Z0-9_-]+$/"; // any combination of letters, numbers, dashes, and underscores
                $passwordPattern = "/^[a-zA-Z0-9@$!%*?&]+$/"; // any combination of letters, numbers, and special characters @$!%*?&

                // Validating data & setting errors
                // Tallying errors to help determine when all data is valid
                if(!preg_match($namePattern, $fname) || $fname == "") {
                    $fnameError = "<br><span class='error'>First name should only contain letters, spaces, dashes, and apostrophes.</span>";
                    $errors = true;
                }

                if(!preg_match($namePattern, $lname) || $lname == "") {
                    $lnameError = "<br><span class='error'>Last name should only contain letters, spaces, dashes, and apostrophes.</span>";
                    $errors = true;
                }

                if(!preg_match($usernamePattern, $username) || $username == "") {
                    $usernameError = "<br><span class='error'>Username should only contain letters, spaces, dashes, and underscores.</span>";
                    $errors = true;
                }

                if(!preg_match($passwordPattern, $password) || $password == "") {
                    $passwordError = "<br><span class='error'>Password should only contain letters, numbers, and the special characters @$!%*?&.</span>";
                    $errors = true;
                }

                // If there are no errors caught
                if(!$errors) { 
                    // Sanitize user input
                    $fname = mysqli_real_escape_string($connection, $fname);
                    $lname = mysqli_real_escape_string($connection, $lname);
                    $email = mysqli_real_escape_string($connection, $email);
                    $username = mysqli_real_escape_string($connection, $username);
                    $password = mysqli_real_escape_string($connection, $password);

                    // Get data associated with username from database
                    $query = "SELECT * FROM sjw86.final_users WHERE username = '$username';";
                    $result = executeQuery($connection, $query);

                    // If username is already in use
                    if (mysqli_num_rows($result) >= 1) { 
                        $usernameError = "<br><span class='error'>Username is already taken.</span>";
                        $errors = true;
                    } 

                    // Get data associated with email from database
                    $query = "SELECT * FROM sjw86.final_users WHERE email = '$email';";
                    $result = executeQuery($connection, $query);
                    
                    // If email is already in use
                    if (mysqli_num_rows($result) >= 1) { 
                        $emailError = "<br><span class='error'>Email is already taken.</span>";
                        $errors = true;
                    } 
                    
                    // If no errors with username or email being taken
                    if(!$errors) {
                        // Add user to database
                        $query = "INSERT INTO sjw86.final_users (first_name, last_name, email, username, password)
                        VALUES('$fname', '$lname', '$email', '$username', '$password');";
                        $result = executeQuery($connection, $query);

                        if($result) {
                            // Get user_id and save to session
                            $query = "SELECT * FROM sjw86.final_users WHERE username = '$username';";
                            $result = executeQuery($connection, $query);

                            $row = mysqli_fetch_assoc($result);
                            $_SESSION["loggedin"] = true;
                            $_SESSION["user_id"] = $row["user_id"];
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to categories page
                            header("location: home.php");
                        }
                    }

                    // Close connection
                    mysqli_close($connection);
                }
            }
        ?>

        <h1 id="register-heading">Register for eBooks</h1>

        <form class="register-form" action="register.php" method="POST">
            <input type="text" name="fname" placeholder="First name" <?php if(isset($_POST['fname'])) echo "value='" . htmlspecialchars($_POST['fname'], ENT_QUOTES) . "'";?>> <?php echo $fnameError; ?>
            <br>
            <input type="text" name="lname" placeholder="Last name" <?php if(isset($_POST['lname'])) echo "value='" . htmlspecialchars($_POST['lname'], ENT_QUOTES) . "'";?>> <?php echo $lnameError; ?>
            <br>
            <input type="email" name="email" placeholder="Email" <?php if(isset($_POST['email'])) echo "value='" . htmlspecialchars($_POST['email'], ENT_QUOTES) . "'";?>> <?php echo $emailError; ?>
            <br>
            <input type="text" name="username" placeholder="Username" <?php if(isset($_POST['username'])) echo "value='" . htmlspecialchars($_POST['username'], ENT_QUOTES) . "'";?>> <?php echo $usernameError; ?>
            <br>
            <input type="password" name="password" placeholder="Password"> <?php echo $passwordError; ?>
            <br>
            <!-- Submit button -->
            <input class="btn btn-primary" id="register" name="register" type="submit" value="Register">
            <br>
            <a class="btn btn-secondary m-2" href="login.php">Back to login</a>
        </form> 
    </body>
</html>
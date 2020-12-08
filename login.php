<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Login</title>
        <meta name="description" content="eBooks Login">
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
            $usernameError = $passwordError = "";
            $usernameCorrect = false;

            $_SESSION["loggedin"] = false;
            $_SESSION["user_id"] = "";
            $_SESSION["username"] = "";  

            // If login form has been submitted
            if(isset($_POST["login"])) {
                $username = $_POST['username'];
                $password = $_POST['password'];

                // Get data associated with username from database
                $query = "SELECT * FROM sjw86.final_users WHERE username = '$username';";
                $result = executeQuery($connection, $query);

                // If no rows with that usename exist
                if(mysqli_num_rows($result) === 0) {
                    $usernameError = "<br><span class='error'>Username does not exist.</span>";
                } else if (mysqli_num_rows($result) > 1) { // If more than row with that username exists
                    $usernameError = "<br><span class='error'>Username is not unique - have admin check database.</span>";
                } else { // If there is one instance of that username
                    $usernameCorrect = true; // Indicate we can save that username to be sticky
                    while($row = mysqli_fetch_assoc($result)) {
                        // Check to see if password is correct - display error if isn't
                        if($password === $row['password']) {
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["user_id"] = $row["user_id"];
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to categories page
                            header("location: home.php");
                        } else {
                            $passwordError = "<br><span class='error'>Password is incorrect.</span>";
                        }
                    }
                }

                // Close connection
                mysqli_close($connection);
            }
            
            // If registering, go to Register page
            if(isset($_POST["register"])) {
                header("location: register.php");
            }
        ?>

        <h1 id="login-heading">Login to eBooks</h1>

        <form class="login-form" action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" <?php if(isset($_POST['username']) && $usernameCorrect === true) echo "value='" . htmlspecialchars($_POST['username'], ENT_QUOTES) . "'";?>> <?php echo $usernameError; ?>
            <br>
            <input type="password" name="password" placeholder="Password"> <?php echo $passwordError; ?>
            <br>
            <!-- Submit buttons -->
            <input class="btn btn-primary" id="login" name="login" type="submit" value="Login">
            <br>
            <input class="btn btn-secondary m-2" id="register" name="register" type="submit" value="Register">
        </form>
    </body>
</html>
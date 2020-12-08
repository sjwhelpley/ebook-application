<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>To Read List</title>
    <meta name="description" content="eBooks To Read List">
    <meta name="author" content="Samantha Whelpley">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet" type="text/css">
  </head>

  <body class="text-center">
    <?php
      // Set up necessary files & sessions
      session_start();
      require 'config.php';

      include 'nav.php';
      include 'function.php';
    ?>

    <h1 class="mt-2">To Read List</h1>

    <?php
      $user_id = intval($_SESSION["user_id"]);

      // If user wants to remove book from reading list
      if(isset($_POST["remove-list"])) {
        $book_id = $_POST["item-id"];
        $query = "DELETE FROM sjw86.final_read_list WHERE user_id = $user_id AND book_id = '$book_id';";
        $result = executeQuery($connection, $query);

        header("location: toreadlist.php");
      }

      // Get reading list items associated with username from database
      $query = "SELECT * FROM sjw86.final_read_list WHERE user_id = $user_id;";
      $result = executeQuery($connection, $query);
      
      // If no items in list
      if(mysqli_num_rows($result) === 0) {
          echo "<p class='empty'>Nothing in list</p>";
      } else {
        echo "<div class='list'>";
        while($row = mysqli_fetch_assoc($result)) {
            // Get book information for each list item from API
            $book_id = urlencode($row["book_id"]);
            
            $googleBooksResp = file_get_contents("https://www.googleapis.com/books/v1/volumes/".$book_id);
            $bookData = json_decode($googleBooksResp, true);
            
            $title = $bookData['volumeInfo']['title'];
            $subtitle = $bookData['volumeInfo']['subtitle'];
            $authors = $bookData['volumeInfo']['authors'];
            $authorsString = implode(", ", $authors);
            $imgURL = $bookData['volumeInfo']['imageLinks']['thumbnail'];

            // Check if currently checked out
            $query = "SELECT * from sjw86.final_checked_out WHERE book_id = '$book_id' ORDER BY end_date DESC;";
            $resultStatus = executeQuery($connection, $query);

            $bookStatus = "";
            if(mysqli_num_rows($resultStatus) === 0) { // If book is currently not checked out (no records at all)
              // Book is available
              $bookStatus = "<p style='color:green'>Available</p>";
            } else {
              // Check record with most recent date and see if check out is still active
              $row = mysqli_fetch_assoc($resultStatus);
              date_default_timezone_set('EST');
              $today = date("Y-m-d");
              if($row["end_date"] >= $today) { // Book is still checked out as of today
                $bookStatus = "<p style='color:red'>Checked out until {$row['end_date']}</p>";
              } else { 
                // Last check out has finished - book available
                $bookStatus = "<p style='color:green'>Available</p>";
              }
            }

            echo "<div class='list-item'>
              <img class='list-img' src=$imgURL />
              <a href='book.php?id=$book_id'>
                <h2>$title</h2>
              </a>
              <h3>$subtitle</h3>
              <h4 class='font-italic'>$authorsString</h4>
              $bookStatus
              <form class='remove-list' action='toreadlist.php' method='POST'>";
            
            // Pass database ID as value in a hidden input
            // https://stackoverflow.com/questions/12668141/adding-a-delete-button-in-php
            echo "<input type='hidden' name='item-id' value=$book_id>
                <input class='btn btn-primary' id='remove-list' name='remove-list' type='submit' value='Remove'>
              </form>
            </div>";
        }
        echo "</div";
      }
    ?>
    
    <!-- jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
  </body>
</html>
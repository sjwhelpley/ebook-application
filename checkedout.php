<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Checked Out eBooks</title>
    <meta name="description" content="Checked Out eBooks">
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

      $user_id = intval($_SESSION["user_id"]);

      // If ending rental, update database entry with end_date as today's date
      if(isset($_POST["end-rental"])) {
        date_default_timezone_set('EST');
        $record_id = $_POST["item-id"];
        $new_end_date = date("Y-m-d");
        echo "$new_end_date";
        $query = "UPDATE sjw86.final_checked_out SET end_date = '$new_end_date' WHERE record_id = $record_id";
        $result = executeQuery($connection, $query);

        header("location: checkedout.php");
      }
    ?>
    
    <h1 class="mt-2">Checked Out Books<h1>
    <h2>Current Rentals</h2>

    <?php
      // Get all checked out books that are still active (end date is greater than or equal to today)
      $query = "SELECT * FROM sjw86.final_checked_out WHERE user_id = $user_id AND end_date >= NOW();";
      $result = executeQuery($connection, $query);
      
      // If no checked out items
      if(mysqli_num_rows($result) === 0) {
          echo "<p class='empty'>No current checked out books</p>";
      } else {
          // Display all books - get information from API
          echo "<div class='list'>";
          while($row = mysqli_fetch_assoc($result)) {
              $record_id = $row["record_id"];
              $book_id = urlencode($row["book_id"]);
              $start_date = $row["start_date"];
              $end_date = $row["end_date"];
              
              $googleBooksResp = file_get_contents("https://www.googleapis.com/books/v1/volumes/".$book_id);
              $bookData = json_decode($googleBooksResp, true);
              
              $title = $bookData['volumeInfo']['title'];
              $subtitle = $bookData['volumeInfo']['subtitle'];
              $authors = $bookData['volumeInfo']['authors'];
              $authorsString = implode(", ", $authors);
              $imgURL = $bookData['volumeInfo']['imageLinks']['thumbnail'];

              echo "<div class='list-item'>
                <img class='list-img' src=$imgURL />
                  <a href='book.php?id=$book_id'>
                    <h2>$title</h2>
                  </a>
                  <h3>$subtitle</h3>
                  <h4 class='font-italic'>$authorsString</h4>
                  <p>$start_date to $end_date</p>
                  <form class='end-rental' action='checkedout.php' method='POST'>";

              // Pass database ID as value in a hidden input
              // https://stackoverflow.com/questions/12668141/adding-a-delete-button-in-php
              echo "<input type='hidden' name='item-id' value=$record_id>
                    <input class='btn btn-primary' id='end-rental' name='end-rental' type='submit' value='End Rental'>
                    </form>
                  </div>";
          }
          echo "</div>";
      }
    ?>

    <hr>
    <h1>Past Rentals</h1>

    <?php
      // Get all checked out books that are NO LONGER active (end date less than today)
      $query = "SELECT * FROM sjw86.final_checked_out WHERE user_id = $user_id AND end_date < NOW() ORDER BY end_date DESC;";
      $result = executeQuery($connection, $query);
      
      // If no checked out items
      if(mysqli_num_rows($result) === 0) {
          echo "<p class='empty'>No past checked out books</p>";
      } else {
        // Display all books - get information from API
        echo "<div class='list'>";
        while($row = mysqli_fetch_assoc($result)) {
            $book_id = urlencode($row["book_id"]);
            $start_date = $row["start_date"];
            $end_date = $row["end_date"];
            
            $googleBooksResp = file_get_contents("https://www.googleapis.com/books/v1/volumes/".$book_id);
            $bookData = json_decode($googleBooksResp, true);
            
            $title = $bookData['volumeInfo']['title'];
            $subtitle = $bookData['volumeInfo']['subtitle'];
            $authors = $bookData['volumeInfo']['authors'];
            $authorsString = implode(", ", $authors);
            $imgURL = $bookData['volumeInfo']['imageLinks']['thumbnail'];

            echo "<div class='list-item'>
              <img class='list-img' src=$imgURL />
              <a href='book.php?id=$book_id'>
                <h2>$title</h2>
              </a>
              <h3>$subtitle</h3>
              <h4 class='font-italic'>$authorsString</h4>
              <p>$start_date to $end_date</p>
            </div>";
        }
        echo "</div>";
      }

      // Close connection
      mysqli_close($connection);
    ?>
    
    <!-- jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
  </body>
</html>
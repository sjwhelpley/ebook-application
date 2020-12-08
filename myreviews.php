<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>My Reviews</title>
    <meta name="description" content="eBooks My Reviews">
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

    <h1 class="mt-2">My Reviews</h1>

    <?php
      $user_id = intval($_SESSION["user_id"]);

      // If user wants to delete a review
      if(isset($_POST["delete-review"])) {
        $review_id = $_POST["item-id"];
        $query = "DELETE FROM sjw86.final_reviews WHERE review_id = $review_id;";
        $result = executeQuery($connection, $query);

        header("location: myreviews.php");
      }
      
      // If user wants to update a review
      if(isset($_POST["update-review"])) {
        $rating = intval($_POST["rating"]);
        $review = $_POST["review"];
        $review_id = $_POST["item-id"];

        // Sanitize data
        $review = mysqli_real_escape_string($connection, $review);

        // Insert into database
        $query = "UPDATE sjw86.final_reviews SET num_rating = $rating, review_text = '$review' WHERE review_id = $review_id;";
        $result = executeQuery($connection, $query);

        header("location: myreviews.php");
      }

      // Get all user's reviews from database
      $query = "SELECT * FROM sjw86.final_reviews WHERE user_id = $user_id;";
      $result = executeQuery($connection, $query);

      // If no checked out items
      if(mysqli_num_rows($result) === 0) {
      echo "<p class='empty'>No reviews yet</p>";
      } else {
        while($row = mysqli_fetch_assoc($result)) {
          $review_id = $row["review_id"];
          $book_id = urlencode($row["book_id"]);
          $rating = $row["num_rating"];
          $review = $row["review_text"];
          
          // For each review, get book information from API
          $googleBooksResp = file_get_contents("https://www.googleapis.com/books/v1/volumes/".$book_id);
          $bookData = json_decode($googleBooksResp, true);
          
          $title = $bookData['volumeInfo']['title'];
          $subtitle = $bookData['volumeInfo']['subtitle'];
          $authors = $bookData['volumeInfo']['authors'];
          $authorsString = implode(", ", $authors);

          echo "<div class='list-item'>
            <a href='book.php?id=$book_id'>
              <h2>$title</h2>
            </a>
            <h3>$subtitle</h3>
            <h4 class='font-italic'>$authorsString</h4>
            <p><strong>Rating:</strong> $rating / 5</p>
            <p><strong>Review:</strong> $review</p>
            <form class='edit-review' action='myreviews.php' method='POST'>";
          
          // Pass database ID as value in a hidden input
          // https://stackoverflow.com/questions/12668141/adding-a-delete-button-in-php
          echo "<input type='hidden' name='item-id' value=$review_id>
              <input type='hidden' name='rating' value=$rating>
              <input type='hidden' name='review' value='$review'>
              <input type='hidden' name='title' value='$title'>
              <input class='btn btn-primary' id='edit-review' name='edit-review' type='submit' value='Edit'>
            </form>
            <form class='delete-review mt-2' action='myreviews.php' method='POST'>
              <input type='hidden' name='item-id' value=$review_id>
              <input class='btn btn-primary' id='delete-review' name='delete-review' type='submit' value='Delete'>
            </form>
          </div>";
        }
      }

      // If user wants to edit a review - add edit form to HTML
      if(isset($_POST["edit-review"])) {
        $review_id = $_POST["item-id"];
        $rating = $_POST["rating"];
        $review = $_POST["review"];
        $title = $_POST["title"];

        echo "<h2>Update review of $title</h2>
        <form class='update-review' action='myreviews.php' method='POST'>
          <input type='hidden' name='item-id' value=$review_id>
          <label for='rating'>Number rating (1-5):</label>
          <input type='number' name='rating' id='rating' value=$rating min='1' max='5'>
          <br>
          <textarea id='review' name='review' rows='4' cols='50' placeholder='Update review of book here' required>$review</textarea>
          <br>
          <input class='btn btn-primary' id='update-review' name='update-review' type='submit' value='Edit Review'>
        </form>
        <br>";
      }

      // Close connection
      mysqli_close($connection);
    ?>
    
    <!-- jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
  </body>
</html>
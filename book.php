<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>eBooks</title>
    <meta name="description" content="eBooks">
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

      // Get book information from Google Books API
      $book_id = urlencode($_GET['id']);
      $googleBooksResp = file_get_contents("https://www.googleapis.com/books/v1/volumes/".$book_id);
      $bookData = json_decode($googleBooksResp, true);
      
      $title = $bookData['volumeInfo']['title'];
      $subtitle = $bookData['volumeInfo']['subtitle'];
      $authors = $bookData['volumeInfo']['authors'];
      $authorsString = implode(", ", $authors);
      $description = $bookData['volumeInfo']['description'];
      $imgURL = $bookData['volumeInfo']['imageLinks']['thumbnail'];

      // Check if currently checked out
      $query = "SELECT * from sjw86.final_checked_out WHERE book_id = '$book_id' ORDER BY end_date DESC;";
      $resultStatus = executeQuery($connection, $query);

      $bookStatus = "";
      if(mysqli_num_rows($resultStatus) === 0) { // If book is currently not checked out (no records at all)
        // Book is available
        $bookStatus = array(true, "<p style='color:green'>Available</p>");
      } else {
        // Check record with most recent date and see if check out is still active
        $row = mysqli_fetch_assoc($resultStatus);
        date_default_timezone_set('EST');
        $today = date("Y-m-d");
        if($row["end_date"] >= $today) { // Book is still checked out as of today
          $bookStatus = array(false, "<p style='color:red'>Checked out until {$row['end_date']}</p>");
        } else { 
          // Last check out has finished - book available
          $bookStatus = array(true, "<p style='color:green'>Available</p>");
        }
      }
      
      $user_id = intval($_SESSION["user_id"]);
      $addToListRes = "";
      $checkOutRes = "";

      // If user is checking out the book
      if(isset($_POST["check-out"])) {
        // Complete check out process - insert into database
        $query = "INSERT INTO sjw86.final_checked_out (user_id, book_id, start_date, end_date)
          VALUES($user_id, '$book_id', NOW(), DATE_ADD(NOW(),INTERVAL 1 WEEK));";
        $result = executeQuery($connection, $query);

        if($result) {
          $checkOutRes = "<p>Successfully checked out. You have 1 week to read!</p>";
        } else {
          $checkOutRes = "<p>Check out failed. Try again</p>";
        }
      }

      // If user is adding book to "to read list"
      if(isset($_POST["add-list"])) {
        // Check if already in list
        $query = "SELECT * from sjw86.final_read_list WHERE user_id = $user_id AND book_id = '$book_id'";
        $result = executeQuery($connection, $query);

        if(mysqli_num_rows($result) === 0) { // If not in list
          // Complete process - insert into database
          $query = "INSERT INTO sjw86.final_read_list (user_id, book_id)
            VALUES($user_id, '$book_id');";
        
          $result = executeQuery($connection, $query);

          if($result) {
            $addToListRes = "<p>Successfully added to list</p>";
          } else {
            $addToListRes = "<p>Adding to list failed</p>";
          }
        } else { // It exists in list already
          $addToListRes = "<p>Already added to list</p>";
        }
      }

      // If user is adding book review
      if(isset($_POST["add-review"])) {
        $rating = intval($_POST["rating"]);
        $review = $_POST["review"];

        // Sanitize data
        $review = mysqli_real_escape_string($connection, $review);

        // Insert into database
        $query = "INSERT INTO sjw86.final_reviews (user_id, book_id, num_rating, review_text, date_posted)
        VALUES($user_id, '$book_id', $rating, '$review', NOW());";
        $result = executeQuery($connection, $query);

        header("location: book.php?id=".$book_id);
      }
    ?>

    <!-- Back-link to search page - queries last search term so that user can resume same search - 
    if coming from a different page (not having searched), sends empty string which is handled on home.php -->
    <a class="back-link" href="home.php?search-terms=<?php echo $_SESSION["search-terms"] ?>">Back to search results</a>

    <div class="book">
      <img src=<?php echo $imgURL; ?> alt="Book cover" />
      <h1><?php echo $title; ?></h1>
      <h2><?php echo $subtitle; ?></h2>
      <h3 class="font-italic"><?php echo $authorsString; ?></h3>
      <p><?php echo $description; ?></p>
      <?php echo $bookStatus[1]; ?>
    </div>
    
    <?php if($bookStatus[0]) : ?>
    <form class="check-out" action="book.php?id=<?php echo $book_id; ?>" method="POST">
      <input class="btn btn-primary" id="check-out" name="check-out" type="submit" value="Check Out">
    </form>
    <?php endif; ?>

    <?php echo $checkOutRes ?>

    <form class="add-list" action="book.php?id=<?php echo $book_id; ?>" method="POST">
      <input class="btn btn-primary mt-2" id="add-list" name="add-list" type="submit" value="Add to List">
    </form>

    <?php echo $addToListRes ?>

    <hr>

    <h2 class="mt-2">Reviews</h2>

    <?php 
      // Display all reviews of this specific book
      $query = "SELECT * from sjw86.final_reviews WHERE book_id = '$book_id'";
      $result = executeQuery($connection, $query);

      if(mysqli_num_rows($result) === 0) { // If no reviews
        echo "<p class='empty'>No reviews yet</p>";
      } else {
        // Calculate average review
        $count = 0;
        $totalReviews = mysqli_num_rows($result);
        while($row = mysqli_fetch_assoc($result)) {
          $count += $row["num_rating"];
        }
        $avgReview = $count / $totalReviews;
        echo "<h3>Average Review: $avgReview</h3>";

        // Display all reviews
        mysqli_data_seek($result, 0);
        while($row = mysqli_fetch_assoc($result)) {
          $num_rating = $row["num_rating"];
          $review_text = $row["review_text"];
          $date = $row["date_posted"];

          echo "<div class='review'><p>Rating: $num_rating / 5</p><p>Review: $review_text</p><p>Reviewed on $date</p></div>";
        }
      }
      
      // Close connection
      mysqli_close($connection);
    ?>

    <form class="add-review" action="book.php?id=<?php echo $book_id; ?>" method="POST">
      <label for="rating">Number rating (1-5):</label>
      <input type="number" name="rating" id="rating" value="1" min="1" max="5">
      <br>
      <textarea id="review" name="review" rows="4" cols="50" placeholder="Write review of book here" required></textarea>
      <br>
      <input class="btn btn-primary" id="add-review" name="add-review" type="submit" value="Add Review">
    </form>

    <!-- jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
  </body>
</html>
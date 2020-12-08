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
      
      // Redirect to login page if not logged in
      if(!$_SESSION["loggedin"]) {
        header("location: login.php");
      }

      include 'nav.php';
      include 'function.php';
    ?>

    <h1 class="mt-2">Search for eBooks</h1>
    <h3>Quick and easy access to a large array of books. Reserve, review, and save them for later!</h3>

    <form class="search" action="home.php" method="POST">
      <input type="text" class="search-bar" name="search" placeholder="Search by title" <?php if(isset($_POST["search"])) { echo "value='" . htmlspecialchars($_POST["search"], ENT_QUOTES) . "'"; } else if(isset($_GET["search-terms"])) { echo "value='" . htmlspecialchars($_GET["search-terms"], ENT_QUOTES) . "'"; }?>>
      <input class="btn btn-primary" id="search-submit" type="submit" value="Submit">
    </form>

    <?php
      // If search terms in the URL and they are not empty, populate search results
      if(isset($_GET["search-terms"]) && $_GET["search-terms"] != "") {
        // Get book information from API and display it
        $searchTermsURL = urlencode($_GET["search-terms"]);
        $googleBooks = file_get_contents("https://www.googleapis.com/books/v1/volumes?q=".$searchTermsURL."&maxResults=40&filter=ebooks&key=".$apiKey);
        $booksData = json_decode($googleBooks, true);

        // Make sure max in for loop does not go beyond number of items from API
        $max = 39;
        if($booksData['totalItems'] < 40) {
          $max = $booksData['totalItems'];
        }

        echo "<div class='search-results'>";
        for($x = 0; $x <= $max; $x++) {
          $id = $booksData['items'][$x]['id'];
          $title = $booksData['items'][$x]['volumeInfo']['title'];
          $authors = $booksData['items'][$x]['volumeInfo']['authors'];
          $authorsString = implode(", ", $authors);
          $imgURL = $booksData['items'][$x]['volumeInfo']['imageLinks']['thumbnail'];

          echo "<div class='book-result'>";
          if(!is_null($imgURL)) {
            echo "<img src=$imgURL alt='Book cover'>";
          }
          echo "<div style='padding=10px'><h5>$title</h5><p class='font-italic'>$authorsString</p><a href='book.php?id=$id' class='btn btn-primary'>See more</a></div></div>";
        }
        echo "</div>";
      }

      // If search form submitted
      if($_POST) {
        // Get book information from API and display it
        // Save search terms as session variable in case user comes back to this page later
        $searchTerms = urlencode($_POST["search"]);
        $_SESSION["search-terms"] = $searchTerms;
        $googleBooks = file_get_contents("https://www.googleapis.com/books/v1/volumes?q=".$searchTerms."&maxResults=40&filter=ebooks&key=".$apiKey);
        $booksData = json_decode($googleBooks, true);

        // Make sure max in for loop does not go beyond number of items from API
        $max = 39;
        if($booksData['totalItems'] < 40) {
          $max = $booksData['totalItems'];
        }

        echo "<div class='search-results'>";
        for($x = 0; $x <= $max; $x++) {
          $id = $booksData['items'][$x]['id'];
          $title = $booksData['items'][$x]['volumeInfo']['title'];
          $authors = $booksData['items'][$x]['volumeInfo']['authors'];
          $authorsString = implode(", ", $authors);
          $imgURL = $booksData['items'][$x]['volumeInfo']['imageLinks']['thumbnail'];

          echo "<div class='book-result'>";
          if(!is_null($imgURL)) {
            echo "<img src=$imgURL width='150' alt='Book cover'>";
          }
          echo "<div style='padding=10px'><h5>$title</h5><p class='card-text'>$authorsString</p><a href='book.php?id=$id' class='btn btn-primary'>See more</a></div></div>";
        }
        echo "</div>";
      }
    ?>
    
    <!-- jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
  </body>
</html>
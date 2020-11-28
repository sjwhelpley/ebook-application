# eBook website
This project is a website that allows users to reserve, review, and save ebooks.

Book information is gotten using the Google Books API.

Services:
- Check out ebooks. When a user finds a book they want to read, they are able to check it out. Each book can only be checked out by one user at a time in one week intervals.
- Review ebooks. On the page for each book, all the reviews for that specific book will be displayed. Users can add reviews through a form on this page. Users are also able to go to their "My Reviews" page in the dropdown section of the navbar and view all the reviews they have made.
- Save ebooks for later. Books can be added to a "To Read List" that can be viewed through the dropdown "To Read List" link in the navbar. Users can delete items from the list page as necessary.

To use:
- This site uses PHP, so you must download the code and run using a server like WAMP. It also requires a connection to a MySQL database (script will be uploaded later).
- Everyone must be logged in to use this website. User accounts can be registered, or there is a test account to use:
    Username: test
    Password: test

- Main page: home.php (search)
    - Here you can use search terms to find books by title. The top 40 results are displayed, and any can clicked on to be taken to the specific book's detail page.
    - If you come back to this page from a specific book page, your last search term will be saved and results from that search will still be shown.
- Book page: book.php
    - This will show information about whatever specific book you clicked on. You can read information like the description and view the average rating and all (anonymous) user reviews.
    - Actions on this page include:
        - Check out: Can only be checked out by one user at once. Each user is given a week to read. If currently checked out, it will tell the user what day the person will be done with it.
        - Add to List: Can be viewed through "To Read List" page. A book can only be added to the list once.
        - Add Review: Can add a rating from 1-5 and a written review.
- Checked Out - checkedout.php
    - View all currently checked out books - can end the rental early if desired.
    - View past checked out books.
- To Read List - toreadlist.php
    - View all books put in this list.
    - Can remove any of the books by clicking on "Delete" button.
- My Reviews - myreviews.php
    - View all reviews user has made.
    - Can edit review by clicking on the "Edit" button.
    - Can remove any of them by clicking on "Delete" button.

Project process:
- One of the most challenging parts was incorporating the API, because the Google Books documentation was not entirely helpful or accurate. However, using it has allowed me a lot of flexibilty in the data users are able to interact with. I only needed to store the Google Books ID for each book in the various MySQL tables I created that dealt with the books.
- Another challenging aspect was just getting used to the logic neccessary to get things working for a rental system. I had to incorporate dates so that rentals did not overlap, so using the correct date format and comparing them accurately was important. 

Other thoughts:
- I was able to add everything I had originally planned into this project, as well as figure out a few features that would make this website seem more realistic and functional. 
- Having finished, I would have like to see how I could incorporate JavaScript more alongside PHP. I could have also added more to each of the services (checking out, for instance) to make it more flexibile.
    - Could create a waitlist for renting ebooks.
    - Pull in reviews from other sites.

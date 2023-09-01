<?php
/*******w******** 
    Name: LAI PING SHUM
    Date: 2023-08-31
    Description: Final project
****************/

require_once('connect.php');
require_once('utils.php');
    
// Retrieve the search term from the query string
$searchTerm = isset($_GET['p']) ? $_GET['p'] : null;

// Initialize an empty array to hold the search results
$pages = array();

// Check if a search term was provided
if (!empty($searchTerm)) {
    // Query to get all pages that match the search term
    $querypage = "SELECT * FROM pages WHERE title LIKE :search_term OR content LIKE :search_term ORDER BY title";
    $statementpage = $db->prepare($querypage);
    $statementpage->bindValue(':search_term', '%' . $searchTerm . '%');
    $statementpage->execute();
    $pages = $statementpage->fetchAll();
}
    require('header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <title>Category Search</title>
</head>
<body>
    <div id="container">
        <main>
            <h2>Search result</h2><br>
            
            <ul>
                <?php foreach ($pages as $page): ?>  
                    <li><a href="index.php?p=<?php echo $page['permalink']; ?>"><?php echo $page['title']; ?></a></li>
                <?php endforeach; ?>
            </ul>

             <!-- No search results found -->
            <?php if (empty($pages)): ?>
                <p>No search results found.</p> 
            <?php endif; ?>

            <div class="ReturnMain">
                </br><a href="index.php" class="button">Return to Main page</a>
            </div>
        </main>
    </div>
</body>
</html>


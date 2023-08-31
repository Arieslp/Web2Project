<?php

/*******w******** 
    
    Name: LAI PING SHUM
    Date: 2023-08-31
    Description: Final project

****************/
    
    if (!isset($_SESSION)){
        session_start();
    }

    require_once('connect.php');    
    //require('authenticate.php');

    // Query SQL
    $query = "SELECT * FROM pages";

    // A PDO::Statement is prepared from the query.
    $statement = $db->prepare($query);

    // Execution on the DB server is delayed until we execute().
    $statement->execute(); 
           
    // Query page title
    $titleQuery = "SELECT page_id, title FROM pages";
    $titleStatement = $db->prepare($titleQuery);
    $titleStatement->execute(); 

    require('header.php'); // Include header file

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Black+Ops+One&display=swap" rel="stylesheet">  
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>  
    <title>Welcome to UareSpecial!</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <div id="container">
		
        <!-- Main Content -->    
        <main>      
            <div id="admin_funcbar">
                <nav>
                    <a href="new.php" id="but_newpage" class="button">Create New Page</a>
                    <a href="sortlist.php" id="but_sortlistpage" class="button">Sort Page List</a>
                    <a href="categorylist.php" id="but_categorymainpage" class="button">Categor List</a>
                    <?php if (isset($_SESSION['user_right']) && $_SESSION['user_right'] === 'ADMIN'): ?>
                        <a href="user.php" id="but_userpage" class="button">User List</a>
                    <?php endif; ?>
                    
                </nav>
            </div>

			<div id="content_admin">                
                    <h2>Administration Page</h2></br>
                    <!-- <a href="new.php" id="but_newpost" class="button">Create New Post</a> -->
                    
                <!-- loop the page title and add in <ul><li> etc -->
                <ul>
                <?php
                    // Loop through the results and generate menu items
                    if($titleStatement->rowCount() > 0): 
                        while ($row = $titleStatement->fetch()) {
                            $title = htmlspecialchars($row['title']);
                            $page_id = htmlspecialchars($row['page_id']);

                            // Generate a menu item with the fetched title and permalink
                            echo '<li><a href="edit.php?id=' . $page_id . '">' . $title . '</a></li></br>';
                        }
                    else:
                        echo "<p>No page title found! </p>";
                    endif ?> 
                </ul>
		    </div>                         
        </main>         
    </div>
</body>
</html>


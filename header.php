<?php

/*******w******** 
    
    Name: LAI PING SHUM
    Date: 2023-08-31
    Description: Final project

****************/

  

    // 	Check the GET and ensure it's set
	//	If a count has been supplied, display the selected animal count times   
    $optionId = array('options' => array('default'=>1));
    $pageId = filter_input(INPUT_GET, 'page_id', FILTER_VALIDATE_INT, $optionId);

    // Query SQL
    $query = "SELECT * FROM pages";

    // A PDO::Statement is prepared from the query.
    $statement = $db->prepare($query);

    // Execution on the DB server is delayed until we execute().
    $statement->execute(); 

    // Initialize variable
    $page_title = "";        
    $page_content = "";    
    $page_image_filename = "";    

    
    if (isset($_GET['p'])){
        $page_permalink = htmlspecialchars($_GET['p']);

        // Query SQL
        $query = "SELECT * FROM pages WHERE permalink = ?";
        $statement1 = $db->prepare($query);
        $statement1->execute([$page_permalink]);

        if ($statement1->rowCount() > 0) {
            $row = $statement1->fetch();
            $page_title = htmlspecialchars($row['title']);
            $page_content = htmlspecialchars($row['content']);
            $page_image_filename = htmlspecialchars($row['image_filename']);            
        } 
        else {
            echo "Error: Page not found.";
            exit;
        } 
    }
    else {
            // Set default permalink as home
            $default_permalink = 'home';
                   
            $query = "SELECT * FROM pages WHERE permalink = ?";
            $statement2 = $db->prepare($query);
            $statement2->execute([$default_permalink]);
        
            if ($statement2->rowCount() > 0) {
                $row = $statement2->fetch();
                $page_title = htmlspecialchars($row['title']);
                $page_content = htmlspecialchars($row['content']);
                $page_image_filename = htmlspecialchars($row['image_filename']);                    
            } else {
                echo "You should begin by adding a page with a permalink of 'home' using the admin page";
                exit;          
            }
        }
               
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Black+Ops+One&display=swap" rel="stylesheet">  
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>         
    <link rel="stylesheet" href="main.css">
    <title>Welcome to UareSpecial!</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <div id="container">
		<!-- Navigation bar -->        
        <header class="header">
            <div class="headerbackground">
                <div>
                    <a href="admin.php" class="admin-link">Admin</a>
                </div>                
                <div class="top-left">UareSpecial CMS</div>
				<div class="bottom-right">Content Management System</div>
            </div>

            <div id="menubar">
                <nav>
                    <?php
                    // Loop through the results and generate menu items
                    if($statement->rowCount() > 0): 
                        while ($row = $statement->fetch()) {
                            $menuTitle = htmlspecialchars($row['title']);
                            $menuPermalink = htmlspecialchars($row['permalink']);

                            // Generate a menu item with the fetched title and permalink
                            echo '<a href="index.php?p=' . $menuPermalink . '">' . $menuTitle . '</a>';
                        }
                    else:
                        echo "<p>No page title found! </p>";
                    endif ?>                          
                </nav>
			</div>	
        </header>
        <div>
				<!--<input type="search" name="search" id="search" />
				<button type="submit">Search</button>-->

                <nav class="navbar bg-body-tertiary">
                    <div class="container-fluid">                    
                    <form class="d-flex" role="search">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                    </div>
                </nav>
			</div>     
    </div>
</body>
</html>

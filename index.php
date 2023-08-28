<?php

/*******w******** 
    
    Name: LAI PING SHUM
    Date: 2023-08-31
    Description: Final project

****************/

    require('connect.php');    

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
        } else {
            echo "Error: Page not found.";
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
    <link rel="stylesheet" href="main.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Black+Ops+One&display=swap" rel="stylesheet">       
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
                            $title = htmlspecialchars($row['title']);
                            $permalink = htmlspecialchars($row['permalink']);

                            // Generate a menu item with the fetched title and permalink
                            echo '<a href="index.php?p=' . $permalink . '">' . $title . '</a>';
                        }
                    else:
                        echo "<p>No page title found! </p>";
                    endif ?>                          
                </nav>
			</div>	
        </header>
        <div>
				<input type="text" name="search" id="search" />
				<button type="submit">Search</button>
			</div>     

        <!-- Main Content -->    
        <main>      
			<div id="content">
                <div class="content-block">
                    <h1><?php echo $page_title ?></h1></br>
                </div>
                <div class="content-block">
				    <p><?php echo $page_content ?></p></br>
                </div>
                <div class="content-block">
                    <?php if ($page_image_filename != ""): ?>
                        <img src="images/<?php echo $page_image_filename ?>" alt="image" class="image" />
                    <?php endif; ?>
                </div>    
		    </div>                         
        </main>         
    </div>
</body>
</html>

.<?php

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

    $queryCatList = "SELECT * FROM categories";    
    $statementCatList = $db->prepare($queryCatList);    
    $statementCatList->execute(); 
    $categories = $statementCatList->fetchAll();

    // Initialize variable
    $page_title = "";        
    $page_content = "";    
    $page_image_filename = "";
    $category_id = "";
    $category_name = "";    

    
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
      require('header.php'); // Include header file         
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./main.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Black+Ops+One&display=swap" rel="stylesheet">   
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"> -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>    
    <title>Welcome to UareSpecial!</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <div id="container">
		<header>            
            <nav class="navbar bg-body-tertiary">
                    <div class="container-fluid">
                        <!-- Add a sort function to allow user sort the output by category_name -->
                        <p>Navigator by Category</p>
                        <!-- <a href="categorysearchall.php" class="navbar-brand"> All </a> -->
                        <!-- Loop through the results and generate menu items -->
                        

                        <form method="get" action="categorySearch.php" id="categorySearchForm">
                            <!-- <label for="category_id">Select Category:</label> -->
                                <select name="category_id" id="category_id">        
                                    <!-- Populate Category Dropdown -->                                
                                    <option value="all">ALL</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['category_id']; ?>">
                                            <?php echo $category['category_name']; ?>              
                                        </option>
                                    <?php endforeach; ?>
                                </select>                                
                            <input type="submit" value="Go">
                        </form>


                        <!-- <?php                                             
                        // if($statementCatList->rowCount() > 0): 
                        //     while ($row = $statementCatList->fetch()) {
                        //         $category_name = htmlspecialchars($row['category_name']);
                        //         $category_id = htmlspecialchars($row['category_id']);
                        //         // Generate a menu item with the fetched data
                        //         echo '<a href="categorySearchPart.php?p=' . $category_id . '">' . $category_name . '</a>';                            
                        //     }                    
                        // //endif 
                        // ?>
                        // <br><br>
                        // <form class="d-flex" role="search">
                        //     <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        //     <button class="btn btn-outline-success" type="submit">Search</button>
                        // </form>
                    </div>
            </nav>
        </header>

        <!-- Main Content -->    
        <main>      
			<div id="content">
                <div class="content-block">
                    <h1><?php echo $page_title ?></h1></br>
                </div>
                <div class="content-block">
				    <p><?php echo html_entity_decode($page_content) ?></p></br>
                </div>
                <div class="content-block">
                    <?php if ($page_image_filename != ""): ?>
                        <img src="uploads/<?php echo $page_image_filename ?>" alt="image" class="image" />
                    <?php endif; ?>
                </div>    
		    </div>                         
        </main>         
    </div>
    <script>
        // Add an event listener to the category dropdown
        document.getElementById('category_id').addEventListener('change', function() {        
            var form = document.getElementById('categorySearchForm');
    
            form.submit();
        });
    </script>
</body>
</html>

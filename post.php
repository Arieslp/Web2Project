<?php

/*******w******** 
    
    Name: LAI PING SHUM
    Date: 2023-07-31
    Description: Assignment 3 - Blogging Application

****************/

    require('connect.php');    

    if (isset($_GET['id'])){
        $post_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);


        // Check if the post_id is a valid integer
        if ($post_id === false || $post_id <= 0) {
            header('Location: index.php');
            exit;
        }

         // Query SQL
        $query = "SELECT * FROM blog WHERE id = :post_id";
     
        // A PDO::Statement is prepared from the query.
        $statement = $db->prepare($query);

        //  Bind param to the parameters        
        $statement->bindParam(':post_id', $post_id, PDO::PARAM_INT);

        // Execution on the DB server is delayed until we execute().
        $statement->execute();

        if ($statement->rowCount() > 0) {
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            $title = $row['title'];
            $timestamp = $row['timestamp'];
            $content = $row['content'];
        } 
        else {
            echo "Error: Post not found.";
            exit;
        }
    }
    else {
        echo "Error: No post ID provided.";
        exit;
    }    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>My Blog Post - <?php echo $title ?></title>
  
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <div id="container">
        <header class="header">
            <div class="box-left">                
                <a href="index.php"><img src="images/peppy.jpg" alt="My Blog" class="logoleft" /></a>
            </div>
            <div class="box-title">
                <a href="index.php"><h1>My Amazing Blog</h1></a>
            </div>
            <div class="box-right"> 
            </div>            
            <div class="box-right">               
                <a href="index.php">Home</a>
            </div>                    
        </header>

        <main>  
            <?php
                // Format the timestamp.
                $formatted_timestamp = date("F d, Y, h:i A", strtotime($timestamp));
            ?>

            <div class="blogcontent">
                <br>
                <h3><a href="post.php?id=<?php echo $post_id; ?>"><?php echo $title; ?></a></h3>
                <p>                       
                    <?php echo $formatted_timestamp; ?>
                    <a href="edit.php?id=<?php echo $post_id; ?>">edit</a>
                </p><br>

                <div class="blog_content">
                    <p><?php echo $content; ?></p><br>
                </div>
            </div>
        </main>    
    </div>
</body>
</html>


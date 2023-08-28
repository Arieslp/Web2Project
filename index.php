<?php

/*******w******** 
    
    Name: LAI PING SHUM
    Date: 2023-08-31
    Description: Final project

****************/

    require('connect.php');    

    // Query SQL
    $query = "SELECT * FROM pages";

     // A PDO::Statement is prepared from the query.
     $statement = $db->prepare($query);

     // Execution on the DB server is delayed until we execute().
     $statement->execute(); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Welcome to UareSpecial!</title>
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
                <a href="new_post.php">New Post</a>
            </div>            
            <div class="box-right">               
                <a href="index.php">Home</a>
            </div>                     
        </header>
        <main>
            <!-- Display each blog post as a list item.-->
            <?php if($statement->rowCount() > 0):                
                while($row = $statement->fetch()): 
                    $id = $row['id'];
                    $title = htmlspecialchars($row['title']);
                    $timestamp = $row['timestamp'];
                    $content = htmlspecialchars($row['content']);

                    // Truncate the content if it's longer than 200 characters.
                    $strcontent = strlen($content) > 200 ? substr($content, 0, 200) . ' ' : $content;

                    // Format the timestamp.
                    $formatted_timestamp = date("F d, Y, h:i A", strtotime($timestamp));
            ?>
                <div class="blogcontent">
                    <br>
                    <h3><a href="post.php?id=<?php echo $id; ?>"><?php echo $title; ?></a></h3>
                    <p>                       
                        <?php echo $formatted_timestamp; ?>
                        <a href="edit.php?id=<?php echo $id; ?>">edit</a>
                    </p><br>

                    <div class="blog_content">
                        <p><?php echo $strcontent; 
                            if (strlen($strcontent) > 200): ?>
                                <a href="post.php?id=<?php echo $id; ?>"> Read Full Post ...</a>                            
                            <?php endif ?>
                        </p><br>                            
                    </div>
                </div>            
                <?php 
                endwhile;  
            else:
                echo "<p>No Blog Update</p>";
            endif ?>  
        </main>         
    </div>
</body>
</html>
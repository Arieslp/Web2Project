<?php

/*******w******** 
    
    Name: LAI PING SHUM
    Date: 2023-08-31
    Description: Final project

****************/

    require_once('connect.php');   
    require_once('utils.php');

    // Handle Delete Blog Post
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {     
    // if (isset($_POST['delete_action']) && $_POST['delete_action'] == 1) {
        $deletePageId = filter_input(INPUT_POST, 'page_id', FILTER_VALIDATE_INT);
        // Delete SQL
        $query = "DELETE FROM pages WHERE page_id = :page_id";

        // A PDO::Statement is prepared from the query.
        $statement = $db->prepare($query);

        //  Bind param to the parameters        
        $statement->bindParam(':page_id', $deletePageId, PDO::PARAM_INT);

        // Execution on the DB server is delayed until we execute().
        if (!$statement->execute()){                     
            utils::jsonError("Error: Unable to delete the page");
            return;            
        }
        utils::jsonSuccess("Page delete successfully.");
    }

    
?>

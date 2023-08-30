<?php

/*******w******** 
    
    Name: LAI PING SHUM
    Date: 2023-08-31
    Description: Final project

****************/

    require_once('connect.php');
   //require('authenticate.php');
    require_once('utils.php');
    require '\xampp\htdocs\challenges\C7\lib\ImageResize.php';
    require '\xampp\htdocs\challenges\C7\lib\ImageResizeException.php';
    
    use Gumlet\ImageResize;      

// file_upload_path() - Safely build a path String that uses slashes appropriate for our OS.
// Default upload path is an 'uploads' sub-folder in the current folder.
function file_upload_path($original_filename, $upload_subfolder_name = 'uploads') {
    $current_folder = dirname(__FILE__);

    // Build an array of paths segment names to be joined using OS specific slashes.
    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];

    // The DIRECTORY_SEPARATOR constant is OS specific.
    return join(DIRECTORY_SEPARATOR, $path_segments);
}

// file_is_an_image() - Checks the mime-type & extension of the uploaded file for "image-ness".
function file_is_an_image($temporary_path, $new_path) {
    $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

    $actual_file_extension   = strtolower(pathinfo($new_path, PATHINFO_EXTENSION));        
    $actual_mime_type = mime_content_type($temporary_path);

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_is_valid && $mime_type_is_valid;
}

    // Get category list from categories table
    $queryCatList = "SELECT * FROM categories";
    $statementCatList = $db->prepare($queryCatList);
    $statementCatList->execute();
    $category_list = $statementCatList->fetchAll();

    // Rewrite the $_get function to get the record from database by page_id
    // check the status of the request
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (isset($_GET['id'])){
            $page_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            
            // Check if the page_id is a valid integer
            if ($page_id === false || $page_id <= 0) {
                utils::jsonError("Error: Page not found.");
                return;
            }
        } 
        else {
            utils::jsonError("Error: No Page ID provided.");
            return;         
        }

        // Query SQL with page_id
        $query = "SELECT * FROM pages WHERE page_id = :page_id";

        // A PDO::Statement is prepared from the query.
        $statement = $db->prepare($query);

        //  Bind param to the parameters
        $statement->bindParam(':page_id', $page_id, PDO::PARAM_INT);

        // Execution on the DB server is delayed until we execute().
        $statement->execute();

        //check return row count and fetch the data
        if ($statement->rowCount() > 0) {
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            $title = $row['title'];
            $content = $row['content'];
            $permalink = $row['permalink'];
            $image_filename = $row['image_filename'];
            $category_id = $row['category_id'];
        } else {
            utils::jsonError("Error: Page not found.");
            return;
        }

        //Get category name from categories table based on category id which is stored in pages table
        $queryCatName = "SELECT category_name FROM categories WHERE category_id = :category_id";      
        $statementCatName = $db->prepare($queryCatName);
        $statementCatName->bindValue(':category_id', $category_id);
        $statementCatName->execute();
        $category_name = $statementCatName->fetchColumn();
    }

    // Handle Update page
    // Define variable
    $success = false; 
  
    // check if the post request is made
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {   
    // if (isset($_POST['update_action']) && $_POST['update_action'] == 0) {     
        $updatePageId = filter_input(INPUT_POST, 'page_id', FILTER_VALIDATE_INT);
        $updateTitle = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $updateContent = filter_input(INPUT_POST, 'content');
        $updatePermalink = filter_input(INPUT_POST, 'permalink', FILTER_SANITIZE_STRING);
        $updateCategory = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
        $updateImage = filter_input(INPUT_POST, 'file', FILTER_SANITIZE_STRING);
        

        if (empty($updateTitle)){            
            utils::jsonError("* Title is required and must be at least 1 character long.");
            return;
        }

        if (empty($updateContent)){            
            utils::jsonError("* Content is required and must be at least 1 character long.");
            return;
       }
   
       if (empty($updatePermalink)){        
           utils::jsonError("* Permalink is required and must be at least 1 character long.");
           return;
       }
   
       if (empty($updateCategory)){
           utils::jsonError("* Category is required.");
           return;
       }
            
       // Get category name from categories table where category_id = $category_id
        $queryCatName = "SELECT category_id FROM categories WHERE category_name = :category_name";
        $statementCatName = $db->prepare($queryCatName);
        $statementCatName->bindValue(':category_name', $updateCategory);
        $statementCatName->execute();
        $category_id = $statementCatName->fetchColumn();

        // Compare new page data and old page data from database, then update the page into database once pass the data validation.
        // Check Title is changed or not
        $query = "SELECT title FROM pages WHERE page_id = :page_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':page_id', $updatePageId);
        $statement->execute();
        $oldTitle = $statement->fetchColumn();

        if ($oldTitle !== $updateTitle){
            $query = "UPDATE pages SET title = :title WHERE page_id = :page_id";
            $statement = $db->prepare($query);
            $statement->bindValue(':title', $updateTitle);
            $statement->bindValue(':page_id', $updatePageId);
            $statement->execute();            
        }

        // Check Content is changed or not
        $query = "SELECT content FROM pages WHERE page_id = :page_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':page_id', $updatePageId);
        $statement->execute();
        $oldContent = $statement->fetchColumn();

        if ($oldContent !== $updateContent){
            $query = "UPDATE pages SET content = :content WHERE page_id = :page_id";
            $statement = $db->prepare($query);
            $statement->bindValue(':content', $updateContent);
            $statement->bindValue(':page_id', $updatePageId);
            $statement->execute();            
        }

        // Check Permalink is changed or not
        $query = "SELECT permalink FROM pages WHERE page_id = :page_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':page_id', $updatePageId);
        $statement->execute();
        $oldPermalink = $statement->fetchColumn();

        if ($oldPermalink !== $updatePermalink){
            $query = "UPDATE pages SET permalink = :permalink WHERE page_id = :page_id";
            $statement = $db->prepare($query);
            $statement->bindValue(':permalink', $updatePermalink);
            $statement->bindValue(':page_id', $updatePageId);
            $statement->execute();            
        }

        // Check Category is changed or not
        $query = "SELECT category_id FROM pages WHERE page_id = :page_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':page_id', $updatePageId);
        $statement->execute();
        $oldCategory = $statement->fetchColumn();

        if ($oldCategory !== $category_id){
            $query = "UPDATE pages SET category_id = :category_id WHERE page_id = :page_id";
            $statement = $db->prepare($query);
            $statement->bindValue(':category_id', $category_id);
            $statement->bindValue(':page_id', $updatePageId);
            $statement->execute();        
        }

        // Check Image is changed or not
        // Check if an image file was uploaded
                
        if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
            $image_filename = $_FILES['file']['name'];
            $temporary_image_path = $_FILES['file']['tmp_name'];
            $new_image_path = file_upload_path($image_filename);

            if (file_is_an_image($temporary_image_path, $new_image_path)) {
                // Resize and save image as thumbnail size
                $image_filename = "thumbnail_" . $image_filename;
                $thumbnail_image_path = file_upload_path($image_filename);
                $image = new ImageResize($temporary_image_path);
                $image->resizeToWidth(300);
                $image->save($thumbnail_image_path);
            } else {            
                utils::jsonError("Invalid image format. Please upload a valid image.");
                return;
            }
            $query = "UPDATE pages SET image_filename = :image_filename WHERE page_id = :page_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':image_filename', $image_filename);
        $statement->bindValue(':page_id', $updatePageId);
        $statement->execute();            
        }
        
        
    
        // Execution on the DB server is delayed until we execute().
        
            
            if (!$statement->execute()){
                //$statement->debugDumpParams();
                utils::jsonError("Error: Unable to update the page");
                return;
            }  
            
            utils::jsonSuccess("Page updated successfully.");   
    }

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
        if ($statement->execute()){
            utils::jsonSuccess("Page delete successfully.");
            exit;
        } else {            
            utils::jsonError("Error: Unable to delete the page");
            return;            
        }
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
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>      
    <title>Edit Page<?php echo $title ?></title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <div id="container">
        
        <main>
            <form method="post" id="edit-form" enctype="multipart/form-data">                
                    <h2>Edit Page</h2><br>

                    <?php if ($success): ?>
                        <p class="success">Post updated successful</p>
                    <?php endif; ?>

                    <div class="formfield">
                        <input type="hidden" name="page_id" id="page-id" value="<?php echo $page_id ?>">

                        <label for="title">Title</label><br>
                        <input type="text" name="title" id="title" value="<?php echo $title ?>" autofocus="autofocus"><br></br>                
            
                        <label for="content">Content:</label><br>                        
                        <div name="content" id="content" rows="20" cols="50"><?php echo $content ?></div><br>
                        <br>

                        <label for="permalink">Permalink</label><br>
                        <input type="text" name="permalink" id="permalink" value="<?php echo $permalink ?>"><br></br>
                         
                        <!-- Image upload form -->
                        <label for="file">Image (optional)</label><br>                        
                        <!-- check if image_filname is not empty -->
                        <?php if (isset($image_filename) && !empty($image_filename)): ?>                            
                            <h6>Here is your existing file "<?php echo $image_filename ?>"</h6>
                            <h6>Upload a new file if you want to replace the existing file.</h6>
                        <?php endif; ?>
                        <input type="file" name="file" id="file"><br>                       
                        </br>

                        <label for="category">Category</label><br>
                        <h6>Here is your existing category "<?php echo $category_name ?>"</h6>
                        <h6>Upload a new file if you want to replace the existing file.</h6>

                        <select name= "category" class="form-select" aria-label="category">  
                            <?php foreach ($category_list as $category) : ?>
                            <option value="<?php echo $category['category_name']; ?>"><?php echo $category['category_name']; ?></option>
                            <?php endforeach; ?>                        
                        </select>
                        </br>
                        <br>

                        <input type="hidden" name="update_action" value="0">
                        <button   id="update-btn">Update Page</button>

                        <input type="hidden" name="delete_action" value="1">
                        <!-- <button id="delete-btn" onclick="return confirm('Are you sure you wish to delete this post?')">Delete Page</button> -->
                        <button id="delete-btn">Delete Page</button>

                        <div class="ReturnAdmin">
                            </br><a href="admin.php" class="button">Return to Admin</a>
                        </div>

            </form>
        </main>
    </div>
    <script>
        content
        var quill = new Quill('#content', {
        theme: 'snow', // Snow theme is the default theme
        modules: {
        toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{'header': 1}, {'header': 2}],
            [{'list': 'ordered'}, {'list': 'bullet'}],
            [{'script': 'sub'}, {'script': 'super'}],
            [{'indent': '-1'}, {'indent': '+1'}],
            [{'direction': 'rtl'}],
            [{'size': ['small', false, 'large', 'huge']}],
            [{'header': [1, 2, 3, 4, 5, 6, false]}],
            [{'color': []}, {'background': []}],
            [{'font': []}],
            [{'align': []}],
            ['clean'],
            ['link', 'image', 'video']
            ]
            }
        });

        // update button click event
        

        $('#update-btn').click(function(e) {
            // prevent default form submit action
            e.preventDefault();

            // get form data
            var formData = new FormData($('#edit-form')[0]);
            formData.append('content', quill.root.innerHTML);

            // add form data to ajax request
            $.ajax({
                url: 'edit.php',
                type: 'POST',
                data: formData,
                success: function(data) {
                    // do something on success
                    console.log(data);
                    if (data.status !== 1) {
                        alert(data.message);
                    } else {
                        alert(data.message);
                        window.location.href = "admin.php";
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

        // delete button click event
        $('#delete-btn').click(function(e) {
            // prevent default form submit action
            e.preventDefault();
            // show confirm dialog before deleting a record
            if (confirm("Are you sure you want to delete this page?")) {
                // get page id from hidden input field
                var pageId = $('#page-id').val();
                // DELETE request to delete a record
                $.ajax({
                    url: 'delete.php',
                    type: 'POST',
                    data: {
                        page_id: pageId
                    },
                    success: function(data) {
                        // do something on success
                        console.log(data);
                        if (data.status !== 1) {
                            alert(data.message);
                        } else {
                            alert(data.message);
                            window.location.href = "admin.php";
                        }
                    }
                });

                
            }
           
        });
    </script>
</body>
</html>


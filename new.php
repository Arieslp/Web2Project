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
   
require('utils.php');

require '\xampp\htdocs\challenges\C7\lib\ImageResize.php';
require '\xampp\htdocs\challenges\C7\lib\ImageResizeException.php';

use Gumlet\ImageResize;  

// Define variable
$title = '';
$content = '';
$permalink = '';
$image_filename = '';
$category = '';
$errors = [];


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

// Get category name from categories table
$queryCatName = "SELECT category_name FROM categories";
$statementCatName = $db->prepare($queryCatName);
$statementCatName->execute();
$category_list = $statementCatName->fetchAll();
//var_dump($category_list);
//exit();
 

// check if the post request is made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {     
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $content = filter_input(INPUT_POST, 'content');
    $permalink = filter_input(INPUT_POST, 'permalink', FILTER_SANITIZE_STRING);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);

    //var_dump($category);
    //exit();

    // Check if an image file was uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $image_filename = $_FILES['file']['name'];
        $temporary_image_path = $_FILES['file']['tmp_name'];
        $new_image_path = file_upload_path($image_filename);

        if (file_is_an_image($temporary_image_path, $new_image_path)) {
            // Move the uploaded image to the appropriate folder
            //move_uploaded_file($temporary_image_path, $new_image_path);
            
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
    }        

    if (empty($title)){        
        utils::jsonError("* Title is required and must be at least 1 character long.");
        return;
    }

    if (empty($content)){   
         utils::jsonError("* Content is required and must be at least 1 character long.");
         return;
    }

    if (empty($permalink)){        
        utils::jsonError("* Permalink is required and must be at least 1 character long.");
        return;
    }

    if (empty($category)){        
        utils::jsonError("* Category is required.");
        return;
    }

    if (empty($errors)){
        // Retrieve the category id from categories table
        $queryCat = "SELECT category_id FROM categories WHERE category_name = :category";
        $statementCat = $db->prepare($queryCat);
        $statementCat->bindParam(':category', $category, PDO::PARAM_STR);        
        $statementCat->execute();
        $rowCat = $statementCat->fetch(PDO::FETCH_ASSOC);

        if ($rowCat == false) {
            utils::jsonError("Error: Category not found.");
            return;
        }
        $category_id = $rowCat['category_id'];
            
        // Retrieve the author id from user table
        $queryUser = "SELECT user_id FROM users WHERE username = :username";
        $statementUser = $db->prepare($queryUser);
        $statementUser->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
        $statementUser->execute();
        $rowUser = $statementUser->fetch(PDO::FETCH_ASSOC);

        if ($rowUser == false) {
            utils::jsonError("Error: User not found.");
            return;
        }
        $author_id = $rowUser['user_id'];


        // Insert SQL            
            $query = "INSERT INTO pages (title, content, permalink, image_filename, category_id, author_id, created_at) 
                        VALUES (:title, :content, :permalink, :image_filename, :category_id, :user_id, NOW())";
                        
            // A PDO::Statement is prepared from the query.
            $statement = $db->prepare($query);

            // Bind params to the parameters        
            $statement->bindParam(':title', $title, PDO::PARAM_STR);
            $statement->bindParam(':content', $content, PDO::PARAM_STR);
            $statement->bindParam(':permalink', $permalink, PDO::PARAM_STR); 
            $statement->bindParam(':image_filename', $image_filename, PDO::PARAM_STR);      
            $statement->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $statement->bindParam(':user_id', $author_id, PDO::PARAM_INT);
            
            // Execution on the DB server is delayed until we execute().
            if ($statement->execute()){
                // Redirect to the home page after insert successful
                utils::jsonSuccess("New page added successfully.");
                exit;
            } else {                    
                utils::jsonError("Error: Unable to add the new page");
                 return;
            }
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
    <link rel="stylesheet" href="./main.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>        
    <title>New Page</title>  
</head>
<body>
    <!-- Remember that alternative syntax is good and HTML inside PHP is bad -->
    <div id="container">
         <main>
            <form method="post">                
                    <h2>New Page</h2><br>
                    
                    <div class="formfield">

                        <label for="title">Page Title</label><br>
                        <input type="text" name="title" id="title" autofocus="autofocus"><br>
                        </br>

                        <label for="content">Content</label><br>
                        <div name="content" id="content" rows="8" cols="50"></div><br>
                        </br>

                        <label for="permalink">Permalink</label><br>
                        <input type="text" name="permalink" id="permalink" autofocus="autofocus"><br>                     
                        </br>

                        <!-- Image upload form -->
                        <label for="file">Image (optional)</label><br>
                        <input type="file" name="file" id="file"><br>                       
                        </br>
                        
                        <label for="category">Category</label><br>
                        <select name= "category" class="form-select" aria-label="category">  
                            <?php foreach ($category_list as $category) : ?>
                            <option value="<?php echo $category['category_name']; ?>"><?php echo $category['category_name']; ?></option>
                            <?php endforeach; ?>                        
                        </select>
                        </br>
                        </br>

                        <button type="submit">create</button>
                        
                        <div class="ReturnAdmin">
                        </br><a href="admin.php" class="button">Return to Admin</a>
                        </div>

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


        // create from submit
        $('form').submit(function(e) {
            // prevent default form submit action
            e.preventDefault();

            // get form data
            var formData = new FormData(this);
            formData.append('content', quill.root.innerHTML);

            // add form data to ajax request
            $.ajax({
                url: 'new.php',
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
    </script>
</body>
</html>


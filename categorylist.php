<?php
/*******w******** 
    Name: LAI PING SHUM
    Date: 2023-08-31
    Description: Final project
****************/

require_once('connect.php');
require_once('utils.php');

// Create or Update Category
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['category_id'])) {
        // Update Category
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        $category_name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_STRING);

        if (empty($category_name)) {
            utils::jsonError("Category name is required.");
            return;
        }

        $query = "UPDATE categories SET category_name = :category_name WHERE category_id = :category_id";
    } else {
        // Create Category
        $category_name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_STRING);

        if (empty($category_name)) {
            utils::jsonError("Category name is required.");
            return;
        }

        $query = "INSERT INTO categories (category_name) VALUES (:category_name)";
    }

    $statement = $db->prepare($query);
    $statement->bindParam(':category_name', $category_name, PDO::PARAM_STR);

    if (isset($category_id)) {
        $statement->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    }

    if ($statement->execute()) {
        utils::jsonSuccess("Category saved successfully.");
    } else {
        utils::jsonError("Error saving category.");
    }

    exit;
}

// Fetch Categories
$query = "SELECT * FROM categories";
$statement = $db->prepare($query);
$statement->execute();
$categories = $statement->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Category Management</title>
</head>
<body>
    <div id="container">
        <main>
            <h2>Category Management</h2>
            
            <!-- Create or Update Category Form -->
            <form id="categoryForm" method="post">
                <?php if (isset($_GET['edit'])) : ?>
                    <input type="hidden" name="category_id" value="<?php echo $_GET['edit']; ?>">
                <?php endif; ?>

                <label for="category_name">Category Name:</label>
                <input type="text" name="category_name" id="category_name" <?php if (isset($_GET['edit'])) echo 'value="' . $categories[$_GET['edit'] - 1]['category_name'] . '"'; ?>>
                <button type="submit"><?php echo isset($_GET['edit']) ? 'Update' : 'Create'; ?> Category</button>
            </form><br>

            <!-- List of Categories -->
            <h3>Categories List📃</h3>
            <ul>
                <?php foreach ($categories as $key => $category) : ?>
                    <li>
                        <?php echo $category['category_name']; ?>
                        <a href="?edit=<?php echo $key + 1; ?>">Edit</a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="ReturnAdmin">
                </br><a href="admin.php" class="button">Return to Admin</a>
            </div>
        </main>
    </div>


    <script>
        // create form submit
        $('form').submit(function(e) {
            // prevent default form submit action
            e.preventDefault();

            // get form data
            var formData = new FormData(this);
            
            // add form data to ajax request
            $.ajax({
                url: 'categorylist.php',
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


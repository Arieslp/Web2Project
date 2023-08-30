<?php

/*******w******** 
    
    Name: LAI PING SHUM
    Date: 2023-08-31
    Description: Final project

****************/

session_start(); // Start the session

require_once('connect.php'); // Include database connection
require('utils.php'); // Include utility functions

// Initialize variables to store user input and error messages
$username = $password = $confirmPassword = $admin = "";
$usernameError = $passwordError = $confirmPasswordError = $registrationSuccess = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Function to sanitize and validate user input
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }


    // Retrieve and validate username and email format
    if (empty($_POST['username'])) {        
        utils::jsonError("Username is required");
        return;          
    } else {

        $username = test_input($_POST['username']);
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {            
            utils::jsonError("Invalid email format");
            return;   
        }

        // Check if email address exist in user table username column
        $query = "SELECT * FROM users WHERE username = :username";
        $statement = $db->prepare($query);
        $statement->bindParam(':username', $username); // Bind the value of $username
        $statement->execute();        

        // $statement->debugDumpParams();
        if($statement->rowCount() > 0): 
            utils::jsonError("Username already exists");
            return;
        endif;        
    }

    // Retrieve and validate password
    if (empty($_POST['password'])) {
        //$passwordError = "Password is required";
        utils::jsonError("Password is required");
        return;
    } else {
        $password = $_POST['password'];
    }

    // Retrieve and validate confirm password
    if (empty($_POST['confirm_password'])) {
        //$confirmPasswordError = "Please confirm your password";
        utils::jsonError("Please confirm your password");
        return;
    } else {
        $confirmPassword = $_POST['confirm_password'];
        if ($password !== $confirmPassword) {
            //$confirmPasswordError = "Passwords do not match";
            utils::jsonError("Passwords do not match");
            return;
        }
    }

    // Retrieve and validate admin checkbox
    // $admin = $_POST['admin'];

    // if (empty($_POST['admin'])) {
    //     $admin = "NONADMIN";
    // } else {
    //     $admin = "ADMIN";
    // }

    // define a variable to store the value of the checkbox
    if (isset($_POST['admin']) && $_POST['admin'] === 'admin') {     
         $roles = 'ADMIN';
     } else {
         $roles = 'NONADMIN';
    }
   
     //var_dump($roles);
     //exit();

    // If there are no errors, you can proceed with registration
    if (empty($usernameError) && empty($passwordError) && empty($confirmPasswordError)) {
        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, user_password, roles) VALUES (:username, :user_password, :roles)";
        $statement = $db->prepare($query);
        
        $statement->bindParam(':username', $username);
        $statement->bindParam(':user_password', $hashedPassword);
        $statement->bindParam(':roles', $roles); 
        
        // After successful registration, set a success message
        //$registrationSuccess = "Registration successful! You can now log in.";                     
        if (!$statement->execute()){
            //$statement->debugDumpParams();
            utils::jsonError("Error: Unable to add user");
            return;
        }  else {
            utils::jsonSuccess("Registration successful! You can now log in.");
        }
    }
}
require("header.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css"> -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>       
    <link rel="stylesheet" href="main.css">
    <!-- <script src="js/utilityFunctions.js"></script>
	<script src="js/formValidate.js"></script>     -->
    <title>Registration</title>
</head>

<body id="register-body">
    <div id="container">
        <div id="register-container">
        <main>
            <h2>SIGN IN</h2><br>

            <?php if (!empty($registrationSuccess)): ?>
                <p style="color: green;"><?php echo $registrationSuccess; ?></p>
            <?php endif; ?>

            <form method="POST" id="register-form">
                
                <div class="form-group">      
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" placeholder="Enter email address" value="<?php echo $username; ?>" required>                    
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Enter password" required>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" required>                    
                </div>

                <div class="form-group">
                    <div class="form-check">                        
                        <!-- <label for="form-check-input">Admin</label>
                        <input type="checkbox" name ="admin" id="form-check-input" value="admin">
                        
                        <label class="form-check-label" for="flexCheckDefault">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"> -->

                        <label class="form-check-label" for="flexCheckDefault">Admin</label>
                        <input class="form-check-input" type="checkbox" value="admin" id="flexCheckDefault" name="admin">



                    </div>
                </div>
                
                <button id="register-button" type="submit">Register</button>

            </form>
        </main>
        </div>
    </div>

    <script>        
    // create from submit
    $('form').submit(function(e) {
        // prevent default form submit action
        e.preventDefault();

        // get form data
        var formData = new FormData(this);
        
        // add form data to ajax request
        $.ajax({
            url: 'register.php',
            type: 'POST',
            data: formData,
            success: function(data) {
                // do something on success
                console.log(data);
                if (data.status !== 1) {
                    alert(data.message);
                } else {
                    alert(data.message);
                    window.location.href = "index.php";
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


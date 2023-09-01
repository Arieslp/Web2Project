<?php

/*******w******** 
    
    Name: LAI PING SHUM
    Date: 2023-08-31
    Description: Final project

****************/

if (!isset($_SESSION)){
    session_start();
}

require_once('connect.php'); // Include database connection
require('utils.php'); // Include utility functions

// Initialize variables to store user input and error messages
$username = $password = "";
$usernameError = $passwordError = "";

// Assuming successful login
// $response = array("status" => 1, "message" => "Login successful");
// echo json_encode($response);

// header('Content-Type: application/json');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    
    // Retrieve and validate username and email format
    if (empty($username)) {        
        utils::jsonError("Username is required");
        return;          
    }
    
    // Retrieve and validate password
    if (empty($password)) {        
        utils::jsonError("Password is required");
        return;
    }

    // Check if email address and password exist in user table username column, and user_password column
    $query = "SELECT * FROM users WHERE username = :username";
    $statement = $db->prepare($query);
    $statement->bindParam(':username', $username); // Bind the value of $username
    $statement->execute();

    // $statement->debugDumpParams();
    // check if username exist
    if($statement->rowCount() == 0): 
        utils::jsonError("Username does not exist");
        return;
    endif;

    // check if password is correct
    $row = $statement->fetch();
    $user_password = $row['user_password'];
    $user_right = $row['roles'];
    $retrievedSalt = $row['salt'];

    // Retrieve the stored salt for the user from the database
    // Combine the entered password with the retrieved salt
    //$passwordWithSalt = $password . $retrievedSalt; // the salt stored in the database
    $passwordWithSalt = $password . $retrievedSalt; // the salt stored in the database

    // Retrieve the stored hashed password for the user from the database
    $storedHashedPassword = $user_password; // the hashed password stored in the database

    // Verify the password using password_verify
    if (!password_verify($passwordWithSalt, $storedHashedPassword)) {
        utils::jsonError("Password is incorrect");
        return;
    };
        
    // set session variables
    $_SESSION['username'] = $username;
    $_SESSION['user_right'] = $user_right;

    // Display value of session variables
    // echo "Username: " . $_SESSION['username'] . "<br />";
    // echo "User Right: " . $_SESSION['user_right'] . "<br />";

    // Assuming successful login
    utils::jsonSuccess("Login successful!");
    exit();
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
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>       
    <link rel="stylesheet" href="main.css">
    
    <title>SIGN IN</title>
</head>

<body id="login-body">
    <div id="container">
        <div id="register-container">
        <main>
            <h2>SIGN IN</h2><br>

            <form method="POST" id="login-form">
                
                <div class="form-group">      
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" placeholder="Enter email address ✉️" required>                    
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Enter password 🔑" required>
                              
                <button id="login-button" type="submit">Submit</button>

            </form>
        </main>
        </div>
    </div>

    <script>      

    // create from submit
    $('#login-form').submit(function(e) {
        //$('#login-button').click(function(e) {
        // prevent default form submit action
        e.preventDefault();

       
        //var formData = new FormData($('login-form')[0]);
        var formData = new FormData(this);
        
        // add form data to ajax request
        $.ajax({
            url: 'login.php',
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


<?php

/*******w******** 
    
    Name: LAI PING SHUM
    Date: 2023-08-31
    Description: Final project

****************/

    require_once('connect.php');


    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']))
    {
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Basic realm="Our Blog"');
        exit("Access Denied: Username and password required.");
    }

    // Check username and password against the database
    $username = $_SERVER['PHP_AUTH_USER'];

    $query = "SELECT username, user_password, roles FROM users WHERE username = ?";
    $statement = $db->prepare($query);
    $statement->execute([$username]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    

    // Check if the user exists
    if (!$user) {        
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Basic realm="Our Blog"');
        exit("Access Denied: Invalid username.");
    }
    
    // Verify the password
    if ($_SERVER['PHP_AUTH_PW'] != $user['user_password']) {
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Basic realm="Our Blog"');
        exit("Access Denied: Invalid password.");
    }
    
    // Check user's permissions
    $user_right = $user['roles'];
    
    if (($user_right) !== 'ADMIN' && ($user_right) !== 'NONADMIN') {
        header('HTTP/1.1 403 Forbidden');
        exit("Access Forbidden: You don't have permission to access this page.");
    }
 
?>
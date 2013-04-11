<?php session_start(); ?>
<html>
    <head>
        <link rel="stylesheet" href="css/main.css" type="text/css" />
        <title>Login</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body> 
    <?php 
        if(!isset($_POST['flag']))
        {
            include("login.php");
        }else
        {
            $password = $_POST['password'];
            $username = $_POST['username'];
            include("includes/db.php");
            global $host, $user, $pass, $db, $port;
            $link = mysqli_connect($host, $user, $pass, $db, $port);
            $query = "SELECT permission FROM users WHERE username='$username' AND password='$password'";
            $result = mysqli_query($link, $query);
            $temp = array();
            $temp = mysqli_fetch_row($result);
            $auth = $temp[0];
            if($auth==1)
            {
                $_SESSION['permission'] = "admin";
                $_SESSION['username'] = $username;
                include("adminHome.php");
            }elseif($auth==2)
            {
                $_SESSION['username'] = $username;
                $_SESSION['permission'] = "faculty";
                include("facultyHome.php");
            }else
            {
                // User fails authentication. Output login page.
                $_SESSION['username'] = NULL;
                $_SESSION['permission'] = NULL;
                include("login.php");
                echo("<p> There was a problem logging in. Please try again.</p>");
            }
        }
    ?>
    </body>
</html>

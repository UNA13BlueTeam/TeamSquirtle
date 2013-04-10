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
                include("adminHome.php");
            }elseif($auth==2)
            {
                include("facultyHome.php");
            }else
            {
                // User fails authentication. Output login page.
                include("login.php");
                echo("Auth = $auth");
                echo("$query");
            }
        }
    ?>
    </body>
</html>

<?php 
    session_start();
    require("includes/global.php");
?>
<html>
    <head>
        <link rel="stylesheet" href="css/main.css" type="text/css" />
        <link rel="icon" type="image/png" href="img/squirtle.png">
        <title>Login</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body> 
    <?php 
        if(!isset($_POST['flag']))
        {
            // $_SESSION['loggedIn'] = false;
            include("login.php");
        }else
        {
            $password = $_POST['password'];
            $username = $_POST['username'];
            global $host, $user, $pass, $db, $port;
            $link = mysqli_connect($host, $user, $pass, $db, $port);
            $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
            $result = mysqli_query($link, $query);
            $temp = array();
            $temp = mysqli_fetch_assoc($result);
            $auth = $temp['permission'];
            $setup = $temp['firstLogOn'];
            $firstname = $temp['firstName'];
            $lastname = $temp['lastName'];
            echo($setup);
            // while($row = mysqli_fetch_row($result))
            // {
            //     foreach($row as &$value)
            //     {
            //         array_push($_SESSION, $value);
            //     }
            // }
            $_SESSION['permission'] = $auth;
            if($auth==1)
            {
                $_SESSION['loggedIn'] = true;
                $_SESSION['username'] = $username;
    	        $_SESSION['firstname'] = $firstname;
                $_SESSION['lastname'] = $lastname;
                if($setup==1)
                {
                    header("Location: adminSetUp.php");
                }else
                {
                    header("Location: adminHome.php");
                }
            }elseif($auth==2)
            {
                $_SESSION['loggedIn'] = true;
                $_SESSION['username'] = $username;
    	        $_SESSION['firstname'] = $firstname;
                $_SESSION['lastname'] = $lastname;
                if($setup==1)
                {
                    header("Location: facultySetUp.php");
                }else
                {
                    header("Location: facultyHome.php");
                }
            }else
            {
                // User fails authentication. Output login page.
                // $_SESSION['loggedIn'] = false;
                $_SESSION['username'] = NULL;
                $_SESSION['permission'] = NULL;
                include("login.php");
                echo("<p> There was a problem logging in. Please try again.</p>");
            }
        }
    ?>
    </body>
</html>

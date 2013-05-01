<?php 
    session_start();
    require("includes/global.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="stylesheet" href="css/main.css" type="text/css" />
        <link rel="icon" type="image/png" href="img/squirtle.png">
        <title>Login</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
    <div class="nav">
    <a href="index.php">University of North Alabama</a>
    <form action="viewSchedulePDF.php" method="POST">
        <input type="hidden" name="loggingIn" value="true">
        <input type="submit" value="View Schedule" id="login">
    </form>
    <?php
        global $host, $user, $pass, $db, $port;
        $test = mysqli_connect($host, $user, $pass, $db, $port);
        if($test){
            echo ('<div style="font-size:8pt; color:chartreuse;">DB Connected</div>');
            mysqli_close($test);
        }else{
            echo ('<div style="font-size:8pt; color:red;">DB Failed</div>');
        }
        if (mysqli_connect_errno())
        {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
    ?>
    </div> 
    <?php 
        if(!isset($_POST['flag']) and !isset($_POST['loggingIn']))
        {
            // $_SESSION['loggedIn'] = false;
			include("login.php");
        }elseif(!isset($_POST['flag'])){
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
            $deptName = $temp['deptName'];
            $semesterName = $temp['semesterName'];
			
			$_SESSION['permission'] = $auth;
			
            if($auth==1)
            {
                $_SESSION['loggedIn'] = true;
                $_SESSION['username'] = $username;
    	        $_SESSION['firstname'] = $firstname;
                $_SESSION['lastname'] = $lastname;
                $_SESSION['deptName'] = $deptName;
                $_SESSION['semesterName'] = $semesterName;
                if($setup==1)
                {
                    header("Location: adminSetup.php");
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
                $_SESSION['deptName'] = $deptName;
                $_SESSION['semesterName'] = $semesterName;
                if($setup==1)
                {
                    header("Location: facultySetup.php");
                }else
                {
                    header("Location: facultyHome.php");
                }
            }else
            {
                // User fails authentication. Output login page.
                $_SESSION['loggedIn'] = false;
                $_SESSION['username'] = NULL;
                $_SESSION['permission'] = NULL;
                include("login.php");
                echo("<p style='color:red; float:left;'> There was a problem logging in. Please try again.</p>");
            }
        }
    ?>
    </body>
</html>

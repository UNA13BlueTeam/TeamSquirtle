<html>
<head>
    <!-- Stuff goes here. -->
    <link rel="stylesheet" href="css/main.css" type="text/css" />
    <link rel="icon" type="image/png" href="img/squirtle.png">
</head>
<div class="nav">
    <a href="index.php">University of North Alabama</a>
    <?php
        include("includes/db.php");
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
<body>
    <div class="content">
        <h1>Set Up</h1>
        <div class="purpleBox" id="setup">
            <h2>Change Your Password</h2>
            <hr>
            <form action="doFacultySetup.php" method="post">
                <label for="pass1">New Password: <input type="password" name="pass1" autofocus></label> <br>
                <label for="pass2">Retype Password: <input type="password" name="pass2"></label> <br>
                <input type="submit" value="Submit" />
                <input type="reset" value="Reset" />
            </form>
        </div>
        <?php 
            if(isset($_POST['invalid']))
            {
                if($_POST['invalid']==true)
                {
                    echo('<h5 style="color:red;">Your passwords did not match. Please try again.</h5>');
                }
            }
        ?>
    </div>
</body>
<?php include("includes/footer.php"); ?>
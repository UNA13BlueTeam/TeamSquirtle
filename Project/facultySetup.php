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
                <div class="row">
                    <label for="pass1">New Password:</label> 
                    <input type="password" name="pass1" autofocus>
                </div>
                <br>
                <div class="row">
                    <label for="pass2">Retype Password:</label>
                    <input type="password" name="pass2">
                </div>
                <br>
                <div class="row">
                    <input type="hidden" name="actions" value="true">
                    <input type="submit" value="Submit" />
                    <input type="reset" value="Reset" />
                </div>
            </form>
        </div>
        <?php printError(); ?>
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

<?php
    function printError()
    {
        echo('<p style="float:left;">');
        echo("<hr>");
        echo("Password requirements include the following: <br>");
        echo("1. Must begin with an upper-case or lower-case alphabetic character.<br>");
        echo("2. Must contain at least one of the following: , . ! ? <br>");
        echo("3. Must contain at least one digit 0-9. <br>");
        echo("4. Must have a total length of 6 to 10 chacraters. <br>");
        echo("5. Must NOT contain spaces or tabs. <br>");
        echo("<hr>");
        echo('</p>');
    }
    include("includes/footer.php");
?>
<html>
<head>
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
        <h1>Semester Set Up</h1>
        <div class="purpleBox" id="adminsetup">
            <h2>Start a New Semester</h2>
            <hr>
            <form action="doAdminSetup.php" method="post">
                <label for="departmentName">Department Name: <input type="text" name="departmentName" autofocus></label> <br>
                <label for="semesterName">Semester Name: <input type="text" name="semesterName"></label> <br>
                <input type="submit" value="Submit" />
                <input type="reset" value="Reset" />
            </form>
        </div>
    </div>
</body>
<?php include("includes/footer.php"); ?>


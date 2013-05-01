<?php include("includes/header.php");?>

<h1>Change Password</h1>

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
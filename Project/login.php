<div class="nav">
    <a href="index.php">University of North Alabama</a>
    <?php 
        global $host, $user, $pass, $db, $port;
        // $test = mysqli_connect($host, $user, $pass, $db, $port);
        // if($test){
        //     echo ('<div style="font-size:8pt; color:chartreuse;">DB Connected</div>');
        //     mysqli_close($test);
        // }else{
        //     echo ('<div style="font-size:8pt; color:red;">DB Failed</div>');
        // }
        // if (mysqli_connect_errno())
        // {
        //     printf("Connect failed: %s\n", mysqli_connect_error());
        //     exit();
        // }
    ?>
</div>
<div class="content">
    <div class="purpleBox" id="login">
        <form id="login" name="login" method="post" action=".">
            <div class="row">
                <label for="username">Username:&nbsp;</label>
                <input id="username" name="username" type="text" size="15" autofocus/>
            </div>
            <div class="row">
                <label for="password">Password:&nbsp;&nbsp;</label>
                <input id="password" name="password" type="password" size="15" />
            </div>
            <div class="row">
                <input type="hidden" name="flag" value="true">
                <input type="submit" name="submit" value="Login" />
                <input type="reset" name="submit" value="Reset"  />
            </div>
            <div class="row"><img src="img/unaLogo.png" alt="UNA Logo" id="logo" /></div>
        </form>
    </div>
</div>
<html>
<head>
    <title>Check username and password</title>
</head>
<body>
    <?php include_once("includes/global.php"); ?>
    <?php include("includes/db.php"); ?>
    <?php 
    		global $host, $user, $pass, $db, $port;
			$test = mysqli_connect($host, $user, $pass, $db, $port);
    
    if(!$test){
      echo 'fail to connect database';
    }
    $password = trim($_POST['Password']);
    $username = $_POST['Username'];
    $admin ="Select * from users where username='$username' and password='$password'";
    $faculty = "Select * from userfaculty where username='$username' and password='$password'";
    $facultyquery = mysqli_query( $test, $faculty );
    $queryResult = mysqli_query( $test, $admin );
    if($queryResult===false){
        printf("error: %s\n", mysqli_error($test));
    }
    $result = mysqli_num_rows( $queryResult );
    if($result){
        echo 'admin login';
        header("Location: index1.php");
    }
    elseif(mysqli_num_rows($facultyquery )){
        echo 'faculty login';
        header("Location: facultyHome.php");
    }
    else{
        echo 'login failure';
    }
    ?>
</body>
</html>

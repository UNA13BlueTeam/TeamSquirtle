<html>
<head>
    <title>Check username and password</title>
</head>
<body>
    <?php include("includes/db.php"); 
    $link = mysqli_connect ($host, $user, $pass, $db);
    if(!$link){
      echo 'fail to connect database';
    }
    $password = trim($_POST['Password']);
    $username = $_POST['Username'];
    $admin ="Select * from useradmin where username='$username' and userpass='$password'";
    $faculty = "Select * from userfaculty where username='$username' and userpass='$password'";
    $adminquery = mysqli_query( $link, $admin );
    $facultyquery = mysqli_query( $link, $faculty );
    if(mysqli_num_rows( $adminquery )){
        echo 'admin login';
        header('adminhome.php');
    }
    elseif(mysqli_num_rows( $facultyquery )){
        echo 'faculty login';
        header('facultyhome.php');
    }
    else{
        echo 'login failure';
    }
    ?>
</body>
</html>

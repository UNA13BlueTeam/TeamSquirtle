<?php include("includes/header.php");

	// Get variables from input form
	$type = $_POST['roomType'];
	$size = $_POST['size'];
	$name = $_POST['roomName'];
        
        $link = mysqli_connect ($host, $user, $pass, $db);
        if(!link){
            die('cannot connect database'. mysqli_error($link));
        }
        mysqli_query ("INSERT INTO tablename (roomType, size, roomName )
        VALUES (\'$type\',\'$size\', \'$name\')
        ");

	// Print out contents accepted
	echo "You have successfully added this course information to the database! <br>";
	echo "Room Type: $type <br>";
	echo "Room Size: $size <br>";
	echo "Room Name: $name <br>";

include("includes/footer.php");

?>

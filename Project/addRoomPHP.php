<?php include("header.php");

	// Get variables from input form
	$type = $_POST['roomType'];
	$size = $_POST['size'];
	$name = $_POST['roomName'];
        
        $link = mysql_connect (localhost,username, password);
        if(!link){
            die('cannot connect database'. mysql_error());
        }
        mysql_select_db (dbname);
        mysql_query ("INSERT INTO tablename (roomType, size, roomName )
        VALUES (\'$type\',\'$size\', \'$name\')
        ");

	// Print out contents accepted
	echo "You have successfully added this course information to the database! <br>";
	echo "Room Type: $type <br>";
	echo "Room Size: $size <br>";
	echo "Room Name: $name <br>";

include("footer.php");

?>

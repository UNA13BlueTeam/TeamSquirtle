<?php
	include_once("../includes/db.php");
	$link = mysqli_connect($host, $user, $pass, $port);

	$createCourses = "CREATE TABLE `courses` (
  						`id` int(11) NOT NULL AUTO_INCREMENT,
  						`courseName` varchar(9) COLLATE latin1_general_ci NOT NULL,
  						`dsection` int(11) NOT NULL,
  						`nsection` int(11) NOT NULL,
  						`isection` int(11) NOT NULL,
  						`classSize` int(11) NOT NULL,
  						`roomType` char(1) COLLATE latin1_general_ci NOT NULL,
  						`hours` int(11) NOT NULL,
  						PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;";
	$success = mysqli_query($link, $createCourses);
	if($success){
		echo ("<h1>Table created</h1>");
	}else{
		echo ("<h1>Query Failed</h1>");
	}
	mysqli_close($link);
?>
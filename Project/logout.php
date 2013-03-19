<?php header("Location: index.php"); ?>
<?php include("header.php"); ?>
<?php 
	echo("<h1>Logging out...</h1>");
	http_redirect("index.php");
	exit;
		

?>
<?php include("footer.php"); ?>
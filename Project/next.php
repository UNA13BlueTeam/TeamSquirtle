<html>
<body>
	Welcome <?php echo $_POST["fname"]; ?>!<br>
	You are <?php echo $_POST["age"]; ?> years old.
</body>
</html>

<form action="hello.php">
	<input type="submit">
</form>
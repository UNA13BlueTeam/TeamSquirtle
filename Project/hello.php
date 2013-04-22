<!DOCTYPE html>
<body>
	<html>
	
		<h1>The Hello World Program</h1>
		<html>
		<body>

			<form action="next.php" method="post">
				Name: <input type="text" name="fname">
				Age: <input type="text" name="age">
				<input type="submit">
			</form>

		</body>
		</html>
		
		
		<?php
			//string.explode() converts a string into an array
			//VARIABL/LITERAL TESTING
				echo "Hello, world! <br>	";
				$x = 5;
				$y = 6;
				$z = $x + $y;
				echo "\n" . $z;
			//END
			
			//CONDITIONAL TESTING
			//THEY'RE IDENTICAL TO C/C++
			$t=date("H");
			echo $t . "<br>";
			if ($t<"20")
			  {
			  echo "Have a good day!";
			  }
			  
			//SWITCH STATEMENTS ARE IDENTICAL TO C++
			$supernatural = array("Sam", "Dean", "Bobby", "Ruby");
			foreach($supernatural as $char)
				echo $char . "<br>";
				
			//count function returns number of elements in array
			//combine this with explode() function
			echo "Length of \$supernatural is " . count($supernatural) . "<br>";
			
			$string = "One    two   three      four";
			$str = preg_split('/\s+/', $string);	//use in scanners?
			$str_arr = (explode(" ", $string));
			echo "<br>";
			echo $str[0];
			echo $str[1];
			echo $str[2];
			echo $str[3];
			echo $string .   "<br>";
			echo $str_arr[0];
			echo $str_arr[1];
			for($i=0; $i<count($supernatural); $i++)
				echo $supernatural[$i] . "<br>";
			
			
			echo "1 + 24 = " . add(1, 24) . "<br>";
			
			//FUNCTION TESTING
				speak();
				speak();
				speak();
				
				$txt = "You don't tell me what to do!";
				doAsISay($txt);
				function speak()
				{
					echo " Bark! <br>";
					global $x;
					static $i = 0;
					echo $i;
					$i++;
					echo " " . $x . "<br>";
				}
				
				function add($l, $r)
				{
					return $l + $r;
				}
				
				function doAsISay($par)
				{
					echo $par . "<br>";
				}
			//ENDFUNCTION TESTING
			echo "After all that <br>";
		?>
	</html>
</body>
			
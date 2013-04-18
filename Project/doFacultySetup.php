<?php 
	session_start();
	include("includes/db.php");
	global $host, $user, $pass, $db, $port;
	$link = mysqli_connect($host, $user, $pass, $db, $port);
		
	$password = $_POST['pass1'];
	$passCheck = $_POST['pass2'];
	$user = $_SESSION['username'];
	
	if(($password === $passCheck) and (verifyPassword($password) == true))
	{
		// $password = crypt($password);
		$query = "UPDATE users SET password = '$password', firstLogOn = '0' WHERE username = '$user'";
		$insert = mysqli_query($link, $query);
		header("Location: facultyHome.php");
	}
	else
	{
		$_POST['invalid'] = true;
		header("Location: setup.php");
	}
	
	
	

	
/*-----------------------------------------------------------------------------------------------
 ********************** Function Prologue Comment: verifyPassword ********************
 * Preconditions: $line is a string that contains the password to be checked.
 *
 * Postconditions: Function will return true if password is meets the specified
 *                  requirements. Function will return false if not valid.
 * Function Purpose: To validate a password. If password is not valid for any reason the appropriate 
 *                   message will be displayed.
 * Input Expected: A string that contains the password to check.
 *
 * Exceptions/Errors Thrown: Invalid password length, password must begin with a character, password 
 *                           must contain a digit, password must contain a special character, password
 *                           cannot contain a blank or tab.
 * Files Accessed: None
 *
 * Function Pseudocode Author: Cody Herring
 *
 * Function Author: Alla Salah
 *
 * Date of Original Implementation: 4-3-2013 and 4-4-2013
 *
 * Tested by SQA Member (NAME and DATE): Alla 4-3-2013 and 4-4-2013
 * 
 ** Modifications by:
 * Modified By (Name and Date): Michael Debs and Jared Cox, 4-4-2013
 * Modifications Description:	Fixed logical errors.  Didn't catch invalid characters.
 *								Implemented constants ($MINPASSWORDLENGTH and $MAXPASSWORDLENGTH).
 * 							
 * Modified By (Name and Date):
 * Modifications Description:
 -------------------------------------------------------------------------------------------------*/
 
        
function verifyPassword($line)
{
    //Varaible declarations and initalization
    $sizeOfLine = strlen(trim($line));
    $index=0;
    $foundDigit= false;
    $foundSpecialSymbol=false;
    $foundWhiteSpace=false;
    $invalidPassword=false;
	$MINPASSWORDLENGTH = 6;		//constant
	$MAXPASSWORDLENGTH = 10;	//constant
	$validSpecialSymbols = array('?', '.', ',', '!');
	
	
    
    //Checks the length of the password 
    if($sizeOfLine < $MINPASSWORDLENGTH || $sizeOfLine > $MAXPASSWORDLENGTH)
    {
        printf("Error: Invalid password length.<br>");
		printMessage();
        return false;
    }
    
	if(ctype_alpha($line[$index]) == false)
	{//$index is always 0 here
		printf("Error: Password must begin with a character.<br>");
        printMessage();
		return false;
	}
    
    while($index < strlen($line))
    {
        if(ctype_digit($line[$index]) == true)
        {
            $foundDigit=true;
        }
        else if(in_array($line[$index], $validSpecialSymbols) == true)
        {
            $foundSpecialSymbol=true;
        }
		else if((ctype_alnum($line[$index]) == false) and (in_array($line[$index], $validSpecialSymbols) == false))
		{
			printf("Error: Invalid character found.<br>");
			printMessage();
			return false;
		}
        $index = $index + 1;
    }//end while loop
    
    if($foundDigit==false) 
    {
        printf("Error: password must contain a digit. <br>");
        printMessage();
		return false;		
    }
    if($foundSpecialSymbol==false )
    {
        printf("Error: password must contain a special symbol. <br>");
        printMessage();
		return false;
    }
    else
    {
        printf("Valid password. <br>");
        return true;
    }
    
    
}//end function	
	
	
	
/*-----------------------------------------------------------------------------------------------
 ********************** Function Prologue Comment: printMessage ********************
 * Preconditions: Function is only intended as a helpful guide to the various password reqirements
 *                used only when an invalid password is recognized.
 * Postconditions:None  
 *
 * Function Purpose: Function is only intended as a helpful guide to the various password reqirements
 *                   used only when an invalid password is recognized.
 * Input Expected: None
 *
 * Exceptions/Errors Thrown: None
 *
 * Files Acessed: None
 *
 * Function Pseudocode Author: Alla Salah
 *
 * Function Author: Alla
 *
 * Date of Original Implementation: 4-3-2013
 *
 * Tested by SQA Member (NAME and DATE): Alla Salah 4-3-2013
 * 
 ** Modifications by:
 * Modified By (Name and Date): Michael Debs and Jared Cox, 4-4-2013
 * Modifications Description: Fixed spelling error and added #5 to the message
 *
 * Modified By (Name and Date):
 * Modifications Description:
 -------------------------------------------------------------------------------------------------*/  
function printMessage()
{
    echo("_____________________________________________________________________<br>");
    echo("Password requirements include the following: <br>");
    echo("1. Must begin with an upper-case or lower-case alphabetic character.<br>");
    echo("2. Must contain at least one of the following: , . ! ? <br>");
    echo("3. Must contain at least one digit 0-9. <br>");
    echo("4. Must have a total length of 6 to 10 chacraters. <br>");
	echo("5. Must NOT contain spaces or tabs. <br>");
    echo("_____________________________________________________________________<br>");
}
	
	
	
	
?>
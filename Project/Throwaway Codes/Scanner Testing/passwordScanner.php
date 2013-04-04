<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title></title>
</head>
<body>
<?php
$fileName = 'P1.txt';
$lineNumber=1;
$inputFile = fopen ($fileName,"r");
if($inputFile == NULL)
{
    echo("Error: Cannot open file.");     
}
         
//checks for empty file
if(filesize($fileName)==0)
{
    echo("Recognized an empty file.");
}
else
{
    while(!feof($inputFile))
    {
        $line = fgets($inputFile);
        if (feof($inputFile))
        {
            //Insert the carriage and new line charater if at the last line in file
            $line= $line . chr(13);
            $line= $line . chr(10);
        }           
        $result1 = verifyPassword($line,$lineNumber);
        $lineNumber= $lineNumber + 1;
        printf("<br>");
    }//end while loop  
}//end else   
/*-----------------------------------------------------------------------------------------------
 ********************** Function Prologue Comment Template ********************
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
 * Date of Original Implementation: 4-3-013 and 4-4-2013
 *
 * Tested by SQA Member (NAME and DATE): Alla 4-3-13 and 4-4-2013
 * 
 ** Modifications by:
 * Modified By (Name and Date):
 * Modifications Description:
 *
 * Modified By (Name and Date):
 * Modifications Description:
 -------------------------------------------------------------------------------------------------*/
 
        
function verifyPassword($line,$lineNumber )
{
    printf("line %d: %s <br>",$lineNumber,$line);
    //Varaible declarations and initalization
    $sizeOfLine= strlen($line)-2;
    $index=0;
    $foundDigit= false;
    $foundSpecialSymbol=false;
    $foundWhiteSpace=false;
    $invalidPassword=false;
    
    //Checks the length of the password 
    if($sizeOfLine < 6 || $sizeOfLine >10 || $sizeOfLine == 0)
    {
        printf("Error on line %d. Invalid password length.<br>",$lineNumber);
        $invalidPassword=true;
    }
    
    //Checks if password begins with a lower-case or upper-case alphabetic character
    if (!(ord($line[0]) >= 65 || ord($line[0]) <= 90 ) || !(ord($line[0]) >= 97 || ord($line[0]) <= 122 ) )
    {        
         printf("Error on line %d. Password must begin with a character.<br>",$lineNumber);
         $invalidPassword=true;       
    }
    
    
    if($invalidPassword== true)
    {
        printMessage();
        return false;
    }
    while($index < strlen($line))
    {
        if(ord($line[$index]) >= 48 && ord($line[$index]) <= 57)
        {
            $foundDigit=true;
        }
        else if ($line[$index]== '?' || $line[$index]== '.' || $line[$index]== '!' || $line[$index]== ',' )
        {
            $foundSpecialSymbol=true;
        }
        else if ($line[$index]== 32 || $line[$index] == 9)
        {
            $foundWhiteSpace=true;
        }
        $index= $index + 1;
    }//end while loop
    
    if($foundDigit==false) 
    {
        printf("Error on line %d, password must contain a digit. <br>",$lineNumber);
        $invalidPassword=true;
    }
    if($foundSpecialSymbol==false )
    {
        printf("Error on line %d, password must contain a special symbol. <br>",$lineNumber);
        $invalidPassword=true;
    }
    if ($foundWhiteSpace==true)
    {
        printf("Error on line %d, password cannot contain a space or tab. <br>",$lineNumber);
        $invalidPassword=true;
    }
    if($invalidPassword==true)
    {
        printMessage();
        return false;
    }
    else
    {
        printf("Line %d: Valid password. <br>",$lineNumber);
        return true;
    }
    
    
}//end function

/*-----------------------------------------------------------------------------------------------
 ********************** Function Prologue Comment Template ********************
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
 * Modified By (Name and Date):
 * Modifications Description:
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
    echo("4. Must have a totoal length of 6 to 10 chacraters. <br>");
    echo("_____________________________________________________________________<br>");
}
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
 ?>
</body>
</html>

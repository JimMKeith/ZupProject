<?php
require "../zup_php/functions.php"; 

// php code for this page goes here
$redirect       = false;
$toPage         = NULL;
$userid         = NULL;
$email          = NULL;
$name           = NULL;
$password1      = NULL;
$password2      = NULL;

$signUp = (isset($_GET['signUp'])) ? $_GET['signUp'] : false; 
if ($signUp) { 
    $userid     = (isset($_GET['userid']))    ? $_GET['userid']    : null; 
    $email      = (isset($_GET['email']))     ? $_GET['email']     : null; 
    $name       = (isset($_GET['name']))      ? $_GET['name']      : null; 
    $password1  = (isset($_GET['password1'])) ? $_GET['password1'] : null;
    $password2  = (isset($_GET['password2'])) ? $_GET['password2'] : null;

    $passHash   = password_hash($password1, PASSWORD_DEFAULT);   // customers password
    
    dbConnect();
    
    $newMbr = ['errMsg' => null, 'newId' => null, 'signUpHash' => null];
    $newMbr =  signUp($pdo, $userid, $passHash, $name, $email);
    
    $dupUserIdMsg = $newMbr['errMsg'];
    $mbr_id       = $newMbr['newId'];  
    $signUpHash   = $newMbr['signUpHash'];
 
    if (empty($dupUserIdMsg)) {      
        /* member added 
        /*   - Send a confirmation email                           */
        /*   - set "redirect" true                                 */                     
        /*   - set "toPage" to new location that will direct user  */ 
        /*     to check their email                                */
        $redirect  = mail_the_Welcome($email, $mbr_id, $userid, $name, $signUpHash);
        $redirect  = true;
        $toPage    = 'checkYourEmail.php';
    }
}
// end php code 

require "./code/top_of_page.php"; 

// define main contents of the page 
echo "<!-- begin main content section -->";     
require "./code/signUp.php";
echo "<!-- end main content section -->";  
// end main contents 

require "./code/bottum_of_page.php";  
?>
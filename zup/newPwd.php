<?php
//  This code is ment to be invoked by a link in an email
//  directing the recipient to click on the link and re-set their password
require "../zup_php/functions.php"; 

// php code for this page goes here 
$action    = $_SERVER['PHP_SELF'];
$sent      = NULL;
$hash      = NULL;
$chgPwdMsg = NULL;
$password1 = NULL;
$password2 = NULL;
$password  = NULL;
$name      = NULL;
$user_id   = NULL;
$mbr_id    = NULL;
        
dbConnect(); 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password1 = $_POST['password1']; 
    $password2 = $_POST['password2'];
    $mbr_id    = $_POST['mbr_id'];
    $name      = $_POST['name']; 
    $user_id   = $_POST['user_id'];
    $password  = $password1;
    
    if (chgPwd($pdo, $mbr_id, $password)) {
       $msg = "Please Sign In";
    } else {
       $msg = "Unable to change your password, Please tyy again"; 
    }
    
    header("location: index.php?msg=$msg");
}

if ($_SERVER["REQUEST_METHOD"] == "GET") { 
    $mbr_id    = (isset($_GET['id']))   ? $_GET['id']   : NULL;
    $sent      = (isset($_GET['sent'])) ? $_GET['sent'] : NULL;
    $hash      = (isset($_GET['h']))    ? $_GET['h']    : NULL;
    
    $r         = validate_pwd_reset($pdo, $mbr_id, $sent, $hash);
    $name      = $r['name'];
    $user_id   = $r['user_id']; 
    $newPwdMsg = $r['message'];
    
    if (!$r['ok']) {
        header("location: index.php?msg=$newPwdMsg");
    }
};

// end php code 

require "./code/top_of_page.php"; 

// define main contents of the page 
echo "<!-- begin main content section -->";     
require "./code/newPwd.php"; 
echo "<!-- end main content section -->";  
// end main contents 

require "./code/bottum_of_page.php";    
?>
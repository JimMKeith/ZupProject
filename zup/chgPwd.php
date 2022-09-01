<?php
   require "../zup_php/functions.php"; 
   
   // php code for this page goes here 
   // must be a logged in to use this page    
   $authority = setAuthority();
   if ($authority < 1) {
      returnToIndexPage();
   exit;
}

$user_id = $_SESSION['userid'];
$name    = $_SESSION['name'];
$mbr_id  = $_SESSION['mbr_id'];
$mbr_sts = $_SESSION['mbr_sts'];

$password1 = NULL;
$password2 = NULL;

if ($mbr_sts != 'a') {
    $redirect = true;
    $page     = 'index.php?msg=Not an Active member';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id      = $_POST['user_id']; 
    $mbr_id       = $_POST['mbr_id'];
    $password     = $_POST['password1'];
    
    dbConnect(); 
    
    $chgPwdMsg = chgPwd($pdo, $mbr_id, $password); 
}

   // end php code 
   
   require "./code/top_of_page.php"; 

   // define main contents of the page 
   echo "<!-- begin main content section -->";     
   require "./code/chgPwd.php";
   echo "<!-- end main content section -->";  
   // end main contents 
   
   require "./code/bottum_of_page.php";
?>

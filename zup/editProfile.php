<?php
require "../zup_php/functions.php"; 

// php code for this page goes here 
// must be a logged in to use this page    
$authority = setAuthority();
if ($authority < 1) {
    returnToIndexPage();
    exit;
}

dbConnect();

$editProfileMsg = NULL;
$mbr_id = $_SESSION['mbr_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $user_id     = $_POST['user_id'];
    $mbr_name    = $_POST['mbr_name'];     
    $email       = $_POST['email'];
    $signup_dt   = $_POST['signup_dt'];
    $lst_updt    = $_POST['lst_updt'];
    
    $mbrRecord['mbr_id']   = $mbr_id;
    $mbrRecord['mbr_name'] = $mbr_name;
    $mbrRecord['email']    = $email;
    
    $editProfileMsg = updateMbrProfile($pdo, $mbrRecord);
} else {
    $row = get_A_member($pdo, $mbr_id);
    
    $user_id     = $row['user_id'];
    $mbr_name    = $row['name'];     
    $email       = $row['email'];
    $signup_dt   = $row['signup_dt'];
    $lst_updt    = $row['lst_updt'];
}
// end php code 

require "./code/top_of_page.php"; 

// define main contents of the page 
echo "<!-- begin main content section -->";     
require "./code/editProfile.php";    
echo "<!-- end main content section -->";  
// end main contents 

require "./code/bottum_of_page.php";    
?>
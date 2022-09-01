<?php
require "../zup_php/functions.php"; 

// php code for this page goes here 
// must be a logged in administrator to use this page    
$authority = setAuthority();
if ($authority < 4) {
    returnToIndexPage();
    exit;
}

dbConnect();

$editMemberMsg = NULL;
$mbr_id = (isset($_SESSION['mbr_id'])) ? $_SESSION['mbr_id'] : NULL;
if ($_SERVER["REQUEST_METHOD"] == "GET") {  
    $mbr_id = $_GET['mbr'];
    $row = get_A_member($pdo, $mbr_id);
    
    $user_id     = $row['user_id'];
    $mbr_name    = $row['name'];     
    $email       = $row['email'];
    $signup_dt   = $row['signup_dt'];
    $mbr_type    = $row['mbr_type']; 
    $mbr_sts     = $row['mbr_sts']; 
    $lst_updt    = $row['lst_updt'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $mbr_id      = $_POST['mbr_id'];
    $user_id     = $_POST['user_id'];
    $mbr_name    = $_POST['mbr_name'];     
    $email       = $_POST['email'];
    $signup_dt   = $_POST['signup_dt'];
    $mbr_type    = $_POST['mbr_type']; 
    $mbr_sts     = $_POST['mbr_sts']; 
    $lst_updt    = $_POST['lst_updt'];
    $password    = (isset($_POST['password1'])) ? $_POST['password1'] : NULL;
    
    $mbrRecord['mbr_id']   = $mbr_id;
    $mbrRecord['mbr_name'] = $mbr_name;
    $mbrRecord['email']    = $email;
    $mbrRecord['mbr_type'] = $mbr_type;
    $mbrRecord['mbr_sts']  = $mbr_sts;
    $mbrRecord['password'] = $password;
    
    $editMemberMsg = updateMbrRcd($pdo, $mbrRecord);
}
// end php code 

require "./code/top_of_page.php"; 

// define main contents of the page 
echo "<!-- begin main content section -->";     
require "./code/editMember.php";    
echo "<!-- end main content section -->";  
// end main contents 

require "./code/bottum_of_page.php";    
?>
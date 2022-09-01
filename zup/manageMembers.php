<?php
require "../zup_php/functions.php"; 

// php code for this page goes here 
// must be a logged in administrator to use this page    
$authority = setAuthority();
if ($authority < 4) {
    returnToIndexPage();
    exit;
}

$mbr_id = (isset($_GET['mbr'])) ? $_GET['mbr']   : NULL; 
$goto   = (isset($mbr_id)) ? "rowId" . "$mbr_id" : NULL; 

dbConnect(); 
// end php code 

require "./code/top_of_page.php"; 

// define main contents of the page 
echo "<!-- begin main content section -->";     
require "./code/manageMembers.php";
echo "<!-- end main content section -->";  
// end main contents 

require "./code/bottum_of_page.php";
?>
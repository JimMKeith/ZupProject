<?php
require "../zup_php/functions.php"; 
require "../zup_class/classLib.php";

// php code for this page goes here    
$authority = setAuthority();
if ($authority < 2) {      // only contributing members, trusted members and administrators are allowed to delete a cartoon 
    returnToIndexPage();
    exit;
} 

$bm = (isset($_SESSION['bm'])) ? $_SESSION['bm'] : NULL; 
if (!isset($bm)) {
    $msg = "Missing Bookmark - Delete failed";
    echo "<h5>".$msg."</h5>";
    exit; 
}

$bm   = unserialize($_SESSION['bm']);

$obj  = (isset($_GET['obj_id'])) ? $_GET['obj_id'] : NULL; 
$prev = (isset($_GET['previous_obj'])) ? $_GET['previous_obj'] : $obj;
if (isset($obj)) {
    dbConnect();   
} else {
    returnToIndexPage();    // do not continue without an obj_id
    exit;    
}

$bm->obj = $prev;     /* set the obj_id to obj_id that preceeds to one in the list */
$_SESSION['bm'] = serialize($bm);  /* save the updated bookmark */  

// end php code 

require "./code/top_of_page.php"; 

// define main contents of the page 
echo "<!-- begin main content section -->";   
require "./code/deleteCartoon.php"; 
echo "<!-- end main content section -->";  
// end main contents 

require "./code/bottum_of_page.php";    
?>
<?php
require "../zup_php/functions.php"; 
require "../zup_class/classLib.php"; 

// php code for this page goes here 
// must be a logged in contributing member to use this page    
$authority = setAuthority();
if ($authority < 2) {
    returnToIndexPage();
    exit;
} 

$signedIn_id = (isset($_SESSION['userid'])) ? $_SESSION['userid'] : NULL;
if ($signedIn_id == NULL) {
    // you should NOT be here. You are not signd in
    gotoPage('index.php?msg=You need to be Signed In'); 
    exit;
}
$mbr         = (isset($_SESSION['mbr_id'])) ? $_SESSION['mbr_id'] : NULL; 

$query_type  = 'update'; 
$page        = ltrim($_SERVER['PHP_SELF'],'/');  // "manageCartoons.php"   
$obj_type    = 3;       // slide show  (cartoons)
$obj         = NULL;

dbConnect();

//  show checkboxes for hidden, private, membership, and public selections
$allow_hidden     = true;
$allow_private    = true;  
$allow_membership = true; 
$allow_public     = true; 

//  set default filter criteria
$hidden           = true;
$private          = true;
$membership       = true; 
$public           = true;

//  default uid is always the current signed in user. 
//  It can only changed if the signed in user is also an administtator
$uid        = $signedIn_id;

if (isset($_SESSION['bm'])) {
    $bm         = unserialize($_SESSION['bm']);    
 
    if ($page != $bm->page) {
        // current bookmark is not for this page, get rid of it
        unset($_SESSION['bm']); 
        $bm = NULL;
    } else {
        // get the current bookMark values
        $filter     = $bm->filter;  
        $uid        = $filter['uid'];
        $hidden     = $filter['hidden'];
        $private    = $filter['private'];
        $membership = $filter['membership']; 
        $public     = $filter['public'];  
        
        $obj        = $bm->obj; 
        $goTo       = 'rowId'.$obj;
    }    
} 

if (!isset($bm)) {     
    // start a new bookMark
    $filter['hidden']     = $hidden;
    $filter['private']    = $private;  
    $filter['membership'] = $membership; 
    $filter['public']     = $public;
    $uid                  = $_SESSION['userid']; 
    $filter['uid']        = $uid;
               
    $bm = new bookMark($page, $query_type, $filter); 
    $_SESSION['bm'] = serialize($bm); //  save new bookmark
}            

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $uid        = $_POST['uid'];
     
    $hidden     = (isset($_POST['hidden']))     ? true : false;
    $private    = (isset($_POST['private']))    ? true : false;
    $membership = (isset($_POST['membership'])) ? true : false;
    $public     = (isset($_POST['public']))     ? true : false;
    
    $filter['hidden']     = $hidden;
    $filter['private']    = $private;
    $filter['membership'] = $membership; 
    $filter['public']     = $public;  
    $filter['uid']        = $uid;   
    $bm->filter           = $filter;    //  update the bookMark
    
    $_SESSION['bm'] = serialize($bm);   // save updated bookmark in the session variable      
}
// end php code 

require "./code/top_of_page.php"; 

// define main contents of the page 
echo "<!-- begin main content section -->";     
require "./code/manageCartoons.php";
echo "<!-- end main content section -->";  
// end main contents 

if (isset($goTo)) {
   echo "<script>"; 
   echo "var elm = document.getElementById('".$goTo."');";
   echo "elm.scrollIntoView(true);"; 
   echo "</script>";
} 

require "./code/bottum_of_page.php";    
?>
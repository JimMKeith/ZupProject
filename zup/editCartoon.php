<?php
require "../zup_php/functions.php"; 
require "../zup_class/classLib.php";

// php code for this page goes here -----------------------------------------
$authority = setAuthority();
if ($authority < 2) {      // only contributing members, trusted members and administrators are allowed to edit a cartoon 
    returnToIndexPage();
    exit;
} 

$session_bm = $_SESSION['bm'];
$bm = isset($_SESSION['bm']) ? unserialize($session_bm) : NULL; 
if (isset($bm)) {
   $obj = (isset($bm->obj)) ? $obj : NULL;     
} else {
   $found = false;
   $cartoonMsg = "Missing BookMark - No Cartoon object specified";
}

// initialize variables 
$found          = false;
$mbr_id         = 0;   
$title          = NULL;
$scope          = NULL;
$description    = NULL;
$obj_type       = NULL;
$lst_updt       = NULL;
$lastSeq        = NULL;
$frameCount     = NULL;
$obj            = NULL; 
$user           = (isset($_SESSION['mbr_type'])) ? $_SESSION['mbr_type'] : NULL;  
$cartoonMsg     = NULL;         
dbConnect();  

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $owner_mbr_id   = $_POST['mbr_id'];   
     $title          = $_POST['title'];
     $scope          = $_POST['scope_code'];
     $description    = $_POST['description'];
     $obj_type       = $_POST['obj_type'];
     $lst_updt       = $_POST['lst_updt'];
     $lastSeq        = $_POST['lastSeq'];
     $frameCount     = $_POST['frameCount']; 
     $obj            = $_POST['obj'];
     $found          = true;
     
     //  update the cartoon object table
     if ($found) {
         $result = update_object($pdo, $obj, $title, $scope, $description); 
         $cartoonMsg =$result['msg'];  
     } 
} else {
     if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $obj        = $_GET['obj_id'];
        $bm->obj    = $obj; 
       
        $uid        = (isset($_GET['uid']))        ? $_GET['uid'] : NULL;
        $hidden     = (isset($_GET['hidden']))     ? true : false;
        $prvt       = (isset($_GET['prvt']))       ? true : false;
        $membership = (isset($_GET['membership'])) ? true : false;
        $pblc       = (isset($_GET['pblc']))       ? true : false;
        $obj_id = $obj;

        // set the initial values for the editVideo form  
        if (isset($obj)) {
            $cartoon  = get_object($pdo, $obj); 
            if ($cartoon) {
                if ($cartoon['obj_type'] != 3) {
                    $found = false;
                    $cartoonMsg = "Object ".$obj." is not a cartoon";
                } else {
                    $found = true; 
                }
            } else {
                $found = false;
                $cartoonMsg = "Object ".$obj." not found" ;
            }    
        }

        if ($found) { 
            $mbr_id         = $cartoon['mbr_id'];   
            $title          = $cartoon['title'];
            $scope          = $cartoon['scope_code'];
            $description    = $cartoon['obj_description'];
            $obj_type       = $cartoon['obj_type'];
            $lst_updt       = $cartoon['lst_updt'];
            $lastSeq        = $cartoon['lastSeq'];
            $frameCount     = $cartoon['frameCount']; 
        }
     }
     if (!isset($obj)) {
         $found      = false; 
         $cartoonMsg = "No object specified Cannot edit the Cartoon";
     }    

}               

// end php code -------------------------------------------------------------

require "./code/top_of_page.php"; 

// define main contents of the page 
echo "<!-- begin main content section -->";     
require "./code/editCartoon.php";
echo "<!-- end main content section -->";  
// end main contents 

require "./code/bottum_of_page.php";    
?>
<?php
   require "../zup_php/functions.php"; 
   require "../zup_class/classLib.php"; 
   
   // php code for this page goes here 
   $authority = setAuthority();
   if ($authority < 2) {
       returnToIndexPage();
       exit;
   } 
    
   $obj = (isset($_GET['obj_id'])) ? $_GET['obj_id'] : NULL; 
   dbConnect();
   
   if (isset($_SESSION['bm'])) {
       $bm      = unserialize($_SESSION['bm']);
       $bm->obj = $obj; 
       $url     = $bm->url; 
       
       $_SESSION['bm'] = serialize($bm);   // save updated bookmark in the session variable 
   }    
   // end php code 
   
   require "./code/top_of_page.php"; 

   // define main contents of the page 
   echo "<!-- begin main content section -->";                         
   require "./code/showVid.php";
   echo "<!-- end main content section -->";  
   // end main contents 
   
   require "./code/bottum_of_page.php";    
?>
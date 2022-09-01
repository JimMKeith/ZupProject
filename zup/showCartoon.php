<?php
   require "../zup_php/functions.php"; 
   require "../zup_class/classLib.php"; 
   
   // php code for this page goes here   
   $obj = (isset($_GET['obj_id'])) ? $_GET['obj_id'] : NULL;
   $bm  = (isset($_SESSION['bm'])) ? $_SESSION['bm'] : NULL;  
   if ((isset($obj)) && (isset($bm))) {       
       // fetch and update bookMark 
       $bm             = unserialize($bm);  
       $bm->obj        = $obj; 
       $url            = $bm->url;               
       $_SESSION['bm'] = serialize($bm);  // save updated bookmark in the session variable
   } else {
       trigger_error('"showCartoon.php" - Object Id not supplied and/or Missing Bookmark object',
                     E_USER_WARNING);  
       exit;               
   }
   
   dbConnect();
   // end php code 
   
   require "./code/top_of_page.php";                                 

   // define main contents of the page 
   echo "<!-- begin main content section -->"; 
   require "./code/showCartoon.php";     
   echo "<!-- end main content section -->";  
   // end main contents 
   
   require "./code/bottum_of_page.php";    
?>
<?php
   require "../zup_php/functions.php"; 
   
   // php code for this page goes here 
   $authority = setAuthority();
   if ($authority < 1) {
      returnToIndexPage();
      exit;
  }
   // end php code 
   
   require "./code/top_of_page.php"; 

   // define main contents of the page 
   echo "<!-- begin main content section -->";     
   require "./code/forum.html"; 
   echo "<!-- end main content section -->";  
   // end main contents 
   
   require "./code/bottum_of_page.php";    
?>

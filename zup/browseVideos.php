<?php  
   require "../zup_php/functions.php"; 
   require "../zup_class/classLib.php";
   
   // php code for this page goes here 
   $obj_type    = 4;       // Video or Movie Presentation
   // end php code 
   
   require "./code/top_of_page.php"; 

   // define main contents of the page 
   echo "<!-- begin main content section -->";  
   require "./code/browse.php";
   echo "<!-- end main content section -->";  
   // end main contents 
   
   require "./code/bottum_of_page.php";
?>
<?php
   require "../zup_php/functions.php"; 
   
   // php code for this page goes here  
   $userid     = NULL;
   $password   = NULL;

   if ($_SERVER["REQUEST_METHOD"] == "POST") { 
       $userid     = $_POST['userid'];
       $password   = $_POST['password'];
       
       dbConnect();
       $signInErr = signIn($pdo, $userid, $password);
      
       if (empty($signInErr)) {         
           /* member signed in - go back to main page */
           returnToIndexPage();
           exit;
       }
   } 
   // end php code 
   
   require "./code/top_of_page.php"; 

   // define main contents of the page 
   echo "<!-- begin main content section -->";     
   require "./code/signIn.php";
   echo "<!-- end main content section -->";  
   // end main contents 
   
   require "./code/bottum_of_page.php";    
?>
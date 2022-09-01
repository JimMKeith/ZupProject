<?php  
   // php code for this page goes here 
   require "../zup_php/functions.php"; 
  
   $mbr_id = NULL;
   $hash   = NULL;  
   if ($_SERVER["REQUEST_METHOD"] == "GET") {  
     $mbr_id = (isset($_GET['id'])) ? $_GET['id'] : NULL;
     $hash   = (isset($_GET['h']))  ? $_GET['h']  : NULL;
  }
  
  dbConnect();
  $msg = confirm_signUp($pdo, $mbr_id, $hash);
  // end php code 
   
  require "./code/top_of_page.php"; 

  // define main contents of the page 
  echo "<!-- begin main content section -->";     
  $redirect  = true;
  $toPage    = "index.php?msg=$msg";
  echo "<!-- end main content section -->";  
  // end main contents 
   
  require "./code/bottum_of_page.php";
?>
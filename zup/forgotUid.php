<?php
   require "../zup_php/functions.php"; 
   
   // php code for this page goes here 
   $email = NULL;
   if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email    = $_POST['email'];  
        $ok       = false; 
        dbConnect(); 
        $dtls         = get_userIds($pdo, $email);
        $ok           = $dtls['ok']; 
        $forgotUidMsg = $dtls['msg'];
        $uids         = $dtls['uids']; 
        if ($ok) {
            $ok = send_userId_email($email, $uids); 
            $redirect  = true;
            $toPage    = "forgotUidCheck.php";     
        }
   }  
        
   // end php code 
   
   require "./code/top_of_page.php"; 

   // define main contents of the page 
   echo "<!-- begin main content section -->";     
   require "./code/forgotUid.php";
   echo "<!-- end main content section -->";  
   // end main contents 
   
   require "./code/bottum_of_page.php";
?>


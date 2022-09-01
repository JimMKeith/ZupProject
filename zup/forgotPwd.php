<?php
   require "../zup_php/functions.php"; 
   
   // php code for this page goes here 
   $user_id = NULL;
   if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user_id  = $_POST['user_id'];  
        $ok       = false; 
        dbConnect(); 
        $dtls         = get_user_record($pdo, $user_id);
        $ok           = $dtls['ok']; 
        $forgotPwdMsg = $dtls['msg'];
        if ($ok) {
            $mbr_id       = $dtls['mbr_id'];
            $name         = $dtls['name'];  
            $email        = $dtls['email'];  
            $today        = $dtls['today'];
            $hash = update_mbr_hash($pdo, $mbr_id);
            if ($hash) {   
                 $ok = send_pw_reset_email($mbr_id, $user_id, $name, $email, $hash, $today); 
                 $redirect  = true;
                 $toPage    = "forgotPwdCheck.php";     
            }
        }
   }  
        
   // end php code 
   
   require "./code/top_of_page.php"; 

   // define main contents of the page 
   echo "<!-- begin main content section -->";     
   require "./code/forgotPwd.php";
   echo "<!-- end main content section -->";  
   // end main contents 
   
   require "./code/bottum_of_page.php";
?>

<?php
   require '../zup_php/functions.php';
   
   $email = 'jim@programmingbc.com';
   $mbr_id = 1001;
   $userid = 'jim';
   $hash_pass = '$2y$10$m1wMSTkHfRNLLg7idrQbweujDRFUh2rbOT6u0m4SD0n.';
     
   $request_scheme = $_SERVER['REQUEST_SCHEME'];
   $server_name    = $_SERVER['SERVER_NAME']; 
   $self           = $_SERVER['PHP_SELF'];
   $get            = '?id='.$mbr_id.'&h='.$hash_pass;   
   $verifyLink     = $request_scheme.'://'.$server_name.$self.$get; 
  
   $filename = './emails/welcome.html'; 
   $msg = ''; 
  
   if (is_file($filename)) {
        echo "$filename found OK"; 
        $msg = file_get_contents($filename);
   } else {
        echo "Cannot find $filename";
        exit;
   }

   $msg         = nl2br($msg);
   $htmlBody    = str_replace('%%verifyLink%%', $verifyLink, $msg);
   $subject     = "Welcome to Dr. Zup";
   $sender_name = "Zup";
   $attach      = "./images/zup.png";
    
   zupMail($email, $mbr_id, $sender_name, $subject, $htmlBody, $attach);
?>

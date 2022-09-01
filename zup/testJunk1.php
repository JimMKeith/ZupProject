<?php
  $hash       = 'khlihX798785Y#^^)&-=';    
  $mbr_id     = 1025;
  
  $request_scheme = $_SERVER['REQUEST_SCHEME'];
  $server_name    = $_SERVER['SERVER_NAME']; 
  $self           = $_SERVER['PHP_SELF'];
  $get            = '?id='.$mbr_id.'&h='.$hash;   
  $verifyLink     = $request_scheme.'://'.$server_name.$self.$get; 
  
  $filename = './emails/welcome.html'; 
  
  if (is_file($filename)) {
        ob_start();
        include $filename;
        $msg = ob_get_clean();
  } else {
        echo "Cannot find $filename";
        exit;
  }
  
  $msg = nl2br($msg);
  $msg = str_replace('%%verifyLink%%', $verifyLink, $msg);
  echo $msg; 
?>
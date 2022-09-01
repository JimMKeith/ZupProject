<?php
   echo "</div>  <!-- end main content -->";    
   echo "<!-- begin space reserved for advertisments -->";                            
   require "./code/adv1.php";        //  includes the advertisments                                                                                                 
   echo "</div>  <!-- end of main page row -->"; 
   require "./code/footer.html";
   echo "</div>  <!-- end of main container -->";  
   if (isset($redirect)) {
       if ($redirect) {
           $page = (isset($toPage)) ? $toPage : "index.php";
           echo "<script>";
           echo "window.location.assign('$page')";     
           echo "</script>"; 
       }
   }
   echo "</body></html>"; 
?>

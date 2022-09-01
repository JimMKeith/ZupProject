<?php

require "../zup_php/functions.php"; 

// php code for this page goes here 
$log     = NULL;
$message = NULL;
if (isset($_SERVER["REQUEST_METHOD"])) { 
    if ($_SERVER["REQUEST_METHOD"] == "GET") { 
        $log     = (isset($_GET['log'])) ? $_GET['log'] : NULL;
        $message = (isset($_GET['msg'])) ? $_GET['msg'] : NULL;

        if (!empty($log)) {         
            if ($log == 'off') {
                logout();
            }
        } 
    }
}
// end php code 

require "./code/top_of_page.php"; 

// define main contents of the page 
echo "<!-- begin main content section -->";     
require "./code/frontpage.php";    
echo "<!-- end main content section -->";  
// end main contents 

require "./code/bottum_of_page.php";    
?>
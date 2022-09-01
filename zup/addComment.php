<?php
   require "../zup_php/functions.php"; 
   require "../Zup_class/classLib.php"; 
   
   // php code for this page goes here
   dbConnect();
   
   $obj             = 0;
   $uid             = 0; 
   $bm              = NULL;
   
   $user_id         = NULL;
   $mbr_id          = NULL;
   $title           = NULL;
   $obj_type        = NULL;
   $scope_code      = NULL;
   $obj_description = NULL;
   $lst_updt        = NULL;
   $now             = NULL;
      
   if (isset($_SESSION['bm'])) {
       $bm = unserialize($_SESSION['bm']); 
       $obj = $bm->obj;
       $uid = $bm->uid;
       $objDtls = getObjDtls($pdo, $obj); 
       if ($objDtls) {  
           $user_id         = $objDtls['user_id']; 
           $mbr_id          = $objDtls['mbr_id'];
           $title           = $objDtls['title'];
           $obj_type        = $objDtls['obj_type'];
           $type_desc       = $objDtls['type'];
           $scope_code      = $objDtls['scope_code'];
           $obj_description = $objDtls['obj_description'];
           $lst_updt        = $objDtls['lst_updt'];
           $now             = $objDtls['now'];
           
           $obj_dt          = date_create($lst_updt);
           $obj_ymd         = date_format($obj_dt, 'Y-m-d');
           
       } 
   }
   
   //  process POST         
   if ($_SERVER["REQUEST_METHOD"] == "POST") {    
       $user_id             = $_POST['user_id']; 
       $obj                 = $_POST['obj'];
       $title               = $_POST['title'];  
       $type_desc           = $_POST['type_desc']; 
       $obj_ymd             = $_POST['obj_ymd']; 
       $now                 = $_POST['now'];
       $comment             = $_POST['comment']; 
       
       $com_sts             = 'a';    // active  
       
       $comment_count       = insertComment($pdo, $obj, $user_id, $com_sts, $comment);
       
       // go back to the showVid page 
       switch($obj_type) {
            case 3:  $goto = "../showCartoon.php?obj_id=$obj"; 
                     break; 
            case 4:  $goto = "../showVid.php?obj_id=$obj"; 
                     break;
            default: $goto = "../index.php?msg='Unkown object type'";          
       }
       gotoPage($goto);
   } 

   // end php code 
   
   require "./code/top_of_page.php"; 

   // define main contents of the page 
   echo "<!-- begin main content section -->";     
   require "./code/addComment.php";
   echo "<!-- end main content section -->";  
   // end main contents 
   
   require "./code/bottum_of_page.php";
?>

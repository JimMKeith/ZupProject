<?php
   dbConnect();
   $goTo  = NULL; 
   $user  = (isset($_SESSION['userid'])) ? $_SESSION['userid'] : NULL;  
     
   // 'allow' in contex here means allow user to filter the list
   // Guests can only see objects in a 'public' scope.
   // Allowing a Guest to switch scope viewing scope is not allowed.    
   $allow_hidden     = false;       // hidden videos will not appear in any list           
   $allow_private    = (isset($user)) ? true : false; // user still must be current owner
   $allow_membership = (isset($user)) ? true : false; 
   $allow_public     = (isset($user)) ? true : false;    
   
   // --------------------------------------------------------------------------
   // set the default values for a new bookMark  
   $uid         = (isset($user)) ? $user : '*';      // "*"  means ALL members
   if (isset($user)) {
        // signed in users are allowed to view other members objects and their own Private objects
        $private     = true;
        $membership  = true;
   } else {
        // guests or memnbers not signed in can olny view Public objects 
        $private     = false;
        $membership  = false;
   }
   $hidden      = false;   // no one is allowed to view hidden objects     
   $public      = true;    // everyone can view public objects
   
   $query_type  = 'browse'; 
   $page        = ltrim($_SERVER['PHP_SELF'],'/');  // "browseCartoons.php"   
   
   $filter = ['uid'         => $uid, 
              'hidden'      => $hidden,
              'private'     => $private,
              'membership'  => $membership,
              'public'      => $public]; 
   //  end set defaults 
   // --------------------------------------------------------------------------           
 
   // --------------------------------------------------------------------------
   // if there is a current bookMark 
   //    get rid of it if it is not for this page
   // else
   //    use the bookmarked values 
   if (isset($_SESSION['bm'])) {
       $bm =  unserialize($_SESSION['bm']);
       if ($bm->page != $page) {
           // current bookmark is not for this page, get rid of it
           // and create a new one with default values
           unset($_SESSION['bm']); 
           $bm = NULL;         
       } else {
           // get the current bookmark values
           $obj          = $bm->obj; 
           $goTo         = (isset($obj)) ? 'rowId'.$obj : NULL; 
            
           $filter       = $bm->filter;
            
           $uid          = $filter['uid']; 
           $hidden       = $filter['hidden'];         
           $private      = $filter['private'];        
           $membership   = $filter['membership'];     
           $public       = $filter['public'];          
       }     
   }
   // --------------------------------------------------------------------------   
   
   // --------------------------------------------------------------------------
   // make sure there is a bookMark
   if (!isset($_SESSION['bm'])) {
       $bm = new bookMark($page, $query_type, $filter);
       $_SESSION['bm'] = serialize($bm);  // save bookmark in a session variable
   } 
   // --------------------------------------------------------------------------
    
   if ($_SERVER["REQUEST_METHOD"] == "POST") { 
       $uid        = (isset($_POST['uid']))        ? $_POST['uid'] : '*';
       $private    = (isset($_POST['private']))    ? true : false;
       $membership = (isset($_POST['membership'])) ? true : false;
       $public     = (isset($_POST['public']))     ? true : false;
       $hidden     = false; 
        
       $filter = ['uid'         => $uid,
                  'hidden'      => false, // hidden videos should never be shown
                  'private'     => $private,
                  'membership'  => $membership,
                  'public'      => $public]; 
       // save the updated bookMark   
       $bm->filter      = $filter;    
       $_SESSION['bm']  = serialize($bm); // save bookmark in a session variable          
   }
   
   require_once "./code/filterForm.php";   // main content 
   $rows = browse($pdo, $filter, $obj_type);   

   if (isset($goTo)) {
       echo "<script>"; 
       echo "var elm = document.getElementById('".$goTo."');";
       echo "elm.scrollIntoView(true);"; 
       echo "</script>"; 
   } 
?>

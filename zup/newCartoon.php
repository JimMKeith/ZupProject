<?php
require "../zup_php/functions.php"; 

// php code for this page goes here -----------------------------------------
$authority = setAuthority();
if ($authority < 2) {
    returnToIndexPage();
    exit;
} 

// initialize variables
$obj_id        = NULL;
$mbr_id        = NULL;
$part_id       = NULL;
$cFile_type    = 'image'; 
$mime_code     = NULL; 
$seq           = 10;

$cTitle        = NULL;
$cScope        = NULL;
$cDesc         = NULL;
$cFile         = NULL;
$cTmp          = NULL;

$cMsg          = ''; 
$newCartoonMsg = '';

$mbr_id        = $_SESSION['mbr_id'];
$mbr_image_dir = "members/mbr".$mbr_id."/image/";
$mbr_video_dir = "members/mbr".$mbr_id."/video/";
$mbr_audio_dir = "members/mbr".$mbr_id."/audio/";

$continue_upload    = true; 
$cOverwrite         = false;

dbConnect(); 

//  process POST         
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $cFile       = $_FILES['cFile']['name'];
    $cMime       = $_FILES['cFile']['type']; 
    $cErr        = $_FILES['cFile']['error'];
    $cTmp        = $_FILES['cFile']['tmp_name']; 
    $cTitle      = $_POST['cTitle'];
    $cScope      = $_POST['cScope'];
    $cDesc       = $_POST['cDesc']; 

    $cTitle      = trim($cTitle);
    $cDesc       = trim($cDesc);
    $obj_id      = NULL;

    $cOverwrite = (isset($_POST['cOverwrite'])) ? true : false;          

    $cMsg = validate_upload_file('cFile','image');


    if ($cMsg) {
        $continue_upload = false;   // there was a validation error
    }    

    if ($continue_upload) {
        $cResult = move_to_mbr_directory($cTmp,$mbr_image_dir,$cFile,$cOverwrite); 
        $cMsg    = $cResult['msg'];
        $cFile   = $cResult['newName'];
        if ($cResult['errLvl'] > 0) {
            $continue_upload = false;
        }
    }                                                        

    if ($continue_upload)  {
        $newObj =  ['title'           => $cTitle, 
                    'scope'           => $cScope, 
                    'obj_description' => $cDesc,
                    'vFile'           => $cFile,
                    'vMime_code'      => $cMime,
                    'pFile'           => NULL,
                    'pMime_code'      => NULL,
                    'object_type'     => 3];    //  a cartoon or slideshow
        $rslt = insert_new_object($pdo, $newObj);
        $newCartoonMsg = "$rslt[1]";
        
        // insert_new_object() returns an array rslt[]"   
        // If "insert_new_object()" is successful the 
        // object id (obj_id) of the new object will be
        // in $rslt[2] 
        // 
        // The presence of a value in $obj_id will trigger  
        // the enabling of the 'view' button. The user may 
        // navigate directly to the new video and view it.   
        if (isset($rslt[2])) {
            $obj_id = $rslt[2];
        }     
    }

}  //  end of processing the POST 

// make sure empty messages do not show up in the form
if ($cMsg == '') {
    $cMsg = NULL;
}

if ($newCartoonMsg == '') {
    $newCartoonMsg = NULL;
}
// end php code -------------------------------------------------------------

require "./code/top_of_page.php"; 

// define main contents of the page 
echo "<!-- begin main content section -->";     
require "./code/newCartoon.php";
echo "<!-- end main content section -->";  
// end main contents 

require "./code/bottum_of_page.php";    
?>
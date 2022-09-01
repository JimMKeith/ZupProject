<?php
require "../zup_php/functions.php"; 

// php code for this page goes here -----------------------------------------
$authority = setAuthority();
if ($authority < 2) {
    returnToIndexPage();
    exit;
} 

// initialize variables
$obj_id     = NULL;
$mbr_id     = NULL;
$part_id    = NULL;
$vFile_type = 'video'; 
$pFile_type = 'poster'; 
$mime_code  = NULL; 
$seq        = 1;

$vTitle     = NULL;
$vScope     = NULL;
$vDesc      = NULL;
$vFile      = NULL;
$pFile      = NULL;
$vTmp       = NULL;
$pTmp       = NULL;

$vMsg       = ''; 
$pMsg       = '';
$newVideoMsg = '';

$mbr_id        = $_SESSION['mbr_id'];
$mbr_image_dir = "members/mbr".$mbr_id."/image/";
$mbr_video_dir = "members/mbr".$mbr_id."/video/";
$mbr_audio_dir = "members/mbr".$mbr_id."/audio/";

$continue_upload    = true; 
$poster_file_input  = true;
$vOverwrite         = false;
$pOverwrite         = false; 

dbConnect(); 

//  process POST         
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $vFile       = $_FILES['vFile']['name'];
    $vMime       = $_FILES['vFile']['type']; 
    $vErr        = $_FILES['vFile']['error'];
    $vTmp        = $_FILES['vFile']['tmp_name']; 
    $pFile       = $_FILES['pFile']['name'];
    $pMime       = $_FILES['pFile']['type']; 
    $pErr        = $_FILES['pFile']['error'];
    $pTmp        = $_FILES['pFile']['tmp_name'];
    $vTitle      = $_POST['vTitle'];
    $vScope      = $_POST['vScope'];
    $vDesc       = $_POST['vDesc']; 

    $vTitle      = trim($vTitle);
    $vDesc       = trim($vDesc);

    $vOverwrite = (isset($_POST['vOverwrite'])) ? true : false;
    $pOverwrite = (isset($_POST['pOverwrite'])) ? true : false;  
    $obj_id     = NULL;           

    $vMsg = validate_upload_file('vFile','video');

    if (($pFile == '') && ($pErr = 4)) {
        // do nothing, a poster file was not selected, nothing was uploaded
        $pMsg = '';             // there is no poster file validation error 
        $poster_file_input = false;
    } else {
        $pMsg = validate_upload_file('pFile','image');   
    }

    if (($vMsg) || ($pMsg)) {
        $continue_upload = false;   // there was a validation error
    }    

    if ($continue_upload) {
        $vResult = move_to_mbr_directory($vTmp,$mbr_video_dir,$vFile,$vOverwrite); 
        $vMsg    = $vResult['msg'];
        $vFile   = $vResult['newName'];
        if ($vResult['errLvl'] > 0) {
            $continue_upload = false;
        }
    } 

    if (($continue_upload) && ($poster_file_input)) {
        $pResult = move_to_mbr_directory($pTmp,$mbr_image_dir,$pFile,$pOverwrite); 
        $pMsg    = $pResult['msg'];
        $pFile   = $pResult['newName'];
        if ($pResult['errLvl'] > 1) {
            $continue_upload = false;
        } 
    }                                                        

    if ($continue_upload)  {
        $newObj =  ['title'           => $vTitle, 
                    'scope'           => $vScope, 
                    'obj_description' => $vDesc,
                    'vFile'           => $vFile,
                    'vMime_code'      => $vMime,
                    'pFile'           => $pFile,
                    'pMime_code'      => $pMime,
                    'object_type'     => 4];   // video 
        $rslt = insert_new_object($pdo, $newObj);
        $newVideoMsg = "$rslt[1]";  
        
        // if insert_new_object() returns an object Id in  
        // the result rslt then we set $obj_id here and pass 
        // it along. When the 'newVideoForm' is displayed
        // the presence of a value in $obj_id will trigger  
        // the enabling of the 'view' button. The user may 
        // navigate directly to the new video and view it.   
        if (isset($rslt[2])) {
            $obj_id = $rslt[2];
        } 
    }

}  //  end of processing the POST 

// make sure empty messages do not show up in the form
if ($vMsg == '') {
    $vMsg = NULL;
}

if ($pMsg == '') {
    $pMsg = NULL;
}

if ($newVideoMsg == '') {
    $newVideoMsg = NULL;
}
// end php code -------------------------------------------------------------

require "./code/top_of_page.php"; 

// define main contents of the page 
echo "<!-- begin main content section -->";     
require "./code/newVideo.php";
echo "<!-- end main content section -->";  
// end main contents 

require "./code/bottum_of_page.php";    
?>
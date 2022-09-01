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
$seq           = 1;

$cTitle        = NULL;
$cFile         = NULL;
$cTmp          = NULL;
$ovrwrt0       = false;

$fMsg          = ''; 
$addFrameMsg   = '';

$mbr_id        = $_SESSION['mbr_id'];
$mbr_image_dir = "members/mbr".$mbr_id."/image/";
$mbr_video_dir = "members/mbr".$mbr_id."/video/";
$mbr_audio_dir = "members/mbr".$mbr_id."/audio/";

$continue_upload    = true; 
$fOverwrite         = false;

dbConnect(); 

//  process GET
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $obj_id =    $_GET['obj'];
    
    $object = get_object($pdo, $obj_id); 
    if ($object)  {
        $mbr_id          = $object['mbr_id'];
        $cTitle          = $object['title'];
        $obj_type        = $object['obj_type'];
        $cScope          = $object['scope_code'];
        $obj_description = $object['obj_description'];
        $lst_updt        = $object['lst_updt'];
        $lastSeq         = $object['lastSeq'];
        $frameCount      = $object['frameCount'];
        
        $seq             = $lastSeq + 1;
    }
}

//  process POST         
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $cFile       = $_FILES['cFile']['name'];
    $cMime       = $_FILES['cFile']['type']; 
    $cErr        = $_FILES['cFile']['error'];
    $cTmp        = $_FILES['cFile']['tmp_name']; 
    $cTitle      = $_POST['cTitle'];
    $cScope      = $_POST['cScope'];
    $seq         = $_POST['seq'];
    $obj_id      = $_POST['obj_id'];

    $cTitle      = trim($cTitle);

    $fOverwrite = (isset($_POST['ovrwrt0'])) ? true : false;          

    $fMsg = validate_upload_file('cFile','image');


    if ($fMsg) {
        $continue_upload = false;   // there was a validation error
    }    

    if ($continue_upload) {
        $cResult = move_to_mbr_directory($cTmp,$mbr_image_dir,$cFile,$fOverwrite); 
        $fMsg    = $cResult['msg'];
        $cFile   = $cResult['newName'];
        if ($cResult['errLvl'] > 0) {
            $continue_upload = false;
        }
    }                                                        

    if ($continue_upload)  {       
        $result =  insert_new_part($pdo, $cFile, $cMime, $seq, 'image', $obj_id);   
        if ($result['success']) {
            $addFrameMsg = "New frame added to comic";
        } else {
            $addFrameMsg = $result['msg'];
        } 
    }
}  //  end of processing the POST 

// make sure empty messages do not show up in the form
if ($fMsg == '') {
    $fMsg = NULL;
}

if ($addFrameMsg == '') {
    $addFrameMsg = NULL;
}
// end php code -------------------------------------------------------------

require "./code/top_of_page.php"; 

// define main contents of the page 
echo "<!-- begin main content section -->";     
require "./code/addFrame.php";
echo "<!-- end main content section -->";  
// end main contents 

require "./code/bottum_of_page.php";    
?>
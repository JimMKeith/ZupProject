<?php
require "../zup_php/functions.php"; 
require "../zup_class/classLib.php";

// php code for this page starts here 
$authority = setAuthority();
if ($authority < 2) {      // only contributing members, trusted members and administrators are allowed to edit a cartoons
    returnToIndexPage();
    exit;
}

dbConnect();
$obj_id = NULL;
$topMsg = NULL;

$bm = (isset($_SESSION['bm'])) ? unserialize($_SESSION['bm']) : NULL; 
if (isset($bm)) {
   $obj_id = $bm->obj;     
} else {
   $found = false;
   $topMsg = "Missing BookMark";
} 

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $obj_id     = $_GET['obj_id'];
    $bm->obj    = $obj_id;
    $_SESSION['bm'] = serialize($bm); 
    
    $topMsg     = (isset($_GET['msg']))        ? $_GET['msg'] : ''; 
    $errMsg     = '';
    $frames = getCartoonFrames($pdo, $obj_id); 
    $newSeq = 0;
    foreach($frames as $f) {
        if ($f['seq'] > $newSeq) {    
            $newSeq = $f['seq'];    
        } 
    }       
    $newSeq = $newSeq + 1; 
}

$obj_id= (isset($_POST['obj_id'])) ? $_POST['obj_id'] : $obj_id;

$owner     = null; 
$checked   = []; 
$partsList = '';
$part_id_list = getCartoonPartIds($pdo, $obj_id); 
if ($part_id_list['part_count'] == 0) {
    $errMsg = '<h4 class="bg-danger">There are no Cartoon Frames</h4>'; 
} else {
    $partsList = $part_id_list['parts_list']; 
    $partCount = $part_id_list['part_count'];
    $title     = $part_id_list['Cartoon_title'];
    $owner     = $part_id_list['owner'];  
    
    if  ($partCount == 0) {
        $errMsg = "<h4 class='bg-danger text-white'>This Cartoon has no frames</h4>";
    } else {
        $parts = explode(',',$partsList); 
        foreach($parts as $part_id) {
            $checked[$part_id] = true;  
        } 
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid          = $_POST['uid']; 
    $owner        = $_POST['owner'];
    $partsList    = $_POST['partsList'];
    $newSeq       = $_POST['seq0'];
    $ovrwrt0      = (isset($_POST['ovrwrt0'])) ? true : false; 
    
    $newFrame     = $_FILES['frame0']['name'];     // name of new image file for the frame
    $mime         = $_FILES['frame0']['type'];     // new image file mime type   
    $err          = $_FILES['frame0']['error'];    // upload error 
    $tmp          = $_FILES['frame0']['tmp_name'];
    
    $mbr_image_dir = "members/mbr".$owner."/image/";
    
    
    // ================================================ // 
    // Add a new frame if one was entered into the form //
    // ================================================ //
    $newFileMsg   = '';
    $continue     = true;
    if (!empty($newFrame)) {
        //  validate the file
        $vldtRslt =  validate_upload_file('frame0', 'image'); 
        $newFileMsg = ($vldtRslt) ? $vldtRslt : ''; 
        $continue   = ($vldtRslt) ? false : true;  // "false"  means the file passed validation
        $mbrHasFile = file_exists($mbr_image_dir.$newFrame); 
        clearstatcache(); 
        
        // Remove file from the Members Dir only if
        // it the member already has a file of the same name in his "Members Dir" and
        // it is Ok to overwrite it
        if ($continue) {
            if (($mbrHasFile) && ($ovrwrt0)) {
                $remove = remove_file_from_members_dir($pdo, $owner, $newFrame, $mbr_image_dir);
                $newFileMsg = $remove['msg']; 
                $continue = $remove['success'];     
            } 
        }
        
        // Move the file into the members directory
        // If the member already has a copy of the file in his "members dir" and 
        // if is NOT ok to overwrite the file then DO NOT move it up 
        if ($continue) {
            if (!(($mbrHasFile) && (!$ovrwrt0))) {
                $rsult = move_to_mbr_directory($tmp, $mbr_image_dir, $newFrame, $ovrwrt0);
                $newFrame = $rsult['newName'];  // the move strips any ' and  " from the name
                if ($rsult['errLvl'] > 0)  {   
                    $newFileMsg = $rsult['msg'];
                    $continue = false;
                }
            }   
        }        
                      
        //  add frame to the cartoon (insert a new parts record) 
        if ($continue)  {      
            $result =  insert_new_part($pdo, $newFrame, $mime, $newSeq, 'image', $obj_id);   
            if ($result['success']) {
                $addFrameMsg = "New frame added to comic";
            } else {
                $addFrameMsg = $result['msg'];
            } 
        }
    }
     
    $parts        = explode(',',$partsList); 
    $i            = 0;
    $j            = 0;
    $k            = 0; 
    $frameCount   = 0;
    $zeroSeqCount = 0; 
    $checked      = []; 
    $frames       = [];
    $continue     = true; 
    
    // ======================================= //
    // Update or delete cartoon frames in the  //
    // "Members Dir" as appropiate             //
    // ======================================= // 
    foreach ($parts as $p) {
        if ($p == 0) {
            break;     // Part 0 is a new frame to be ADDED to the cartoon, not updated and not deleted
        }
        $frameCount++;
        // create unique names by suffixing names with the part_id ($p) 
        $seq    = 'seq'.$p;
        $oSeq   = 'oSeq'.$p;
        $ovrwrt = 'ovrwrt'.$p;
        $frame  = 'frame'.$p;
        $oFrame = 'oFile'.$p;
        
        //  fetch named items from $_POST 
        $oName  = $_POST[$oFrame];             // old image file for the frames 
        $s      = $_POST[$seq];                // new frame sequence number
        $oS     = $_POST[$oSeq];               // original sequence number assigned to this frame
        $o      = isset($_POST[$ovrwrt]) ? true : false;   // overwrite checkbox check 
        if ($o) {
            $checked += [$ovrwrt => true];    // set the overwrite checkbox "on" for this file    
        }  
  
        //  fetch uploaded named items ($frame) from $_FILES 
        $name   = $_FILES[$frame]['name'];     // name of new image file for the frame
        $mime   = $_FILES[$frame]['type'];     // new image file mime type   
        $err    = $_FILES[$frame]['error'];    // upload error 
        $tmp    = $_FILES[$frame]['tmp_name']; // location new image file is moved to
        
        if ($s == 0) {  // count the frames to be deleted
             $zeroSeqCount++;
        } 
                 
        $mbr_image_dir = "members/mbr".$owner."/image/"; 
        
        $frameMsg = '';     
        if (($s != 0) && (!empty($name))) {
            $ovr = (isset($_POST[$ovrwrt])) ? true : false;
                
            // validate the upload file 
            $vldtRslt =  validate_upload_file($frame, 'image'); 
            if (!($vldtRslt)) {  // "false"  means the file passed validation
                                 //  file is a ok to upload  
                $frameMag = '';
                // remove old file from the members directory 
                if ($name != $oName) { 
                    $remove = remove_file_from_members_dir($pdo, $owner, $oName, $mbr_image_dir);
                    if ($remove['success']) {
                        $oName = NULL;
                        //  put the new file into the members directory
                        $rsult = move_to_mbr_directory($tmp, $mbr_image_dir, $name, $o);
                        $name = $rsult['newName'];  // the move strips any ' and  " from the name
                        if ($rsult['errLvl'] > 0) {
                            $frameMsg = $rsult['msg'];
                        } 
                    } else {
                        $framwMsg = $remove['msg']; 
                    }
                }                           
            } else {
                $frameMsg = $vldtRslt; // validation failed
            }
            
            if (!($frameMsg != '')) {
                $continue = false;
            } 
        }     
             
        // build sn array of frames to pass into buildSeqFramesForm function
        $fRcd = ['owner' => $owner, 'scope' => 0, 'part_id' => $p, 'seq' => $s, 'oSeq' => $oS, 'frame' => $name, 'oFrame' => $oName, 'mime' => $mime, 'frameMsg' => ''];
        $frames[$j++] = $fRcd;                 
    } 
    
    // ========================================= //
    // Update the existing Cartoon frames in the //
    // database as appropiate                    //
    // ========================================= //
    $rcd             = [];
    $frameDltCount   = 0;
    $frameUpdtCount  = 0; 
    $cartoonDltCount = 0;
    
    // continue - process the existing frames for this Cartoon 
    if (($continue) && ($frameCount > 0)) {    
        if ($frameCount == $zeroSeqCount) { 
            //  delete the entire cartoon 
            $cartoonDltCount += deleteWholeCartoon($pdo, $obj_id); 
        } else {
            //  walk thru the existing $frameRcds table and
            //  apply database updates or deletes as required to the 
            //  cartoon frames (parts records) 
            for ($k = 0; $k < count($frames); $k++) {
                $rcd    = $frames[$k]; 
                $part_id = $rcd['part_id'];
                if ($rcd['seq'] == 0) { 
                    // delete the frame 
                    $frameDltCount += deleteFrame($pdo, $part_id); 
                } else {
                    // When the data has changed update database (the parts record)  
                    // if no new Frame file was entered use the old (or original) 
                    // Frames file name (oFrame)
                    if (empty($rcd['frame'])) { 
                        $rcd['frame'] = $rcd['oFrame'];
                    }     
                    if (($rcd['oFrame'] != $rcd['frame']) || ($rcd['oSeq'] != $rcd['seq'])) {
                       // apply database updates to the frame (parts table)
                       $frameUpdtCount += updateFrame($pdo, $rcd);
                    }   
                }
            } 
        }
        
        $reSeqMsg = $frameUpdtCount.'->frames updated  '.$frameDltCount.'->frames deleted  '.$cartoonDltCount.'->Cartoons deleted';   
        $reSeqMsg = '<h3>'.$reSeqMsg.'</h3>';  
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = "seqFrames.php?obj=$obj_id&msg=$reSeqMsg";
        header("Location: http://$host$uri/$extra");
    }  
}     

// end php code 

require "./code/top_of_page.php"; 

// define main contents of the page 
echo "<!-- begin main content section -->";     
require "./code/seqFrames.php";    
echo "<!-- end main content section -->";  
// end main contents 

require "./code/bottum_of_page.php";    
?>
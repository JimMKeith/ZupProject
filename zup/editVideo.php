<?php
require "../zup_php/functions.php"; 

// php code for this page goes here -----------------------------------------
$authority = setAuthority();
if ($authority < 2)  {
    returnToIndexPage();
    exit;
}

$user = (isset($_SESSION['mbr_type'])) ? $_SESSION['mbr_type'] : NULL;           
dbConnect();  

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $obj = (isset($_GET['obj_id'])) ? $_GET['obj_id'] : NULL; 
    $obj_id = $obj;

    // initialize variables 
    $found        = false;
    $title        = '';
    $scope        = 0;
    $description  = '';
    $oldVid       = '';  
    $oldPoster    = '';
    $pOver        = '';
    $vOver        = '';
    $pDelete      = '';
    $newVid       = ''; 
    $newPoster    = '';
    
    $obj_id          = $obj;
    $video_part_id   = 0;
    $poster_part_id  = 0;
    $owner_mbr_id    = 0;
    $oldVid          = '';
    $oldPoster       = '';

    // set the inial values for the editVideo form  
    if (isset($obj)) {
        $dtls  = getVideoDtls($pdo, $obj); 
        $found = $dtls['found']; 
    }

    if ($found) { 
        $owner_mbr_id   = $dtls['mbr_id'];   
        $owner_type     = $dtls['oType']; 
        $owner_sts      = $dtls['oSts'];
        $title          = $dtls['title'];
        $scope          = $dtls['scope'];
        $description    = $dtls['description'];
        $oldVid         = $dtls['oldVid']; 
        $oldPoster      = $dtls['oldPoster']; 
        $video_part_id  = $dtls['video_part_id'];
        $poster_part_id = $dtls['poster_part_id'];          
    }
    // end set inial values
} 

//  create an array to hold all the data that will be received from the editVideo form        
$newDtls = ['obj_id' => NULL,
    'owner_mbr_id'   => NULL,
    'title'          => NULL, 
    'scope'          => 1, 
    'description'    => NULL, 
    'oldVid'         => NULL,  
    'oldPoster'      => NULL, 
    'pOver'          => false, 
    'vOver'          => false,
    'pDelete'        => false,
    'video_part_id'  => NULL,
    'poster_part_id' => NULL,          
    'newVid'         => NULL,
    'vMime'          => NULL,  
    'vErr'           => NULL, 
    'vTmp'           => NULL,  
    'newPoster'      => NULL, 
    'pMime'          => NULL,  
    'pErr'           => NULL, 
    'pTmp'           => NULL]; 

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    // set values for the update procedures 
    $continue                  = true;
    
    $newDtls['obj_id']         = $_POST['obj_id'];
    $newDtls['owner_mbr_id']   = $_POST['owner_mbr_id']; 
    $newDtls['title']          = trim($_POST['title']);
    $newDtls['scope']          = $_POST['scope'];
    $newDtls['description']    = trim($_POST['description']);
    $newDtls['oldVid']         = $_POST['oldVid']; 
    $newDtls['oldPoster']      = $_POST['oldPoster'];
    $newDtls['pOver']          = (isset($_POST['pOver']))   ? $_POST['pOver']   : false;
    $newDtls['vOver']          = (isset($_POST['vOver']))   ? $_POST['vOver']   : false;
    $newDtls['pDelete']        = (isset($_POST['pDelete'])) ? $_POST['pDelete'] : false;
    $newDtls['video_part_id']  = $_POST['video_part_id'];
    $newDtls['poster_part_id'] = $_POST['poster_part_id']; 

    $newDtls['newVid']         = $_FILES['newVid']['name'];
    $newDtls['vMime']          = $_FILES['newVid']['type']; 
    $newDtls['vErr']           = $_FILES['newVid']['error'];
    $newDtls['vTmp']           = $_FILES['newVid']['tmp_name']; 
    $newDtls['newPoster']      = $_FILES['newPoster']['name'];
    $newDtls['pMime']          = $_FILES['newPoster']['type']; 
    $newDtls['pErr']           = $_FILES['newPoster']['error'];
    $newDtls['pTmp']           = $_FILES['newPoster']['tmp_name'];
    
    // set values for the editVideo form 
    $owner_mbr_id              = $newDtls['owner_mbr_id'];   
    $title                     = $newDtls['title'];
    $scope                     = $newDtls['scope'];
    $description               = $newDtls['description'];
    $oldVid                    = $newDtls['oldVid']; 
    $oldPoster                 = $newDtls['oldPoster']; 
    $video_part_id             = $newDtls['video_part_id'];
    $poster_part_id            = $newDtls['poster_part_id'];
    $obj_id                    = $newDtls['obj_id'];  
    $pOver                     = $newDtls['pOver'];
    $vOver                     = $newDtls['vOver'];
    $pDelete                   = $newDtls['pDelete']; 
    $vTmp                      = $newDtls['vTmp'];
    $newVid                    = $newDtls['newVid']; 
    $newPoster                 = $newDtls['newPoster'];
    $pTmp                      = $newDtls['pTmp'];
    
    $newVid    = ($newVid == '')    ? NULL : $newVid;
    $newPoster = ($newPoster == '') ? NULL : $newPoster;
    $oldVid    = ($oldVid == '')    ? NULL : $oldVid;
    $oldPoster = ($oldPoster == '') ? NULL : $oldPoster;
    
    // make sure unwanted messages do not show up in the form
    $vMsg         = NULL;
    $pMsg         = NULL;
    $editVideoMsg = NULL;

    //  apply the updates to the Video object 
    if ($pOver   == 'on') {$pOver   = true;}    
    if ($vOver   == 'on') {$vOver   = true;}     
    if ($pDelete == 'on') {$pDelete = true;} 
    $mbr_image_dir = "members/mbr".$owner_mbr_id."/image/";
    $mbr_video_dir = "members/mbr".$owner_mbr_id."/video/";
    $mbr_audio_dir = "members/mbr".$owner_mbr_id."/audio/";
 
    if (!(empty($newVid))) {
        $vMsg = validate_upload_file('newVid', 'video');    
    }

    if (!(empty($newPoster))) {
        $pMsg = validate_upload_file('newPoster', 'image');    
    }


    if (($vMsg) || ($pMsg)) {
        $continue = false;
    }
    
    if (($pDelete) && (isset($oldPoster)) && ($continue)) { 
        $remove = remove_file_from_members_dir($pdo, $owner_mbr_id, $oldPoster, $mbr_image_dir);
            $path       = "members/mbr".$owner_mbr_id."/image/";
        if ($remove['success']) {
            $remove = delete_part($pdo, $poster_part_id); 
        } 
        if ($remove['success']) {
            $oldPoster = NULL;
            $poster_part_id = 0;       
        } else {
            $pMsg = $remove['msg'];
            $continue = false;
        }   
    }
    

    //  upload Video and Poster files         
    if (($continue) && (isset($newVid))) {
        if ((isset($oldVid)) && (isset($newVid))) {
            if (!($oldVid == $newVid)) {  
                $remove = remove_file_from_members_dir($pdo, $owner_mbr_id, $oldVid, $mbr_video_dir);
                if ($remove['success']) {
                    $oldVid = NULL;
                }
            }    
        }  
        $vResult = move_to_mbr_directory($vTmp, $mbr_video_dir, $newVid, $vOver); 
        $vMsg    = $vResult['msg'];
        $oldVid  = $vResult['newName'];
        if ($vResult['errLvl'] > 0) {
            $continue = false;
        }
    }
    
    if (($continue) && (isset($newPoster)))  {
        if ((isset($oldPoster)) && (isset($newPoster))) {
            if (!($oldPoster == $newPoster)) {  
                $remove = remove_file_from_members_dir($pdo, $owner_mbr_id, $oldPoster, $mbr_image_dir);
                if ($remove['success']) {
                    $oldPoster = NULL;
                }
            }    
        } 
        $pResult    = move_to_mbr_directory($pTmp, $mbr_image_dir, $newPoster, $pOver); 
        $pMsg       = $pResult['msg'];
        $oldPoster  = $pResult['newName'];
        if ($pResult['errLvl'] > 0) {
            $continue = false;
        }
    }
    
    //  update the object table, update or insert new Parts 
    if ($continue) {
        $editVideoMsg = updateVideo($pdo, $newDtls);   
    }
}           

// end php code -------------------------------------------------------------

require "./code/top_of_page.php"; 

// define main contents of the page 
echo "<!-- begin main content section -->";     
require "./code/editVideo.php";
echo "<!-- end main content section -->";  
// end main contents 

require "./code/bottum_of_page.php";    
?>
<?php
if (!isset($obj)) {
    $msg = "No Object Id was given - Transaction failed";
    exit;
}
$r       = getVidFileNames($pdo, $obj);
$success = $r["success"];
$video   = ($success) ? $r["video"] : ""; 
$poster  = ($success) ? $r["poster"] : "";
$mbr     = ($success) ? $r["owner"] : "";
$msg     = $r["msg"];

$count = 0;
if ($success) { 
    // get rid of all files in the members directory 
    if (deleteVidFiles($pdo,$mbr,$video,$poster)) { // remove the video file and poster file from the members directory  
        $count = deleteVid($pdo, $obj);             // remove the video object from the database 
    }    
    $msg = "$count Video Object was removed from the database"; 
}
?>

<h3>Delete Video</h3>
<h5><?php echo "$msg";?></h5>
<form action="manageVids.php">
    <button class='btn btn-primary'type='submit'>Continue</button>  
</form> 
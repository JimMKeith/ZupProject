<?php
if (!isset($obj)) {
    $msg = "No Object Id was given - Transaction failed";
    echo "<h5>".$msg."</h5>";
    exit; 
}

$r       = getCartoonFileNames($pdo, $obj);
$success = $r["success"];
$files   = ($success) ? $r["files"] : ""; 
$mbr     = ($success) ? $r["owner"] : "";
$msg     = $r["msg"];

$count = 0;
if ($success) { 
    // get rid of the cartoon files in the members directory 
       if (deleteCartoonFiles($pdo,$mbr,$files)) {  // remove cartoon files from the members directory  
           $count = deleteCartoon($pdo, $obj);      // remove the cartoon object from the database 
       }    
    $msg = "$count Cartoon Object was removed from the database"; 
}

echo "<h3>Delete Cartoon</h3>";
echo "<h5>".$msg."</h5>";

$url_to_list = $bm->url; 
?>     
    <p class="text-center"><a class='bg-primary text-white' href=<?php echo "$url_to_list";?>>Back to the List</a></p> 
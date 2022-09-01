<?php
$logged_in = false; 
if ($_SESSION['mbr_id']) {
    $logged_in = true; 
}

if ($logged_in) {  
?>
<p><a href="addComment.php"><button type="button" class="btn btn-success">Add aComment</button></a></p>
<p><hr></p>  
<?php  
}
?>

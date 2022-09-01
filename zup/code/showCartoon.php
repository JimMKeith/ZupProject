<?php
    $url_to_list = $bm->url; 
?>     
    <p class="text-center"><a class='bg-primary text-white' href=<?php echo "$url_to_list";?>>Back to the List</a></p>   
<?php
    $frames = getCartoon($pdo, $obj);
     
    $frameCount = $frames->rowCount();
    switch($frameCount) {
        case 0 :
            echo "<h3>No Cartoons in Scope</h3>";
            break;
        case 1 :
            $row = $frames->fetch();
            $blurb = $row['comic_description'];
            $frame = "members/mbr".$row['owner']."/image/".$row['frame']; 
            echo "<h4 class='text-center'>".$blurb."</h4>";
            echo '<img src="'.$frame.'" class="d-block w-50 mx-auto" style="height:550px;" alt="...">';
            break; 
        default :
            showInCarousel($frameCount, $frames); 
            break; 
    }
    
    $comment_rows = getComments($pdo, $obj);
    if ($comment_rows) {
       echo "<h3>Comments</h3>"; 
   }
   foreach($comment_rows as $c) { 
       $c_id    = $c['c_id']; 
       $o_id    = $c['o_obj']; 
       $made_by = $c['made_by']; 
       $sts     = $c['sts'];  
       $date    = $c['date'];
       $comment = $c['comment'];       
   ?>
   
   <div>    
       <div class="row">
            <div class="col-md-2 col-md-auto offset-md-1 bg-light">
                <?php echo "<span class='text-primary'><br><strong>".$made_by."</strong></span> - ".$date;?>
            </div>
            <div class="col-md-9">
            </div>
       </div>
   </div>
   <div>    
       <div class="row">
            <div class="col-md-9 col-md_auto offset-md-2">
                 <?php echo "$comment";?>
            </div>
            <div class="col-md-1">
            </div>            
       </div>  
   </div> 
   <br>
   
   <?php 
   }
    require "./code/com_but.php";
   ?>

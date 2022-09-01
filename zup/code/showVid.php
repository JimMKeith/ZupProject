<?php
    if (isset($_SESSION['bm'])) {
        $url_to_list = $bm->url;      
        echo '<p class="text-center"><a class="bg-primary text-white" href='.$url_to_list.'>Back to the List</a></p>';
    } else {
        echo '<p></p>';
    }       

   getVid($pdo, $obj);
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

<?php
   require "../zup_php/functions.php"; 
   
   // php code for this page goes here 

   // end php code 
   
   require "./code/top_of_page.php"; 

   // define main contents of the page 
   $obj = 6;
   $previous_obj = 5;
   $record = ['title' => 'Counting', 'description' => 'From One to Five', 'createdBy' => 'Jim', 'lst_updt' => '2020-10-20']; 
   
   $i = 1;
   while ($i < 11) {
      $previous_obj = $i;
      $obj          = ++$i;
      $parms        = "obj_id=$obj"; 
      $parms2       = "&prev_obj=$previous_obj";
      $previous_obj = $obj; 
      $desc         = "<h4>".$record['title']."</h4><p>".$record['description']."  object-".$obj."</p>";
      $rowId        = "rowId".$obj; 
      
      echo "<div class='row p-0' id='".$rowId."' onmouseover='highlight(this)' onmouseout='normal(this)'>";
      echo "<div class='d-flex col-md-2 justify-content-start p-2 align-items-center'>";
            echo "<a class='btn btn-sm btn-dark' onmouseover='m_over(this)' onmouseout='m_out(this)' role='button'  href='showCartoon.php?".$parms."'>View</a>";
            echo "<a class='btn btn-sm btn-dark' onmouseover='m_over(this)' onmouseout='m_out(this)' role='button'  href='editCartoon.php?".$parms."'>Edit</a>";
            echo "<a class='btn btn-sm btn-dark' onmouseover='m_over(this)' onmouseout='m_out(this)' role='button'  href='seqFrames.php?".$parms."'>Re-Seq</a>";
            echo "<a class='btn btn-sm btn-dark' onmouseover='m_over(this)' onmouseout='m_out(this)' role='button'  href='deleteCartoon.php?".$parms.$parms2."'>Delete</a>";
      echo "</div>";  
          
      echo "<div class='col-md-3 p-2'>";
      echo "<h5 onmouseover='bigText()'>".$record['title']."</h5>";
      echo "</div>"; 
        
      echo "<div class='col-md-1 p-2'>";
      echo "<h5>".$record['createdBy']."</h5>";
      echo "</div>"; 
        
      echo "<div class='col-md-5 p-2'>";
      echo "$desc";
      echo "</div>"; 
                
      echo "<div class='col-md-1 p-2'>";
      echo $record['lst_updt'];
      echo "</div>"; 

      echo '</div>';                                                                                                           
   }                                                                                        
                                                     
   // end main contents 
   
   require "./code/bottum_of_page.php";

?>                
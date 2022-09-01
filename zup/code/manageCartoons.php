<?php 
    //   A form, accepts parameters to filter the list 
    require "./code/filterForm.php";        
    
    // get the filtered cartoon rows from the database          
    $cartoonRows = listCartoons_manage($pdo, $filter);
?>
    <div class='row bg-light text-dark d-flex justify-content-center pt-2'>
        <h4>Cartoon ==></h4>
    <a class='btn btn-sm btn-dark' onmouseover='m_over(this)' onmouseout='m_out(this)' role='button'  href='newCartoon.php'>New</a>
    </div>
    <div class='row p-0 bg-light'>

    <div class='col-md-4'><h3>Cartoons</h3></div>
    <div class='col-md-2 pt-2 m-0'><h4>Title</h4></div>
    <div class='col-md-1 pt-2 m-0'><h4>By</h4></div>
    <div class='col-md-4 pt-2 m-0'><h4>Description</h4></div>
    <div class='col-md-1 pt-2 m-0'><h4>Updated</h4></div>
    </div>
    
<?php
    // display the cartoon rows
    displayCartoonRows($cartoonRows);
?>
    <div class='row p-2 border border-primary'>
    <div class='col-md-12 pt-4'></div>
    </div>
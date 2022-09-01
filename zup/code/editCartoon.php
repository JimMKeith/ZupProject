<?php
if (isset($scope)) {    
    // $scope exists and is not NULL, do nothing 
} else  {
    // set to a default value of 1, "private" viewing 
    $scope = 1;
}

if (isset($cartoonMsg)) {
    echo "<h3 class='my_green'>$cartoonMsg</h3>";
}

$cartoonList = $bm->url; 
$_SESSION['bm'] = serialize($bm);    // save bookmark in a session variable  
          
?> 
<p class="text-center"><a class='bg-primary text-white' href=<?php echo "$cartoonList";?>>Back to the List</a></p>   

<form class="form mt-5 p-3 my_green"
    style="border-style: solid;"
    onsubmit='return editCartoon_form_validate()' 
    onreset='return reset_editCartoon_form()'
    action=''
    method='post'
    enctype="multipart/form-data"
    id='editCartoon_form'>
    <fieldset>
        <legend>Edit Cartoon</legend>
        <div class="form-group row">
            <label class="control-label col-sm-2" for="title">Cartoon Title</label>
            <div class="col-sm-10">
                <input class="form-control" id="title" type="text" required name="title"
                    value='<?php if (isset($title)) {echo "$title";}?>'>
            </div> 
        </div>                                                      

        <div class="form-group row">
            <label class="control-label col-sm-2" for="scope">Viewing options:</label>
            <div class="col-sm-2">
                <select class="form-control" id="scope" name="scope" form="editCartoon_form">
                    <?php select_scope_options($pdo, $scope)?>
                </select>    
            </div> 
        </div>
           
        <div class="form-group row">
            <label class="control-label col-sm-2" for="description">Brief Description of the Cartoon</label>
            <div class="col-sm-10">
                <textarea placeholder="Enter a brief description of the Cartoon here ..."
                    class="form-control" id="description" rows="4" cols="80"
                    required name="description">
                    <?php if (isset($description)) {echo "$description";}?>                               
                </textarea>
            </div> 
        </div>
  
    </fieldset>
    <fieldset class="float-right">
        <button class='button btn-primary' type='submit' name='submit' value='Submit'>Submit</button>
        <button class='button btn-primary' type='button' name='seqFrames' value='Sequence'>Sequence</button>
        <button class='button btn-default' type='reset'>Clear</button>
    </fieldset> 

    <?php 
    $obj_id          = (isset($obj_id))         ? $obj_id         : NULL;
    ?>
    <fieldset>
        <input type='hidden' name='obj'            value='<?php echo "$obj";?>'> 
        <input type='hidden' name='obj_type'       value='<?php echo "$obj_type";?>'>
        <input type='hidden' name='lst_updt'       value='<?php echo "$lst_updt";?>'> 
        <input type='hidden' name='lastSeq'        value='<?php echo "$lastSeq";?>'>
        <input type='hidden' name='frameCount'     value='<?php echo "$frameCount";?>'> 
        <input type='hidden' name='mbr_id'         value='<?php echo "$mbr_id";?>'>
        <input type='hidden' name='scope_code'     value='<?php echo "$scope";?>'>
    </fieldset>

</form>
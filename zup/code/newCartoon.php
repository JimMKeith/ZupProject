<?php
if (isset($cScope)) {    
    // $scope exists and is not NULL, do nothing 
} else  {
    // set to a default value of 1, "private" viewing 
    $cScope= 1;
}

if (isset($newCartoonMsg)) {
    echo "<h3 class='my_green'>$newCartoonMsg</h3>";
}    
?>
<!--  value='<?php if (isset($cFile)) {echo "$cFile";}?>'>   -->

<form class="form mt-5 p-3 my_green" style="border-style: solid;"
    onreset='return reset_newCartoon_form()'
    action=''
    method='post'
    enctype="multipart/form-data" id='newCartoon_form'
    name="newCartoon_form">
    <fieldset>
        <legend>Add a New Cartoon</legend>

        <div class="form-group row">
            <label class="control-label col-sm-2" for=
                "CartoonTitle">Cartoon Title</label>

            <div class="col-sm-10">
                <input class="form-control" id="CartoonTitle" type="text"
                    required="" name="cTitle" value=
                    '<?php if (isset($cTitle)) {echo "$cTitle";}?>'>
            </div>
        </div>

        <div class="form-group row">
            <label class="control-label col-sm-2" for=
                "cFileName">Cartoon Frame (max size 5 MB)</label>

            <div class="col-sm-7">
                <input class="form-control" id="cFileName" type="file" required
                    name="cFile" accept=
                    "image/apng, image/jpg, image/jpeg, image/tif, image/tiff, image/bmp, image/png, image/gif, image/jfif, image/webp">
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000">
            </div>

            <div class='col-sm-2 bg-light text-center form-group form-check'>
                <label class="control-label">Allow Overwite
                    <input class="form-control" type="checkbox"
                        name="cOverwrite"
                       <?php if ($cOverwrite) {echo " checked='checked'";}?>>
                </label>         
            </div>
        </div>

        <p class='my_purple' id="cMsg">
        <?php if (isset($cMsg)) {echo "$cMsg";}?></p>

        <div class="form-group row">
            <label class="control-label col-sm-2" for="cScope">Viewing
                options:</label>

            <div class="col-sm-10">
                <select class="form-control" id="cScope" name="cScope"
                    form="newCartoon_form">
                    <?php select_scope_options($pdo, $cScope)?>

                </select>
            </div>
        </div>

        <div class="form-group row">
            <label class="control-label col-sm-2" for=
                "cDescription">Breif Description of the Cartoon</label>

            <div class="col-sm-10">
                <textarea placeholder=
                    "Enter a breif description of the Cartoon here ..."
                    class="form-control" id="cDescription" rows="4" cols="80"
                    required="" name="cDesc">
                    <?php if (isset($cDesc)) {echo "$cDesc";}?>                               

                </textarea>
            </div>
        </div>

    </fieldset>
    
    <?php 
    $obj      = '';
    $disabled = 'disabled';
    $color    = 'btn-secondary';
    if (isset($obj_id)) {
        $obj      = $obj_id;
        $disabled = '';
        $color    = 'btn-primary';
    }
    ?>

    <fieldset class="float-right">
        <button class='button btn-primary' type='submit'
            name='upload' value='Submit'>Up Load</button>
        <button class='button btn-default' 
            type='reset'>Clear</button>
        <button type='button' class='button <?php echo "$color";?>'
            onclick=<?php echo "viewCartoon('".$obj."') $disabled";?>> 
            View The Cartoon</button>        
        <button type='button' class='button <?php echo "$color";?>'
            onclick=<?php echo "newFrame('".$obj."') $disabled";?>>
            Add A Frame</button>            
    </fieldset>
</form>
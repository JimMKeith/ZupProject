<?php
if (isset($addFrameMsg)) {
    echo "<h3 class='my_green'>$addFrameMsg</h3>";
}    
?>
<!--  value='<?php if (isset($cFile)) {echo "$cFile";}?>'>   -->

<?php  echo "<h3>$cTitle</h3>";
       echo "<h4>Object ($obj_id)</h4>";
?>        
<form class="form mt-5 p-3 my_green" style="border-style: solid;"
    onreset='return reset_addFrame_form()'
    action=''
    method='post'
    enctype="multipart/form-data" id='addFrame_form'
    name="addFrame_form">
    <fieldset>
        <legend>Add New Frames to a Cartoon</legend>

        <div class="form-group row">
            <label class="control-label col-sm-2" for="cFileName">Cartoon Frame (max size 5 MB)</label>

            <div class='col-sm-1 bg-light'>
            <label class="control-label">Sequence
                <input class="form-control" type="number"
                    name="seq" max='2000' min='1' step='1'  
                    value=<?php echo "$seq";?>>
            </label>
            </div>
                
            <div class="col-sm-6">
                <input class="form-control" id="cFileName" type="file" required
                    name="cFile" accept=
                    "image/apng, image/jpg, image/jpeg, image/tif, image/tiff, image/bmp, image/png, image/gif, image/jfif, image/webp">
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000">
            </div>

            <div class='col-sm-2 bg-light text-center form-group form-check'>
                <label class="control-label">Allow Overwite
                    <input class="form-control" type="checkbox"
                        name="ovrwrt0"
                       <?php if ($ovrwrt0) {echo " checked='checked'";}?>>
                </label>
            </div>
        </div>

        <p class='my_purple' id="cMsg">
        <?php if (isset($fMsg)) {echo "$fMsg";}?></p>

    </fieldset>

    <?php 
    $obj_id = (isset($obj_id)) ? $obj_id : NULL; 
    $cTitle = (isset($cTitle)) ? $cTitle : 'Un Titled';
    $cMime  = (isset($cMime))  ? $cMime  : NULL;
    $cScope = (isset($cScope)) ? $cScope : NULL;
    ?>
    
    
    <fieldset>
        <input type='hidden' name='obj_id' value='<?php echo "$obj_id";?>'>
        <input type='hidden' name='cTitle' value='<?php echo "$cTitle";?>'> 
        <input type='hidden' name='cMime'  value='<?php echo "$cMime";?>'> 
        <input type='hidden' name='cScope' value='<?php echo "$cScope";?>'>
    </fieldset>
    
    <fieldset class="float-right">
        <button class='button btn-primary' type='submit'
            name='upload' value='Submit'>Up Load</button>
        <button class='button btn-default' 
            type='button' id='seqButton'  disabled onclick='href="#"'>re-sequence</button>     
    </fieldset>
</form>
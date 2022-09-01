<?php
if (isset($vScope)) {    
    // $vScope exists and is not NULL, do nothing 
} else  {
    // set to a default value of 1, "private" viewing 
    $vScope = 1;
}

if (isset($newVideoMsg)) {
    echo "<h3 class='my_green'>$newVideoMsg</h3>";
}    
?> 

<!--  value='<?php if (isset($vfile)) {echo "$vfile";}?>'>   -->

<form class="form mt-5 p-3 my_green"
    style="border-style: solid;"
    onreset='return reset_newVideo_form()'
    action=''
    method='post'
    enctype="multipart/form-data"
    id='newVideo_form'>
    <fieldset>
        <legend>Add a New Video</legend>
        <div class="form-group row">
            <label class="control-label col-sm-2" for="videoTitle">Movie Title</label>
            <div class="col-sm-10">
                <input class="form-control" id="videoTitle" type="text" required name="vTitle"
                    value='<?php if (isset($vTitle)) {echo "$vTitle";}?>'>
            </div> 
        </div>

        <p class='my_purple' id="vMsg"><?php if (isset($vMsg)) {echo "$vMsg";}?></p> 
        <div class="form-group row">

            <label class="control-label col-sm-2" for="vfileName">
                Video file (max 2,800 MB)</label>
            <div class="col-sm-7">
                <input class="form-control" id="vfileName" type="file" required
                    name="vFile" accept="video/mp4, video/webm, video/ogg">
                <input type="hidden" name="MAX_FILE_SIZE" value="280000000" maxlength="250"> 
            </div>
            <div class='col-sm-2 bg-light text-center form-group form-check'>
                <label class="control-label">Allow Overwrite
                    <input class="form-control" type="checkbox" name="vOverwrite"
                        <?php if ($vOverwrite) {echo " checked='checked'";}?>>
                </label>        
            </div>                                                       

        </div> 
        <div class="form-group row">
            <label class="control-label col-sm-2" for="vScope">Viewing options:</label>
            <div class="col-sm-10">
                <select class="form-control" id="vScope" name="vScope" form="newVideo_form">
                    <?php select_scope_options($pdo, $vScope)?>
                </select>    
            </div> 
        </div>   
        <div class="form-group row">
            <label class="control-label col-sm-2" for="vDescription">Breif Description of the Video</label>
            <div class="col-sm-10">
                <textarea placeholder="Enter a breif description of the video here ..."
                    class="form-control" id="vDescription" rows="4" cols="80"
                    required name="vDesc">
                    <?php if (isset($vDesc)) {echo "$vDesc";}?>                               
                </textarea>
            </div> 
        </div>

        <p class='my_purple' id="pMsg"><?php if (isset($pMsg)) {echo "$pMsg";}?></p> 
        <div class="form-group row">
            <label class="control-label col-sm-2" for="pfileName">
                Poster for the video (max size 5 MB)</label>
            <div class="col-sm-7">
                <input class="form-control" id="pfileName" type="file" 
                    name="pFile" accept="image/apng, image/jpg, image/jpeg, image/tif, image/tiff, image/bmp, image/png, image/gif, image/jfif, image/webp">
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000"> 
            </div>
            <div class='col-sm-2 bg-light text-center form-group form-check'>
                <label class="control-label">Allow overwrite
                    <input class="form-control" type="checkbox" name="pOverwrite"
                        <?php if ($pOverwrite) {echo " checked='checked'";}?>>
                </label>   
            </div> 
        </div>   
    </fieldset>
    <?php 
    $obj      = (isset($obj_id)) ? $obj_id : '';
    $disabled = 'disabled';
    $color    = 'btn-secondary';
    if (isset($obj_id)) {
        $disabled = '';
        $color    = 'btn-primary';
    }
    ?>
    <fieldset class="float-right">
        <button class='button btn-primary' type='submit' name='upload' value='Submit'>Up Load</button>
        <button class='button btn-primary' type='reset'>Clear</button>
        <input type='button'  class='button <?php echo "$color";?>' value='View Video'
               onclick=<?php echo "manVidView($obj) $disabled";?>> 
    </fieldset>     
</form>


<?php
if (isset($scope)) {    
    // $scope exists and is not NULL, do nothing 
} else  {
    // set to a default value of 1, "private" viewing 
    $scope = 1;
}

if (isset($editVideoMsg)) {
    echo "<h3 class='my_green'>$editVideoMsg</h3>";
} 

$pO   = (isset($pOver))   ? $pOver   : false;   
$vO   = (isset($vOver))   ? $vOver   : false;    
$pDlt = (isset($pDelete)) ? $pDelete : false;  

if ($pO == 'on')   {$pO = true;}    
if ($vO == 'on')   {$vO = true;}     
if ($pDlt == 'on') {$pDelete = true;}          
?> 

<form class="form mt-5 p-3 my_green"
    style="border-style: solid;"
    onsubmit='return editVideo_form_validate()' 
    onreset='return reset_editVideo_form()'
    action=''
    method='post'
    enctype="multipart/form-data"
    id='editVideo_form'>
    <fieldset>
        <legend>Edit Video</legend>
        <div class="form-group row">
            <label class="control-label col-sm-2" for="title">Movie Title</label>
            <div class="col-sm-10">
                <input class="form-control" id="title" type="text" required name="title"
                    value='<?php if (isset($title)) {echo "$title";}?>'>
            </div> 
        </div>

        <p class='my_purple' id="vMsg"><?php if (isset($vMsg)) {echo "$vMsg";}?></p> 
        <div class="form-group row">

            <label class="control-label col-sm-2" for="newVid">
                Video file (max 2,800 MB)</label>
            <div class="col-sm-7">
                <input class="form-control" id="newVid" type="file" 
                    name="newVid" accept="video/mp4, video/webm, video/ogg">
                <input type="hidden" name="MAX_FILE_SIZE" value="280000000" maxlength="250"> 
            </div>
            <div class='col-sm-2 bg-light'>
                <label class="control-label">Allow Overwrite
                    <input class="form-control" type="checkbox" name="vOver"
                        <?php if ($vO) {echo " checked='checked'";}?>>
                </label>        
            </div>                                                        

        </div> 
        <div class="form-group row">
            <label class="control-label col-sm-2" for="scope">Viewing options:</label>
            <div class="col-sm-2">
                <select class="form-control" id="scope" name="scope" form="editVideo_form">
                    <?php select_scope_options($pdo, $scope)?>
                </select>    
            </div> 
        </div>   
        <div class="form-group row">
            <label class="control-label col-sm-2" for="description">Brief Description of the Video</label>
            <div class="col-sm-10">
                <textarea placeholder="Enter a brief description of the video here ..."
                    class="form-control" id="description" rows="4" cols="80"
                    required name="description">
                    <?php if (isset($description)) {echo "$description";}?>                               
                </textarea>
            </div> 
        </div>

        <p class='my_purple' id="pMsg"><?php if (isset($pMsg)) {echo "$pMsg";}?></p> 
        <div class="form-group row">
            <div class='col-sm-2'>
                Check to delete the current Poster
            </div>
            <div class="col-sm-1 custom-control custom-checkbox custom-control-inline bg-light ml-3">
                <label class="control-label">Delete
                    <input class="form-control" type="checkbox" name="pDelete" id="pDelete"
                        <?php if ($pDlt) {echo " checked='checked'";}?>> 
                </label>
            </div>         
        </div>
        <div class="form-group row">
            <label class="control-label col-sm-2" for="newPoster">
                Add or Replace the Poster (max size 5 MB)</label>
            <div class="col-sm-7">
                <input class="form-control" id="newPoster" type="file" 
                    name="newPoster" accept="image/apng, image/jpg, image/jpeg, image/tif, image/tiff, image/bmp, image/png, image/gif, image/jfif, image/webp">
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000"> 
            </div>
            <div class='col-sm-2 bg-light'>
                <label class="control-label">Allow Overwrite
                    <input class="form-control" type="checkbox" name="pOver"
                        <?php if ($pO) {echo " checked='checked'";}?>>
                </label>   
            </div> 
        </div>   
    </fieldset>
    <fieldset class="float-right">
        <button class='button btn-primary' type='submit' name='upload'>Submit</button>
        <button class='button btn-default' type='reset'>Clear</button>
    </fieldset> 

    <?php 
    $obj_id          = (isset($obj_id))         ? $obj_id         : NULL;
    $video_part_id   = (isset($video_part_id))  ? $video_part_id  : NULL;
    $poster_part_id  = (isset($poster_part_id)) ? $poster_part_id : NULL;
    $owner_mbr_id    = (isset($owner_mbr_id))   ? $owner_mbr_id   : NULL;
    $oldVid          = (isset($oldVid))         ? $oldVid         : NULL;
    $oldPoster       = (isset($oldPoster))      ? $oldPoster      : NULL;
    ?>
    <fieldset>
        <input type='hidden' name='obj_id'         value='<?php echo "$obj_id";?>'> 
        <input type='hidden' name='video_part_id'  value='<?php echo "$video_part_id";?>'>  
        <input type='hidden' name='poster_part_id' value='<?php echo "$poster_part_id";?>'> 
        <input type='hidden' name='owner_mbr_id'   value='<?php echo "$owner_mbr_id";?>'>     
        <input type='hidden' name='oldVid'         value='<?php echo "$oldVid";?>'>
        <input type='hidden' name='oldPoster'      value='<?php echo "$oldPoster";?>'>
    </fieldset>

</form>


<?php     
    $newSeq = (isset($newSeq)) ? $newSeq : 0; 
    $title  = (isset($title))  ? $title  : 'Empty Cartoon'; 
    if ($partsList == '') {$partsList = '0';}
    if (isset($topMsg)) {
        $topMsg = ($topMsg == '') ? NULL : $topMsg; 
    }

    $url_to_list = $bm->url; 
?>     
    <p class="text-center"><a class='bg-primary text-white' href=<?php echo "$url_to_list";?>>Back to the List</a></p>   

<form class="form mt-5 p-3 my_green"
    style="border-style: solid;"
    onsubmit='return seqFrames_form_validate(this)' 
    onreset='return reset_seqFrames_form()'
    action=''
    method='post'
    enctype="multipart/form-data"
    id='seqFrames_form'>
    <h3 class='text-center'><?php echo "$title";?></h3> 
    <h4 class="text-center">Manage Cartoon Frames</h4>
    <h5 class="text-center">(Maximum allowed frame size 5 mb)</h5>
    <?php if (isset($topMsg)) {echo "$topMsg";}?>
    <div id='errMsg'></div>
    
    <fieldset class='border border-primary'> 
        <legend>Add a Frame</legend>
            <div class="form-group row align-items-center" id="rowId0">             
                
                <div class="col-sm-3"> 
                </div>
                  
                <div class="col-sm-1 bg-light">
                    <label class="control-label" for="seq0">Sequence</label>
                    <input class="form-control my-red" type="number"
                           id="seq0" name="seq0" max="2000" min="0" step="1"
                           value="<?php echo "$newSeq";?>">                         
                </div>
                
                <div class="col-sm-4">
                    <input class="form-control" id="frame0" type="file" name="frame0" 
                        accept="image/apng, image/jpg, image/jpeg, image/tif, image/tiff, image/bmp,                    image/png, imagegif, image/jfif, image/webp"> 
                    <input type="hidden" name="MAX_FILE_SIZE" value="5000000"> 
                </div>
                 
                <div class="col-sm-1 bg-light text-center form-group form-check pb-2">
                    <label class="control-label" for="ovrwrt0">Over Write</label>
                    <input class="form-control" id="ovrwrt0" type="checkbox" name="ovrwrt0"    
                <?php         
                if (isset($checked['0'])) {
                    echo ' checked="checked"'; 
                }
                ?>
                > 
                </div>
            </div>    
    </fieldset>
    
    <fieldset class='border border-primary'>
        <legend>Re-Sequence -- Update Frames</legend>
        <div class="row align-items-center"> 
            <div>
                <h5 class='col-sm-12 bg-warning text-dark' >Use Sequence "0" to DELETE a frame</h5>
            </div> 
        </div>   
    <?php buildSeqFramesForm($frames, $checked); ?>                                                 
    </fieldset>
 
    <fieldset>
        <input type='hidden' name='partsList'  value='<?php echo "$partsList";?>'>
        <input type='hidden' name='obj_id'     value='<?php echo "$obj_id";?>'>
        <input type='hidden' name='owner'      value='<?php echo "$owner";?>'>
    </fieldset>
    
    <fieldset class="float-right">
        <button class='button btn-primary' type='submit' name='submit' value='Submit'>Submit</button>
        <button class='button btn-default' type='reset'>Clear</button>
    </fieldset> 
</form>
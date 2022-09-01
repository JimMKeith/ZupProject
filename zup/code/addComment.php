<br>
<h2 class="text-center">Add a Comment to <?php echo "$title by $user_id";?></h2>
<h3 class="text-center"><?php echo "($type_desc - $obj_ymd)";?></h3>

<div class='row justify-content-center'>
<div class='col-sm-10'>
<form class="form-horizontal mt-3 my_green" 
             style="border-style: solid;"
             action='addComment.php'
             method='post'
             id='comment_form'>
    <fieldset>
        <div class='form-group row mt-3 justify-content-center'>
            <div class="col">
                 <label for="comments"><h3>Your Comments</h3></label>
                 <textarea id='comments' name='comment' rows='18' cols='100'>
                 </textarea>
            </div>                 
        </div> 
    </fieldset> 
    
    <fieldset>
        <input type='hidden' name='obj'       value='<?php echo "$obj";?>'>
        <input type='hidden' name='user_id'   value='<?php echo "$user_id";?>'>
        <input type='hidden' name='title'     value='<?php echo "$title";?>'> 
        <input type='hidden' name='type_desc' value='<?php echo "$type_desc";?>'> 
        <input type='hidden' name='obj_ymd'   value='<?php echo "$obj_ymd";?>'>
        <input type='hidden' name='now'       value='<?php echo "$now";?>'>
    </fieldset>  
        
    <fieldset class='float-right'>
        <button class='button btn-primary' type='submit' name='Submit' value='Submit'>Submit</button>
        <button class='button btn-default' type='reset'>Clear</button>
    </fieldset>
</form>
</div>
</div>
<h3 class="text-center">Update Members Profile</h3>
<h4 class="text-center">User: <?php echo "$user_id";?> &nbsp
  (member_id: <?php echo "$mbr_id";?>) &nbsp
  Signed up <?php echo "$signup_dt";?></h4>
<p class="text-center">Last Updated: <?php echo "$lst_updt";?></p>  

<h5><?php if (isset($editProfileMsg)) {echo "$editProfileMsg";}?></h5>
<form class="form-horizontal mt-5 p-3 my_green" 
             style="border-style: solid;"
             action='editProfile.php'
             method='post'
             id='editProfile_form'>
    <fieldset>
        <legend>Profile</legend>
        <div class='form-group row'>
            <label class="control-label col-sm-2" for="mbr_name">Name :</label>
            <div class="col-sm-10">
                <input class="form-control" id ="mbr_name" name='mbr_name'
                    type='text' required='required'
                    value="<?php echo "$mbr_name";?>">
            </div>         
        </div> 
        <div class='form-group row'>   
            <label class="control-label col-sm-2" for="email">Email :</label>
            <div class="col-sm-10">
                <input class='form-control' name='email' id="email"
                    type='email' required='required'
                    value="<?php echo "$email";?>">
            </div>        
        </div>                                                                         
    </fieldset>
    
    <fieldset>
        <input type='hidden' name='user_id'        value='<?php echo "$user_id";?>'>  
        <input type='hidden' name='signup_dt'      value='<?php echo "$signup_dt";?>'> 
        <input type='hidden' name='lst_updt'       value='<?php echo "$lst_updt";?>'>     
    </fieldset> 
        
    <fieldset class='float-right'>
        <button class='button btn-primary' type='submit' name='editprof_submit' value='Submit'>Submit</button>
        <button class='button btn-default' type='reset'>Clear</button>
    </fieldset>
</form>

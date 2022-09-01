<h3 class="text-center">Change Your Password</h3>
<h4 class="text-center"><?php echo "$name - User Id: $user_id";?></h4> 

<div class='row justify-content-center'> 
<div class="col-sm-8">
    <h5><?php if (isset($chgPwdMsg)) {echo "$chgPwdMsg";}?></h5>
    <form class="form-horizontal mt-5 p-3 my_green" 
                 onsubmit='return signUp_form_validate()'
                 style="border-style: solid;"
                 method='post'
                 id='chgPwd_form'>
        <fieldset>
            <legend>Password</legend>
            <div class='form-group row'>
                <label class="control-label col-sm-4" for="pw1">Enter New Password :</label>
                <div class="col-sm-4">
                    <input id='pw1' class='form-control' name='password1'
                        type='password' required='required'
                        value="<?php echo "$password1";?>">
                </div>                 
            </div> 
            <div class='form-group row'>
                <label class="control-label col-sm-4" for="pw2">Re-enter Password :</label>
                <div class="col-sm-4">
                    <input id='pw2' class='form-control' name='password2'
                        type='password' required='required'
                        value="<?php echo "$password2";?>">
                </div>                 
            </div> 
            
        </fieldset>    
            
        <fieldset>
            <input type='hidden' name='mbr_id'         value='<?php echo "$mbr_id";?>'> 
            <input type='hidden' name='user_id'        value='<?php echo "$user_id";?>'>     
        </fieldset> 
           
            
        <fieldset class='float-right'>
            <button class='button btn-primary' type='submit' name='chgPwd_submit' value='Submit'>Submit</button>
            <button class='button btn-default' type='reset'>Clear</button>
        </fieldset>
    </form>
</div>
</div>


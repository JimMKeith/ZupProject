<h3 class="text-center">Forgot Password ?</h3>

<h5><?php if (isset($forgotPwdMsg)) {echo "$forgotPwdMsg";}?></h5>
<div class='row justify-content-center'>
<div class='col-sm-5'>
<form class="form-horizontal mt-5 p-3 my_green" 
             style="border-style: solid;"
             action='forgotPwd.php'
             method='post'
             id='fPwd_form'>
    <fieldset>
        <legend>User Id</legend>
        <div class='form-group row justify-content-center'>
        <!--    <label class="control-label col-sm-1" for="user">Your ID</label> -->
            <div class="col-sm-4">
                <input id='user' class='form-control' name='user_id'
                    type='text' required='required'
                    value="<?php echo "$user_id";?>">
            </div>                 
        </div> 
    </fieldset>    
        
    <fieldset class='float-right'>
        <button class='button btn-primary' type='submit' name='forgotPwd_submit' value='Submit'>Submit</button>
        <button class='button btn-default' type='reset'>Clear</button>
    </fieldset>
</form>
</div>
</div>
<h3>Enter your User ID above</h3>
<p>An email will be sent to the address we on file for the User Id. To reset your password follow the instructions in the email. Click on the embedded link. Your browser will open and a form will be presented allowing you to enter a new password. This link will be availble no more than 1 day after it has been sent to you. When it expires you will be required to return to this screen and re-do the process.</p>
<p>If you do not see an email in your in box look in the Junk Mail, or Spam folders. Failing that , Go to "Help->Contact Us" in the menu bar at the top and request assistance.</p>


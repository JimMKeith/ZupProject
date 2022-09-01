<h3 class="text-center">Forgot User Id ?</h3>

<h5><?php if (isset($forgotUidMsg)) {echo "$forgotUidMsg";}?></h5>
<div class='row justify-content-center'>
<div class='col-sm-7'>
<form class="form-horizontal mt-5 p-3 my_green" 
             style="border-style: solid;"
             action='forgotUid.php'
             method='post'
             id='fUid_form'>
    <fieldset>
        <legend>User Id</legend>
        <div class='form-group row justify-content-center'>
        <!--    <label class="control-label col-sm-1" for="user">Your ID</label> -->
            <div class="col-sm-7">
                <input id='email' class='form-control' name='email'
                    type='email' required='required'
                    value="<?php if (isset($email)) {echo "$email";}?>">
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
<h3>Enter your User Email above</h3>
<p>An email containing the User Ids associate with that email address will be sent email address entered above. If you do not receive an email please check your spam and/or junk folders. If nothig is found then either the email address entered was incorrect of there was no accounts associated with that address.</p>


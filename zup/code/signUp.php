<h3>Become a Member</h3>
<form class="form-horizontal mt-5 p-3 my_green" 
             style="border-style: solid;"
             onsubmit='return signUp_form_validate()'
             action='signUp.php'
             method='get'
             id='signUp_form'>
    <fieldset>
        <legend>Sign Up</legend>
        <div class='form-group row'>
            <label class="control-label col-sm-2" for="yourName">Your Name :</label>
            <div class="col-sm-10">
                <input class="form-control" id ="yourName" name='name'
                    type='text' required='required'
                    value="<?php echo "$name";?>">
            </div>         
        </div> 
        <div class='form-group row'>   
            <label class="control-label col-sm-2" for="Email">Email :</label>
            <div class="col-sm-10">
                <input class='form-control' name='email' id="Email"
                    type='email' required='required'
                    value="<?php echo "$email";?>">
            </div>        

        </div> 
        <?php if (isset($dupUserIdMsg)) {echo "<p class='my_red'>".$dupUserIdMsg."</p>";}?>                                                                         
        <div class='form-group row'>   
            <label  class="control-label col-sm-2" for="userId">User Id :</label>
            <div class="col-sm-10">       
            <input class='form-control' name='userid' id="userId"
                type='text' maxlength='25' required='required'
                value="<?php echo "$userid";?>">  
            </div>                     
        </div> 
        <div class='form-group row'>      
            <label class="control-label col-sm-2" for="password1">Password :</label>
            <div class="col-sm-10">
                <input id='password1' class='form-control' name='password1'
                    type='password' required='required'
                    value="<?php echo "$password1";?>">
            </div>         

        </div> 
        <div class='form-group row'>  
            <label class="control-label col-sm-2" for="password2">Re-enter Password :</label>
            <div class="col-sm-10">
                <input id='password2' class='form-control' name='password2'
                    type='password'
                    value="<?php echo "$password2";?>">
            </div>         
        </div>  
    </fieldset>    
    <fieldset>
        <input type='hidden' name='signUp'  value='true'> 
    </fieldset>
    <fieldset class='float-right'>
        <button class='button btn-primary' type='submit' name='signIn' value='Submit'>Submit</button>
        <button class='button btn-default' type='reset'>Clear</button>
    </fieldset>
</form>
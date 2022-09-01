
<form class="form-horizontal mt-5 p-3 my_green" 
             style="border-style: solid;"
             action=''
             method='post'
             id='signIn_form'>
    <fieldset>
        <legend>Sign In</legend>
        <div class='form-group'>  
            <label for='si_userid'>User Id :</label>
            <input class='form-control'
                 id='si_userid'
                 name='userid'
                 type='text'
                 maxlength='25'
                 required='required'>

        </div> 
        <div class='form-group'>      
            <label for='si_password'>Password: </label> 
            <input class='form-control'
                id='si_password'
                name='password' 
                type='password' 
                required='required'>

        </div>      
    </fieldset>
    <fieldset class='float-right'>
        <button class='button btn-primary' type='submit' name='signIn' value='SignIn'>Submit</button>
    </fieldset>    
 <!--   <h5 class="my_red"><?php if (isset($signInErr)) {echo $signInErr;} ?></h5>   -->
</form>
  

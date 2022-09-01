<h3 class="text-center">Update Members Record</h3>
<h4 class="text-center">User: <?php echo "$user_id";?> &nbsp
  (member_id: <?php echo "$mbr_id";?>) &nbsp
  Signed up <?php echo "$signup_dt";?></h4>
<p class="text-center">Last Updated: <?php echo "$lst_updt";?></p> 

<p class='text-center'><button type='button' class='button btn-primary' onclick='manMembers("<?php echo "$mbr_id";?>")'>Return to Members List</button></p>";

<h5><?php if (isset($editMemberMsg)) {echo "$editMemberMsg";}?></h5>
<form class="form-horizontal mt-5 p-3 my_green" 
             onsubmit='return signUp_form_validate()'
             style="border-style: solid;"
             action='editMember.php'
             method='post'
             id='editMember_form'>
    <fieldset>
        <legend>Member</legend>
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
        <div class='form-group row'>  
            <label  class="control-label col-sm-2" for="type">User Type :</label>
            <div class="col-sm-4"> 
                <select class="form-control" id="type" name="mbr_type" form="editMember_form">
                    <?php selectMbrType($pdo, $mbr_type)?>
                </select>            
            </div>  
            <label  class="control-label col-sm-2" for="sts">User Status :</label>
            <div class="col-sm-4"> 
                <select class="form-control" id="sts" name="mbr_sts" form="editMember_form">
                    <?php selectMbrSts($pdo, $mbr_sts)?>
                </select>            
            </div>                     
        </div>  
        <div class='form-group row'>  
            <label  class="control-label col-sm-2" for="pw1">New Password :</label>
            <div class="col-sm-4">
                <input class="form-control" id ="password1" name='password1'
                    type='password'
                    value="<?php if (isset($password1)) {echo "$password1";}?>">
            </div>  
            <label  class="control-label col-sm-2" for="pw2">Re-enter Password :</label>
            <div class="col-sm-4">
                <input class="form-control" id ="password2" name='password2'
                    type='password'
                    value="<?php if (isset($password2)) {echo "$password2";}?>">
            </div>                     
        </div>
             
    </fieldset>    
        
    <fieldset>
        <input type='hidden' name='mbr_id'         value='<?php echo "$mbr_id";?>'> 
        <input type='hidden' name='user_id'        value='<?php echo "$user_id";?>'>  
        <input type='hidden' name='signup_dt'      value='<?php echo "$signup_dt";?>'> 
        <input type='hidden' name='lst_updt'       value='<?php echo "$lst_updt";?>'>     
    </fieldset> 
       
        
    <fieldset class='float-right'>
        <button class='button btn-primary' type='submit' name='editMbr_submit' value='Submit'>Submit</button>
        <button class='button btn-default' type='reset'>Clear</button>
    </fieldset>
</form>

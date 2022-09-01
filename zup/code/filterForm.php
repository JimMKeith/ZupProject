    <?php  
        $obj_type         = (isset($obj_type))         ? $obj_type         : NULL;  
        
        // determine if filter selections checkboxes can be displayed      
        $allow_hidden     = (isset($allow_hidden))     ? $allow_hidden     : FALSE; 
        $allow_private    = (isset($allow_private))    ? $allow_private    : FALSE; 
        $allow_membership = (isset($allow_membership)) ? $allow_membership : FALSE; 
        $allow_public     = (isset($allow_public))     ? $allow_public     : FALSE; 
        
        //  set filter selections off if the corresponding checkbox is not displayed
        if (!$allow_hidden)     {$hidden     = false;};
        if (!$allow_private)    {$private    = false;};
        if (!$allow_membership) {$membership = false;};
        if (!$allow_public)     {$public     = false;};
        
        // if the current user is not signed in then "public" should not be  
        // turnned OFF. That would mean nothing would ever get selected
        // Guests can ONLY view objects scoped in the "public" realm.
        if ($query_type == 'browse')  {
             $public = (isset($_session['mbr_id'])) ? $allow_public : true;     
        }

        // The user_id may be used to filter the selection of video or cartoon objects. 
        // The excpetion being when those objects are filtered for update ($query_type = 'manage') 
        // Only the creator of the object and/or the administrator is allowed to update the object.  
        $selectable = true;     // include a slect box for user id in the filter
        $admin_user = false;    
        $signed_in  = (isset($_SESSION['mbr_type'])) ? true : false;      
        if ($signed_in) {    
            $admin_user = (($_SESSION['mbr_type'] == 'a')) ? true : false;  
        }
        if (isset($query_type)) {
            if (($query_type == 'update') && (!$admin_user)) {
                $selectable = false;    // only admins can "update" (ie manage) another users objects
                                        // do not include a selection box for user id in the filter
            } 
        } 
        // public is alway allowed. It may be filtered out but is alwats allowed 
    ?> 
    
    <div class='container-fluid bg-light m-0 p-0'>
    <div class='row'>
    <div class='col-sm-12  m-0 p-0'>
    <?php $display = '';?> 
    <div class='card' id='showFilter' <?php echo "$display";?>>
         <form class='form-inline'
                style="border-style: solid;"
                action=''
                method='post'
                id='filterForm'>
             <div class='form-group col-sm-12'>
                <label for='usr'>Filter list<br>by User Id ==>&nbsp;</label>
                <?php 
                   if ($selectable) {    
                        select_contributing_user_ids($pdo, $obj_type, $bm->filter);
                    } else {
                        echo '<input class="form-control" id="usr" name="uid" readonly value='.$uid.'>';
                    }
                ?>  
             </div>           
             <div class="checkbox col-sm-2">
                 <label <?php if (!$allow_hidden) {echo "style='display: none'";}?>>Hidden =>&nbsp
                     <input type="checkbox" name="hidden" id="noneChk"
                     <?php if ($hidden) {echo " checked='checked'";}?>> 
                 </label> 
             </div>
             <div class="checkbox col-sm-2">
                 <label <?php if (!$allow_private) {echo "style='display: none'";}?>>Private =>&nbsp
                     <input type="checkbox" name="private" id="prvChk"
                     <?php if ($private) {echo " checked='checked'";}?>> 
                 </label> 
             </div>
             <div class="checkbox col-sm-2">
                 <label <?php if (!$allow_membership) {echo "style='display: none'";}?>>Membership =>&nbsp
                     <input type="checkbox" name="membership" id="mbrChk"
                     <?php if ($membership) {echo " checked='checked'";}?>> 
                 </label> 
             </div>
             <div class="checkbox col-sm-2">
                 <label <?php if (!$allow_public) {echo "style='display: none'";}?>>Public =>&nbsp 
                    <input type="checkbox" name="public" id="pubChk"
                    <?php if ($public) {echo " checked='checked'";}?>>
                 </label> 
             </div>
             <div class='col-sm-2'>
             </div>
             <!--
             <button class="button btn-primary col-sm-2 pr-0"
                    type="submit" name="page_filter">Apply Filter
             </button>  --> 
             <button class='btn btn-sm btn-dark col-sm-2 pr-0' type='submit'
                     onmouseover='m_over(this)' onmouseout='m_out(this)'>Apply Filter
             </button> 
         </form> 
    </div> 
    </div>
    </div>
    </div>
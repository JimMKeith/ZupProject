<?php 
require_once "../zup_php/functions.php"; 
$authority = setAuthority();
?>                                                                        

<div class="container-fluid">
    <div class='row'> 
        <!-- Home -->
        <button type="button"
            class="btn btn-primary" onclick="return authLvlChk('<?php echo "$authority";?>','home')">Home</button>
        <nav class="navbar navbar-expand-sm mx-0 my-0 px-0 py-0">  
            <ul class="navbar-nav nav nav-tabs">

                <!-- Browse -->
                <li class="nav-item">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" 
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>Browse
                        </button>
                        <div class="dropdown-menu"> 
                            <a class="dropdown-item" onclick="authLvlChk('<?php echo "$authority";?>','browseVideo')" href="#">Videos</a>
                            <a class="dropdown-item" onclick="authLvlChk('<?php echo "$authority";?>','cartoons')" href="#">Cartoons</a>
                        </div>  
                    </div>
                </li>

                <!--  Manage  -->    
                <li class="nav-item">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" 
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>Manage
                        </button>
                        <div class="dropdown-menu"> 
                            <a class="dropdown-item" onclick="authLvlChk('<?php echo "$authority";?>','manageVideo')" href="#">Videos</a>  
                            <a class="dropdown-item" onclick="authLvlChk('<?php echo "$authority";?>','manageCartoons')" href="#">Cartoons</a>   
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" onclick="authLvlChk('<?php echo "$authority";?>','addVideo')" href="#">Add new Video </a>
                            <a class="dropdown-item" onclick="authLvlChk('<?php echo "$authority";?>','addCartoon')" href="#">Add new Cartoon </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" onclick="authLvlChk('<?php echo "$authority";?>','manMembers')" href="#">Members </a>
                        
                        </div>  
                    </div>
                </li>

                <!--  Link to Oil Paintings -->     
                <li class="nav-item">
                    <button type="button"
                        class="btn btn-primary"
                        onclick="return authLvlChk('<?php echo "$authority";?>','Oil')">Oily Paintings</button>
                </li>

                <!--  Forum  -->   
                <li class="nav-item">
                    <button type="button"
                        class="btn btn-primary" onclick="return authLvlChk('<?php echo "$authority";?>','forum')">Forum</button>
                </li> 
                
                <!--  Sign Up -->   
                <li class="nav-item">
                    <button type="button"
                        class="btn btn-primary" onclick="return authLvlChk('<?php echo "$authority";?>','signUp')">Join Zup</button>
                </li> 
                
                <!--  Account -->   
                <li class="nav-item">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" 
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>Account
                        </button>
                        <div class="dropdown-menu"> 
                            <a class="dropdown-item" onclick="authLvlChk('<?php echo "$authority";?>','editProfile')" href="#">      Member Profile</a>
                            <a class="dropdown-item" onclick="authLvlChk('<?php echo "$authority";?>','chgPwd')" href="#">           Change Password</a>
                            <a class="dropdown-item" onclick="authLvlChk('<?php echo "$authority";?>','forgotPwd')" href="#">        Forgot Password</a>
                            <a class="dropdown-item" onclick="authLvlChk('<?php echo "$authority";?>','forgotUid')" href="#">        Forgot User ID</a>
                        </div>  
                    </div>
                </li>

                <!--  Help  -->    
                <li class="nav-item">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" 
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>Help
                        </button>
                        <div class="dropdown-menu"> 
                            <a class="dropdown-item" onclick="authLvlChk('<?php echo "$authority";?>','contact')" href="#">Contact Us</a>
                            <a class="dropdown-item" onclick="authLvlChk('<?php echo "$authority";?>','about')" href="#">About</a>
                        </div>  
                    </div>
                </li>                                                                                            
            </ul>
        </nav>
    </div>
</div>
<script src="./js/project/navigate.js"></script>

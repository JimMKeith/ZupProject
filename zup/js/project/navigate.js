  
  function authLvlChk(authority,pageId) {             
      
     var pageTable = {};
     pageTable = new Object();
     pageTable.home           = new Object({"security":0,"external":"n","path":"../index.php"});
     pageTable.browseVideo    = new Object({"security":0,"external":"n","path":"../browseVideos.php"});     
     pageTable.cartoons       = new Object({"security":0,"external":"n","path":"../browseCartoons.php"});
     pageTable.addVideo       = new Object({"security":2,"external":"n","path":"../newVideo.php"});
     pageTable.addCartoon     = new Object({"security":2,"external":"n","path":"../newCartoon.php"});
     pageTable.manageVideo    = new Object({"security":2,"external":"n","path":"../manageVids.php"});
     pageTable.manageCartoons = new Object({"security":2,"external":"n","path":"../manageCartoons.php"});
     pageTable.editProfile    = new Object({"security":1,"external":"n","path":"../editProfile.php"});
     pageTable.forum          = new Object({"security":1,"external":"y","path":"../phpBB3/index.php"});
     pageTable.signUp         = new Object({"security":0,"external":"n","path":"../signUp.php"});
     pageTable.chgPwd         = new Object({"security":1,"external":"n","path":"../chgPwd.php"});
     pageTable.forgotPwd      = new Object({"security":0,"external":"n","path":"../forgotPwd.php"});
     pageTable.forgotUid      = new Object({"security":0,"external":"n","path":"../forgotUid.php"});
     pageTable.about          = new Object({"security":0,"external":"n","path":"../about.php"});
     pageTable.contact        = new Object({"security":0,"external":"n","path":"../contact.php"});
     pageTable.manMembers     = new Object({"security":4,"external":"n","path":"../manageMembers.php"});
     pageTable.logoff         = new Object({"security":0,"external":"n","path":"../index.php?log=off"});
     pageTable.Oil            = new Object({"security":0,"external":"y","path":"https://www.youtube.com/watch?v=TfSRjReU014&list=PLGbquye4FqAj9IboIgUfBc_4xr9oFpQ8V"});
     
        
     if (!(pageId in pageTable)) {
         alert("The requested page (" + pageId + ") not found in the pageTable");
         location.reload(true); 
         return; 
     }

     var destination   = pageTable[pageId].path;
     var securityLevel = pageTable[pageId].security; 
     var external_link = pageTable[pageId].external;
     
     if (authority < securityLevel) { 
         alert("You do not have the authority to view the " + pageId  + " page " +
           "Please sign in to an account with the appropiate authority " +
           "Your current authoity level is " + authority);  
         location.reload(true);   
     }
     else 
     {
        if (external_link == 'y') {
           // open new page a under a new tab, leaving current page in place   
           window.open(destination); 
        }
        else
        { 
           // open new page, replacing curreent page with new page  
           location.assign(destination);
        } 
     }
     return;
  } 
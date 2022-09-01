 function m_over(x) {
    x.classList.remove("btn-dark", "btn-sm");
    x.classList.add("btn-success", "btn-lg");     
 }
 
 function m_out(x) {
    x.classList.remove("btn-success", "btn-lg");
    x.classList.add("btn-dark", "btn-sm");      
 }
 
 function verifyDelete(item) {
     return window.confirm("Delete " + item + " ???"); 
 }


function logout()
{
    return;
}

function signUp_form_validate() 
{   var ok = true;

    p1 = document.getElementById("password1");
    p2 = document.getElementById("password2");

    if (p1.value != p2.value) {
        ok = false;
        alert("Passwords do not match, Please try again");
    }
    return ok;
}

function  reset_newVideo_form() {
    /* Clear the error messages */

    vTxt = document.getElementById("vMsg");
    vTxt.innerHTML = '';

    pTxt = document.getElementById("pMsg");
    pTxt.innerHTML = '';

    return true;
}

function  reset_newCartoon_form() {
    /* Clear the error messages */

    cTxt = document.getElementById("cMsg");
    cTxt.innerHTML = '';

    return true;
}

function viewCartoon(obj_id) {
    location.assign("showCartoon.php?obj_id="+obj_id);
}

function viewVid(obj_id) {
    location.assign("showVid.php?obj_id="+obj_id);
}


function manVidView(obj_id) {
    location.assign("showVid.php?obj_id="+obj_id);
}

function manMembers(mbr_id) {
    location.assign("manageMembers.php?mbr="+mbr_id);
}

function manVidDelete(obj_id) {
    location.assign("deleteVid.php?obj="+obj_id);
}

function editCartoon(obj_id) {
    alert("edit a cartoon. Put cartoon "+obj_id+" on top");
  /*  location.assign("editCartoons.php?obj="+obj_id);   */
} 

function editMember(mbr) {
    location.assign("editMember.php?mbr="+mbr); 
}  
     
function newFrame(obj_id) {
    alert("Hello Earthling. You want to add a Frame (y/n) to object "+obj_id);
    location.assign("addFrame.php?obj="+obj_id);
}

function highlight(x) {
/*    alert("Hello - HighLight");      */
/*    x.style.backgroundColor = "#047bfa"; */
    x.style.backgroundColor = "#d3d3d3";    
    
} 

function high_green(x) {
/*    x.style.backgroundColor = "#72f315";  */ 
    x.style.class = "butBold"
}

function normal(x) {
/*   alert("Goodbye - back to Normal");  */  
   x.style.backgroundColor = "#c8ddde"; 
}

function suckeggs() {
    alert("You are in the Egg Sucking routine");
}

function seqFrames_form_validate(x) {
   var rtn            = true; 
   var delete_cartoon = true;
   var seqValue       = ''; 
   var startOf_name   = 0;
   var seqArray       = [];
   var j              = 0;
   var elementArray   = [];
   var element_name   = ''; 
   var row            = ''; 
   
   var eMsg = document.getElementById('errMsg'); 
   eMsg.innerHTML = '<p class="my_green"><p>'; 
   
   elementArray = x.getElementsByClassName('form-control'); 
   for (var i=0; i < elementArray.length; i++) {
       element_name     = elementArray[i].name;
       startOf_name     = element_name.indexOf("seq"); 
       seqValue         = elementArray[i].value;
       if (startOf_name == 0) {
           rowId = element_name.replace('seq', 'row'); 
           row = document.getElementById(rowId);  
           if (seqValue == 0) {
               row.setAttribute('class', 'form-group row align-items-center bg-warning'); 
           } else {
               row.setAttribute('class', 'form-group row align-items-center'); 
               delete_cartoon = false; 
               seqArray[j++]  = seqValue;  // only non 0 sequence numbers are stored 
                                           // a 0 sequence numbers means delete the frame 
           }
       } 
   }
   var dd = 0; 
   var duplicatesArray = []; 
   var sequenceErrMsg  = 'No Sequence errors';  
   if (seqArray.length == 0) {
       rtn = confirm("There will be no Frames for this Cartoon after all deletes are done. This Cartoon will be deleted. If you wish to DELETE the CARTOON click OK else click Cancel");
   } else {
       var jj = 0;
       // sort the array of sequence numbers 
       // put the duplicate sequence numbers into the duplicatesArray array.
       // only add the duplicate sequence number once, do not include the same number more than once
       seqArray.sort;  
       for (var kk = 1; kk < seqArray.length; jj++, kk++) {
           if (seqArray[jj] == seqArray[kk]) {
                if (duplicatesArray[dd] !== seqArray[kk]) {
                   duplicatesArray[dd++] = seqArray[kk];       
                }
           }
       }
   }
   
   // if dd is > 0 then duplicated wee inserted into the duplicatesArray array 
   // we have an input error, there should be no duplicate sequence numbers. 
   if (dd > 0) {
       sequenceErrMsg =  'Duplicate Sequence numbers not allowed: ==> ' + duplicatesArray;  
       eMsg.innerHTML =  '<h4 class="bg-danger text-white">'+sequenceErrMsg+'</h4>';  
       rtn = false; 
   }  
}
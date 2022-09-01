<?php
session_start(); 

// define some global variables 
$pdo = null;  

/* set_exception_handler('allOtherErrorsHandler'); */

function logout() {
    $_SESSION = array();
    session_destroy();
    returnToIndexPage(); 
}

function hello($person) {
    echo "Hello " . $person . "<br />";
}

function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function dbConnect() {
    global $pdo;

    $ok = true;

    $host    = 'localhost';
    $dbname  = 'programm_zup';                                                              
    $user    = 'programm_zup_u';
    $pass    = 'pNpzzor!lc!n#S7';
    $charset = 'utf8';

    $dsn     = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,];

    try  { 
        $pdo = new PDO($dsn, $user, $pass, $options);
    }    
    catch (\PDOException $e) { 
        $ok = false;
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
    return $ok;
}

function listMbrStatus($pdo)  {
    $stmnt = $pdo->query('select mbr_sts, description from Msts');
    while ($row = $stmnt->fetch())
    {
        echo $row['mbr_sts'] . " - " . $row['description'] . "<br \>";
    }
}

function signUp($pdo, $userid, $passHash, $name, $email) {
    $userid    = clean_input($userid);
    $name      = clean_input($name);
    $email     = clean_input($email);
    $mbr_sts   = 'u';  // Unverified new member
    $mbr_type  = 'm';  // Basic membership (Regular member)
    
    // returns an array containing - error message, the new member id, and the new signUp hash
    $newMbr = ['errMsg' => null, 'newId' => null, 'signUpHash' => null];
       
    $today          = new DateTime();  
    $signUpHash     = password_hash($today->getTimestamp(), PASSWORD_DEFAULT); 
 
    $stmt = $pdo->prepare('INSERT INTO Members
        (user_id,  password, name, hash,  email,  mbr_sts,  mbr_type)
    VALUES (:userid, :password, :name, :hash, :email, :mbr_sts, :mbr_type)');
    try {           
        $stmt->execute(['userid' => $userid,
            'password' => $passHash,
            'name'     => $name,
            'hash'     => $signUpHash,
            'email'    => $email,
            'mbr_sts'  => $mbr_sts,
            'mbr_type' => $mbr_type]);
    } catch (PDOException $e) {
        $existingkey = "Integrity constraint violation: 1062 Duplicate entry";
        if (strpos($e->getMessage(), $existingkey) !== FALSE) {
            $newMbr['errMsg'] = "User Id is taken by another user, Please select a different one";
            return $newMbr;
        } else {
            throw $e;
            $newMbr['errMsg'] = $e->getMessage(); 
            return $newMbr;
        }
    }

    $stmt      = $pdo->prepare('select mbr_id from Members where user_id = ?');
    $stmt->execute([$userid]);
    $row       = $stmt->fetch(); 
    $mbr_id               = $row['mbr_id'];
    $newMbr['newId']      = $mbr_id;
    $newMbr['signUpHash'] = $signUpHash; 
    
    return $newMbr; 
}

function mail_the_Welcome($email, $mbr_id, $userid, $name, $hash) {
    // send a confirmation email out to the new client
    // substitute real values into the Welcome message and then send it
    $request_scheme = $_SERVER['REQUEST_SCHEME'];
    $server_name    = $_SERVER['SERVER_NAME']; 
    $self           = $_SERVER['PHP_SELF'];
    $page           = '/confirm.php';
    $get            = '?id='.$mbr_id.'&h='.$hash;   
  //  $verifyLink     = $request_scheme.'://'.$server_name.$self.$get; 
    $verifyLink     = $request_scheme.'://'.$server_name.$page.$get;
  
    $filename = './emails/welcome.html'; 
    $msg = ''; 
  
    if (is_file($filename)) {
        echo "$filename found OK"; 
        $msg = file_get_contents($filename);
    } else {
        echo "Cannot find $filename";
        exit;
    }

    // $msg         = nl2br($msg);
    $msg         = str_replace('%%user_id%%', $userid, $msg);
    $htmlBody    = str_replace('%%verifyLink%%', $verifyLink, $msg);
    $subject     = "Welcome to Dr. Zup";
    $sender_name = "Zup";
    $attach      = "./images/zup.png";
    
    $ok = zupMail($email, $sender_name, $subject, $htmlBody, $attach);
}

function send_pw_reset_email($mbr_id, $user_id, $name, $email, $hash, $today) {
    // this function is similar to the "mail_the_welcome()" function above; 
    // send an email for change 
    $request_scheme = $_SERVER['REQUEST_SCHEME'];
    $server_name    = $_SERVER['SERVER_NAME']; 
    $self           = $_SERVER['PHP_SELF'];
    $page           = '/newPwd.php';
    $get            = '?id='.$mbr_id.'&sent='.$today.'&h='.$hash;   
    $pwdLink        = $request_scheme.'://'.$server_name.$page.$get;
      
    $filename = './emails/forgotPw.html'; 
    $msg = ''; 
  
    if (is_file($filename)) {
        echo "$filename found OK"; 
        $msg = file_get_contents($filename);
    } else {
        echo "Cannot find $filename";
        exit;
    }

    // $msg         = nl2br($msg);
    $msg         = str_replace('%%name%%', $name, $msg);
    $msg         = str_replace('%%user_id%%', $user_id, $msg);
    $msg         = str_replace('%%today%%', $today, $msg);
    $htmlBody    = str_replace('%%pwdLink%%', $pwdLink, $msg);
    $subject     = "Dr. Zup Password";
    $sender_name = "Zup";
    $attach      = "./images/zup.png";
    
    $ok = zupMail($email, $sender_name, $subject, $htmlBody, $attach);
}

function send_userId_email($email, $uids) {
    // this function is similar to the "mail_the_welcome()" function above; 
    // send a list of user_id associted qith the email       
    $filename = './emails/userList.html'; 
    $msg = ''; 
  
    if (is_file($filename)) {
        echo "$filename found OK"; 
        $msg = file_get_contents($filename);
    } else {
        echo "Cannot find $filename";
        exit;
    }
    
    $uList = '<p><ul>';  
    for ($ii = 0; $ii < count($uids); $ii++) {
        $u  = '<li>';
        $u .= $uids[$ii];
        $u .= '</li>';
        $uList .= $u;
    }
    $uList .= '</ul></p>'; 

    // $msg         = nl2br($msg);
    $today       = date("F j, Y");
    $msg         = str_replace('%%today%%', $today, $msg);
    $htmlBody    = str_replace('%%list%%', $uList, $msg);
    $subject     = "Dr. Zup Members List";
    $sender_name = "Zup";
    $attach      = "./images/zup.png";
    
    $ok = zupMail($email, $sender_name, $subject, $htmlBody, $attach);
    
}

function confirm_signUp($pdo, $mbr_id, $hash) {
// confirm the email recieved has to appropiate hash code; 
// delete the hash code and reset the member status to 'a' for active
// return a message  
   $done    = false;  
   $message = 'Zup was unable to confirm your membership. Please try signing up again';
   
   $stmt = $pdo->prepare("SELECT DATEDIFF(NOW(), m.signup_dt) AS 'days'
         FROM Members AS m      
        WHERE m.mbr_id   = :mbr_id
          AND m.mbr_sts  = 'u' 
          AND m.hash     = :hash 
          AND m.hash IS NOT NULL
          AND m.mbr_type = 'm'");      

    $stmt->execute(['mbr_id' => $mbr_id,
                    'hash'   => $hash]);    
    $mbrCount = $stmt->rowCount(); 
    if ($mbrCount != 1) {
        $err = $mbrCount.' records for member found. Please try the Sign up again';
        trigger_error($err, E_USER_NOTICE);
        return $message;    
    }
    
    $mbr_rcds = $stmt->fetch();
    
    $days = $mbr_rcds['days']; 
    if ($days > 2) {
        $message = 'Sign Up confirmation period is 2 days. Your Zup sign up attempt has expired. Please try again';
        $count = 0;
        $count = delete_u_MbrRcd($pdo, $mbr_id); 
        return $message;
    }

    $sts = 'a';    // set status to 'a' for active
    $done = updateMbrSts($pdo, $mbr_id, $sts);
    if ($done) {
        $message = 'Sign up is confirmed. Please Sign In';
    }
    
return $message; 
}

function validate_pwd_reset($pdo, $mbr_id, $sent, $hash) {
    $result = ['ok' => false,
               'message' => 'Zup was unable to confirm your request to re-set your password. Please try again', 
               'name' => '', 
               'user_id' => ''];
    
    $stmt = $pdo->prepare("SELECT DATEDIFF(:sent, NOW()) AS 'days', m.name, m.user_id
         FROM Members AS m      
        WHERE m.mbr_id   = :mbr_id
          AND m.mbr_sts  = 'a' 
          AND m.hash     = :hash
          AND m.hash IS NOT NULL");     

    $stmt->execute(['mbr_id' => $mbr_id,
                    'sent'   => $sent, 
                    'hash'   => $hash]);    
    $mbrCount = $stmt->rowCount(); 
    if ($mbrCount != 1) {
        $err = $mbrCount.' records for member found. Password reset failed. Please try again';
        trigger_error($err, E_USER_NOTICE);
        $result['ok']      = false;
        $result['message'] = $err; 
        return $result;    
    }  
    
    $mbr_rcds = $stmt->fetch();
    
    $days    = $mbr_rcds['days']; 
    $name    = $mbr_rcds['name']; 
    $user_id = $mbr_rcds['user_id'];
    
    $result['name']    = $name;
    $result['user_id'] = $user_id; 
    
    if ($days > 1) {
        $err               = 'Password reset request has a shelf life of 1 day. Yours has expired. Please try again.';
        $count             = 0;
        $result['ok']      = false;
        $result['message'] = $err;
        return $result;
    } 
    
    $result['ok']  = true;
    $result['message'] = 'Please continue. Enter a new password'; 
    return $result; 
}

function zupMail($to, $sender_name, $subject, $htmlBody, $attach) {
    require "../zup_php/mail_functions.php";
    $ok = zupMailOut($to, $sender_name, $subject, $htmlBody, $attach); 
    return $ok;
}

function get_welcome_contents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        return ob_get_clean();
    }
    return false;
}

function signIn($pdo, $userid, $inPword) {
    $userid   = clean_input($userid);
    $pword    = clean_input($inPword);

    $stmt = $pdo->prepare('SELECT mbr_id, password, name, email, mbr_sts, mbr_type
        FROM Members
        WHERE user_id  = :userid     AND
    mbr_sts = "a"');

    $stmt->execute(['userid'    => $userid]);

    $rowData = $stmt->fetch(); 

    if ($rowData) {     
        $mbr_id                = $rowData['mbr_id'];
        $dbPassword            = $rowData['password'];
        $mbr_type              = $rowData['mbr_type'];
        $mbr_sts               = $rowData['mbr_sts'];
        $name                  = $rowData['name'];
        $email                 = $rowData['email']; 

        if (password_verify($pword, $dbPassword)) { 
            $signInErr             = NULL;  
            
            // get rid of all current session data
            $_SESSION              = array(); 
            
            // establish a new set of session data            
            $_SESSION['mbr_id']    = $mbr_id; 
            $_SESSION['mbr_type']  = $mbr_type;
            $_SESSION['mbr_sts']   = $mbr_sts;
            $_SESSION['name']      = $name;
            $_SESSION['email']     = $email;
            $_SESSION['userid']    = $userid; 
            returnToIndexPage();
        } else {
            $signInErr = "Password is not valid- Please try again";  
            return $signInErr;  
        }  
    } else { 
        $signInErr = "User Id not found - Please try again";
        return $signInErr;
    }             
}

function chgPwd($pdo, $mbr_id, $password) {  
    $passHash   = password_hash($password, PASSWORD_DEFAULT);   // customers password
    
    $stmt = $pdo->prepare('UPDATE Members
          SET password = :passHash, lst_updt = CURRENT_TIMESTAMP, hash = NULL
        WHERE mbr_id = :mbr_id'); 
    $stmt->execute(['mbr_id'      => $mbr_id, 
                    'passHash'    => $passHash]);
    $updateCount = $stmt->rowCount();
    if ($updateCount == 1)
        return true;

    return false; 
}

function setAuthority() {
    /* authoity levels
    0 - none        - this the authority assigned to Guests
                      Can only viw objects with a public scope
    1 - member      - View all objects not marked with a private scope 
                      owned by the member or not marked as private  
    2 - contributor - includes level 1 authority plus the ability to 
                      create and edit their own objects
                      May post objects for other members to view
    3 -trusted      - full authority
                      May post objects with a public scope
    4 - admin       - full authority to view and post any object           
    */   

    if (empty($_SESSION['mbr_id'])) {
        return 0;      // Not signed in, no authority granted 
    } else {
        $type = $_SESSION['mbr_type'];
        $sts  = $_SESSION['mbr_sts'];

        if ($sts != 'a') {
            return 0;  // not an active member, no authority is granted 
        } 

        if ($type == 'a')  { 
            return 4;  // Administrator, all authorities are granted 
        }

        if ($type == 't') { 
            return 3;  //  Member is a trusted contributor, Authority to contibute to Public;
        }    

        if ($type == 'c') { 
            return 2;  //  Untrusted Contibutor, Authority limits contributions to membership only
        }    

        if ($type == 'm')  { 
            return 1;  //  basic member
        } 

        return 0;      // everybody else gets no authority
    }
}  

function returnToIndexPage() {
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'index.php';
    header("Location: http://$host$uri/$extra");

    exit;
} 

function gotoPage($page) {
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = $page;
    header("Location: http://$host$uri/$extra");

    exit;
} 

function check_file_uploaded_name ($filename)
{
/**
* Check $_FILES[][name]
*
* @param (string) $filename - Uploaded file name.
* @author Yousef Ismaeil Cliprz
*/
    (bool) ((preg_match("`^[-0-9A-Z_\.]+$`i",$filename)) ? true : false);
}

function check_file_uploaded_length ($filename)
{
    /**
* Check $_FILES[][name] length.
*
* @param (string) $filename - Uploaded file name.
* @author Yousef Ismaeil Cliprz.
*/
    return (bool) ((mb_strlen($filename,"UTF-8") > 225) ? true : false);
}

function supported_video($type,$extension) {;
    $mime_table = array('video'=>['webm'=>'video/webm', 'mp4'=>'video/mp4', 'ogg'=>'video/ogg'], 
        'image' =>['apng'=>'image/apng', 'bmp'=>'image/bmp', 'gif'=>'image/gif',
            'jpg'=>'image/jpeg', 'jpeg'=>'image/jpeg', 'jfif'=>'image/jpeg',
            'pjpeg'=>'image/jpeg', 'pjp'=>'image/jpeg', 'png'=>'image/png',
            'tif'=>'image/tiff', 'tiff'=>'image/tiff', 'webp'=>'image/webp'],
        'audio' => ['wav'=>'audio/wave', 'mp3'=>'audio/mp3', 'mp4'=>'audio/mp4']);

    if (array_key_exists("$type", $mime_table)) {
        if (array_key_exists("$extension", $mime_table["$type"])) {
            $mime = $mime_table["$type"]["$extension"]; 
            return $mime;
        } else { 
            return false;
        }   
    } else {
        return false;   
    }
}      

function select_scope_options($pdo, $slctd) {                 
    // create the <option> rows for a select box of "Scope" options.
    // Include only the scopes the current user has the authority to use  
    //
    // -----------------------------------------------------------------------
    // include the scopes  | Person granting the scope must have an          |
    // below in <option>   | appropiate authority level                      |
    // only if the user    | (a = the authority level)                       |
    // has the appropiate  |-------------------------------------------------|
    // authority           | Guest | Regular | Contributing |Trusted | Admin |
    // (s = scope code)    | a = 0 |  a = 1  |    a = 2     | a = 3  | a = 4 |
    // -----------------------------------------------------------------------
    // (s = 0)  none       |   x   |    x    |      x       |   x    |   x   |
    // (s = 1)  private    |       |         |      x       |   x    |   x   |
    // (s = 2)  membership |       |         |      x       |   x    |   x   |
    // (s = 3)  public     |       |         |              |   x    |   x   |
    // -----------------------------------------------------------------------
    $a = setAuthority();

    $stmt = $pdo->query('SELECT scope_code, description FROM Scope');
    foreach ($stmt as $row) {
        $select = ($slctd == $row['scope_code']) ? 'selected' : '';
        if (($row['scope_code'] == 0)               ||
           (($row['scope_code'] < 3)  && ($a > 1))  ||
           (($row['scope_code'] >= 3) && ($a == 3)) ||
           (($row['scope_code'] >= 3) && ($a == 4))) {
              echo "<option value='".trim($row['scope_code'])."' ".$select.">".trim($row['description'])."</option>"; 
        }
    }    
}

function select_contributing_user_ids($pdo, $obj_type, $filter) {
    // create the <option> rows for a select box of user_ids that have an object on file 
    // in scope and of the right type
    // Defaults: 
    //     scope is (3) public
    //     slctd is 
    //        - the signed in members id or 
    //        - member is a guest then user_id selection defaults to all (*)  
           
    $hidden     = ($filter['hidden'])     ? 'y' : 'n'; 
    $private    = ($filter['private'])    ? 'y' : 'n'; 
    $membership = ($filter['membership']) ? 'y' : 'n';   
    $public     = ($filter['public'])     ? 'y' : 'n';   
    
    $uid        = (isset($filter['uid'])) ? $filter['uid'] : 'guest';   
    $slctd      = ($uid == 'guest') ? '*' : $uid; 

    if ($uid == 'guest') {
        // '*' is default selected value - means fetch all users 
        $hidden     = 'n';                     // guest can only access public objects
        $private    = 'n'; 
        $membership = 'n';
        $public     = 'y';                     // public only for guests
    }
     
    $stmt = $pdo->prepare("SELECT m.user_id FROM Members AS m 
         WHERE m.mbr_sts = 'a'   
           AND EXISTS 
                (SELECT o.mbr_id FROM Objects AS o
                   WHERE o.mbr_id = m.mbr_id
                     AND o.obj_type = :obj_type
                     AND ((:hidden     = 'y' AND o.scope_code = 0)  
                      OR  (:private    = 'y' AND o.scope_code = 1) 
                      OR  (:membership = 'y' AND o.scope_code = 2) 
                      OR  (:public     = 'y' AND o.scope_code = 3)))"); 
                     
    $stmt->execute(['obj_type'   => $obj_type,
                    'hidden'     => $hidden,
                    'private'    => $private,
                    'membership' => $membership,  
                    'public'     => $public]);  
    
    $slctOpt = ($slctd == '*') ? 'selected' : '';                 
    echo "<select class='form-control' id='usr' name='uid' form='filterForm'>"; 
    echo "<option value='*' ".$slctOpt.">All Members</option>"; 
    while ($row = $stmt->fetch()) {
        $user_id = $row['user_id']; 
        $slctOpt = ($slctd == $user_id) ? 'selected' : ''; 
        echo "<option value='$user_id' $slctOpt > $user_id</option>"; 
    }
    echo "</select>";                                     
}

function selectMbrType($pdo, $slctd) {
    $stmt = $pdo->query('SELECT mbr_type, description FROM Mtype');
    foreach ($stmt as $row) {
        $select = ($slctd == $row['mbr_type']) ? 'selected' : ''; 
        echo "<option value='".$row['mbr_type']."' ".$select.">".$row['description']."</option>";    
    }         
}

function selectMbrSts($pdo, $slctd) {
    $stmt = $pdo->query('SELECT mbr_sts, description FROM Msts');
    foreach ($stmt as $row) {
        $select = ($slctd == $row['mbr_sts']) ? 'selected' : ''; 
        echo "<option value='".$row['mbr_sts']."' ".$select.">".$row['description']."</option>";    
    }         
}

function validate_upload_file($id, $mime_type) {
    // validate the file is ok to upload
    //
    // $id        - is the "HTML ID" assigned <input type='file' ... > tag
    // $mime_type - identifies the file as 'video', 'image', or 'audio'
    //
    // return an error message if a problem is discoverd
    // else return false to indicate no problems found

    $File      = $_FILES["$id"]["name"];
    $Err       = $_FILES["$id"]["error"];
    $Type      = $_FILES["$id"]["type"];
    $Size      = $_FILES["$id"]["size"];
    $Temp      = $_FILES["$id"]["tmp_name"];

    $suffix    = strtolower(pathinfo($File, PATHINFO_EXTENSION));
    if (!isset($suffix)) {
        $suffix = '';
        return "File does not have a valid suffix, ie. 'jpeg', 'mp4' ...";
    } 

    $supported_mimes = supported_video($mime_type,$suffix);
    if (!$supported_mimes) {
        return "$suffix $mime_type files not supportrd by this application";
    } else { 
        if ($supported_mimes != $Type) {
            return "$mime_type file does not hsve the correct suffix for its type";
        }
    } 

    switch($Err) {
        case 0:
            $Msg = '';  // no errors
            break;
        case 1:
            $supported_mimes = "File size exceeds UPLOAD_MAX_FILESIZE in PHP";
            break;
        case 2:
            $Msg = "File size exceeds MAX_FILE_SIZE on this web page";
            break;
        case 3:
            $Msg = "File was only partialy loaded - you should try again";
            break;
        case 4:
            if (isset($File)) {
                $Msg = "The File was not upload - No reason given - contact sys admin"; 
            } else {
                $Msg = '';  // No file name given, nothing to do
            }
            break;
        case 6:
            $Msg = "Missing the temp folder - contact sys admin"; 
            break;
        case 7:
            $Msg = "Failed to write to disk - contact sys admin";
            break;
        case 8: 
            $Msg = "PHP extension has stopped the upload - contact sys admin"; 
            break;
        default:
            $Msg = "Undefined upload error - contact sys admin";
    }

    if ($Msg != '') {
        return $Msg;
    } else {
        return false;   // there were no errors
    }
}

function move_to_mbr_directory($Tmp,$mbr_dir,$fileName,$Overwrite) {
    // move the file out of the temp directory into the members own directory
    // return:
    //    array $result
    //      $result['errLvl'] 
    //         0 - no errors   
    //         1 - information  - upload stop - do not use this file 
    //         2 - warning      - upload canceled  - do not create new video object    
    //         3 - severe error - upload must stop - do not create new video object
    //      $result['newName'] 
    //         the file name cleaned up, no ' or " characters 
    //      $result['msg'] 
    //         the message text  

    $result = ['errLvl'=>0,'newName'=>'','msg'=>''];

    // rename file - get rid of any ' or " characters
    $newName = str_replace("'",'',$fileName);
    $newName = str_replace('"','',$newName);
    $newName = trim($newName);
    $result['newName'] = $newName;

    if (!file_exists($mbr_dir)){
        clearstatcache(); 
        $ok = mkdir($mbr_dir, 0777, true);  
    } 

    $destination = $mbr_dir.$newName;

    // check for duplicate
    If (file_exists("$destination")) {
        clearstatcache(); 
        if ($Overwrite) {    
            // Duplicate file - overwite requested 
            $result['errLvl'] = 0; 
            $result['msg']    = 'Duplicate - File overwritten';
        } else {      
            // Do not upload file - overwrite not requested 
            $result['errLvl'] = 1; 
            $result['msg']    = 'Duplicate - Overwrite not checked, File not uploaded';
        }
    }

    if ($result['errLvl'] < 1) {
        // move the uploaded file into the members directory
        if (!move_uploaded_file("$Tmp","$destination")) {
            $result['errLvl'] = 3;
            $result['msg']    = "Upload failed - unable to move file out of tmp directory";    
        }
    }

    return $result;  
}

function get_object($pdo, $obj_id) {
    // Get and return the Objects record
    // return
    //   - false if no object is found
    //   - an associative array ($object) contianing the field values if the Object is found 
    //     include in the objects record the framne_count and current maximum seq number
                
    $stmt = $pdo->prepare('SELECT o.obj_id, o.mbr_id, o.title, o.obj_type,
                                  o.scope_code, o.obj_description, o.lst_updt,
                                  MAX(p.seq) AS lastSeq, count(p.part_id) AS frameCount
                             FROM Objects AS o, Parts as p
                            WHERE o.obj_id = :obj_id
                              AND o.obj_id = p.obj_id
                              AND o.obj_type = 3
                              AND p.file_type = "image"');  
    $stmt->execute(['obj_id' => $obj_id]); 
    $objects = $stmt->fetch(PDO::FETCH_ASSOC); 
    $objectCount = $stmt->rowCount();
    if ($objectCount < 1) {
        return false;
    } else {
        return $objects;
    }                                                                
}

function insert_new_object($pdo,$newObj) {
    // start a new transaction 
    //    insert a new object row into the database
    //    insert parts rows into the database
    // end transaction
    // 
    // return the array $result
    //      $result[0] - success true/false  
    //      $result[1] - A message 
    //      $result[2] - the object id of the newly inserted  object  

    $result = [true, 'begin insert new object', NULL];

    $pFile           = NULL;
    $pMime_code      = NULL;
    $object_type     = $newObj['object_type'];

    $title           = clean_input($newObj['title']);
    $scope_code      = $newObj['scope'];
    $obj_description = clean_input($newObj['obj_description']);
    $vFile           = clean_input($newObj['vFile']);
    $vMime_code      = clean_input($newObj['vMime_code']);
    if (!empty($newObj['pFile'])) {
        $pFile       = clean_input($newObj['pFile']);
    }
    if (!empty($newObj['pMime_code'])) {
        $pMime_code  = clean_input($newObj['pMime_code']);
    }

    $obj_id          = NULL;
    $mbr_id          = $_SESSION['mbr_id'];      
    $part_id         = NULL;
    $seq             = 1;
    
    switch ($object_type) {
        case 4:     //  video object
           $file_type    = 'video';
           $pType        = 'poster';  // optional extra file is a poster
           $item         = 'video';
           break; 
        case 3:     //  slide show or cartoon object
           $file_type    = 'image';
           $pType        = 'none';
           $item         = 'cartoon';
           $seq          = 1;        // initial sequence number for the first frame
           break;
        default:   //  unknown object
           $file_type    = 'unknown';
           $pType        = 'none';  
           $item         = '???';   
    }
    
    $stmt1 = '';
    $stmt2 = '';
    $stmt3 = '';

    try {
        // start transaction 
        $pdo->beginTransaction();
        // insert the object row
        $stmt1 = $pdo->prepare('INSERT INTO Objects
                    (mbr_id, title, obj_type, scope_code, obj_description)
            VALUES (:mbr_id, :title, :obj_type, :scope_code, :obj_description)');         
        $stmt1->execute(['mbr_id'          => $mbr_id,
                         'title'           => $title,
                         'obj_type'        => $object_type,
                         'scope_code'      => $scope_code, 
                         'obj_description' => $obj_description]);   

        // get the new obj_id
        $obj_id = $pdo->lastInsertId();

        // insert a new parts row for the object
        $stmt2 = $pdo->prepare('INSERT INTO Parts
                    (obj_id, file_name, file_type, mime_code, seq)
            VALUES (:obj_id, :vFile, :vFile_type, :vMime_code, :seq)');
        $stmt2->execute(['obj_id' => $obj_id, 
            'vFile'      => $vFile,
            'vFile_type' => $file_type,
            'vMime_code' => $vMime_code,
            'seq'        => $seq]);    

        // if the optional poster file is present insert a new parts row for the poster
        if (!empty($pFile)) {
            if (!isset($pMime_code)) {
                $pMime_code = 'image/*';
            }
            $stmt3 = $pdo->prepare('INSERT INTO Parts
                         (obj_id, file_name, file_type, mime_code, seq)
                 VALUES (:obj_id, :pFile, :pFile_type, :pMime_code, :seq)');
            $stmt3->execute(['obj_id' => $obj_id, 
                'pFile'      => $pFile,
                'pFile_type' => $pType,
                'pMime_code' => $pMime_code,
                'seq'        => $seq]);    
        }

        //  commit work  (ends the transaction)
        $pdo->commit();
    }
    catch(PDOException $e) {    
        $pdo->rollBack();
        $result[0] = false;
        $result[1] = "Insert failed, database error";
        throw $e;       // re-throw the error 
    } 

    if ($result[0])  {
        $result[1] = "A New $item has been added to your account "; 
        $result[2] = $obj_id; 
    } 

    return $result; 
}

function getVidToPlay($pdo, $obj) {
    // fetch the video object and present it to be played
    $ok = true;
    
    $stmt1 = $pdo->prepare("
        SELECT
            o.obj_id          AS 'obj',
            o.title           AS 'title',
            o.scope_code      AS 'scope',
            o.obj_description AS 'description', 
            o.lst_updt        AS 'lst_updt',    
            p.file_name       AS 'video_file',
            p.mime_code       AS 'mime',
            p.file_type       AS 'file_type',
            m.mbr_id          AS 'owner',
            m.user_id         AS 'createdBy', 
            pp.file_name      AS 'poster_file',
            pp.mime_code      AS 'poster_mime'
        FROM Members AS m 
            INNER JOIN Objects AS o 
            ON m.mbr_id  = o.mbr_id
            INNER JOIN Parts   AS p
            ON o.obj_id  = p.obj_id
            LEFT OUTER JOIN Parts AS pp
            ON (o.obj_id = pp.obj_id and pp.file_type = 'poster')       
        WHERE  o.obj_id     = :obj");   

    $stmt1->execute(['obj' => $obj]);
    $vid = $stmt1->fetch();
    return $vid;
}

function browse($pdo, $filter, $obj_type) {    
    $hidden     = $filter['hidden'];
    $private    = $filter['private'];
    $membership = $filter['membership'];
    $public     = $filter['public'];
    
    $signedIn_id     = (isset($_SESSION['mbr_id'])) ? $_SESSION['mbr_id'] : 0;
    $requested_uid   = $filter['uid']; 
    
    switch ($obj_type) {
        case 3:
            $list_title  = "Cartoon";
            $show_script = "viewCartoon";
            break;
        case 4:
            $list_title  = "Video";
            $show_script = "viewVid";
            break;
        default:
            $list_title  = "Unknown";
            $show_script = "Unknown";         
    }
    
    // Guests and members not currently signed in are allowed 
    // to view only the "public" cartoons
    if ($signedIn_id == 0) {
        $public     = true;
        $membership = false; 
        $private    = false;
        $hidden     = false;   
    }
        
    $public_rqst     = ($public)     ? 'y' : 'n'; 
    $membership_rqst = ($membership) ? 'y' : 'n';
    $private_rqst    = ($private)    ? 'y' : 'n';  
    $hidden_rqst     = ($hidden)     ? 'y' : 'n';    

    $stmt1 = $pdo->prepare("
        SELECT
            o.obj_id          AS 'obj', 
            o.title           AS 'title', 
            o.scope_code      AS 'scope',
            o.obj_description AS 'description',
            o.lst_updt        AS 'lst_updt',    
            m.mbr_id          AS 'owner',
            m.user_id         AS 'createdBy' 
        FROM Members AS m 
            INNER JOIN Objects AS o 
            ON m.mbr_id = o.mbr_id    
        WHERE m.mbr_sts    = 'a'          
          AND o.obj_type   = :obj_type            
          AND o.scope_code > 0
          AND (((:public_rqst = 'y' AND o.scope_code = 3)      AND
                (m.user_id = :requested_uid1 OR :requested_uid2 = '*'))
             OR 
               ((:membership_rqst = 'y' AND o.scope_code = 2)  AND
                (m.user_id = :requested_uid3 OR :requested_uid4 = '*'))
             OR
               (:private_rqst = 'y' AND o.scope_code = 1 AND  m.mbr_id = :signedIn_id))     
        ORDER BY o.lst_updt DESC");  
                     
    $stmt1->execute(['public_rqst'     => $public_rqst, 
                     'membership_rqst' => $membership_rqst,
                     'private_rqst'    => $private_rqst,
                     'signedIn_id'     => $signedIn_id,
                     'obj_type'        => $obj_type,
                     'requested_uid1'  => $requested_uid,
                     'requested_uid2'  => $requested_uid,
                     'requested_uid3'  => $requested_uid,
                     'requested_uid4'  => $requested_uid]);                  

    echo "<h3 class='text-center'>Click on a row to VIEW the $list_title</h3>";
    echo "<table class='table'>";
    echo "<thead>";
    echo "<tr>"; 
    echo "<th scope='col'>Title</th>";
    echo "<th scope='col'>Created By</th>";
    echo "<th scope='col'>description</th>";
    echo "<th scope='col'>Last Updated</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";                           
    while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {   
        $show        = $show_script."(".$row['obj'].")"; 
        $title       = $row['title'];
        $description = $row['description'];
        $createdBy   = $row['createdBy'];
        $lst_updt    = $row['lst_updt'];
        echo "<tr id=rowId".$row['obj']." onclick='$show' onmouseover='highlight(this)' onmouseout='normal(this)'>";
        echo "<th>".$title."</th>";
        echo "<td>".$createdBy."</td>";
        echo "<td>".$description."</td>";
        echo "<td>".$lst_updt."</td>"; 
        echo "</tr>";  
    }
    echo "<tr><td colspan='4'></td></tr>";
    echo "</tbody>";
    echo "</table>"; 
    return true;        
}

function listVideos_Manage($pdo, $filter) {
    // Guests, Users not currently signed and regular members do not have  
    // videos to manage
    // administrators can manage all videos including those belonging to 
    // "inactive" membera     

    $signed_in_mbr   = (isset($_SESSION['mbr_id']))   ? $_SESSION['mbr_id']   : NULL; 
    $signed_in_type  = (isset($_SESSION['mbr_type'])) ? $_SESSION['mbr_type'] : ' '; 
    
    $admin           = ($signed_in_type == 'a') ? 'y' : 'n';
    $hidden          = $filter['hidden'];
    $public          = $filter['public'];
    $membership      = $filter['membership'];
    $private         = $filter['private'];
    $uid             = $filter['uid']; 

    $hidden_rqst     = ($hidden)     ? 'y' : 'n';
    $public_rqst     = ($public)     ? 'y' : 'n'; 
    $membership_rqst = ($membership) ? 'y' : 'n';
    $private_rqst    = ($private)    ? 'y' : 'n';      

    $stmt1 = $pdo->prepare("
        SELECT
            o.title           AS 'title',
            o.scope_code      AS 'scope',
            o.obj_id          AS 'obj',
            o.obj_description AS 'description',
            o.lst_updt        AS 'lst_updt',    
            p.file_name       AS 'video_file',
            p.mime_code       AS 'mime',
            p.file_type       AS 'file_type',
            m.mbr_id          AS 'owner',
            m.user_id         AS 'createdBy', 
            pp.file_name      AS 'poster_file',
            pp.mime_code      AS 'poster_mime'
        FROM Members          AS m 
        INNER JOIN Objects    AS o 
           ON m.mbr_id  = o.mbr_id
        INNER JOIN Parts      AS p
           ON o.obj_id  = p.obj_id
        LEFT OUTER JOIN Parts AS pp
           ON (o.obj_id  = pp.obj_id AND pp.file_type = 'poster')       
        WHERE  p.file_type = 'video' 
          AND (m.user_id = :userid1 OR :userid2 = '*')     
          AND (m.mbr_sts    = 'a'  OR :admin = 'y')          
          AND  o.obj_type   = 4            
          AND ((:public_rqst     = 'y' AND o.scope_code = 3) OR
               (:membership_rqst = 'y' AND o.scope_code = 2) OR
               (:private_rqst    = 'y' AND o.scope_code = 1) OR 
               (:hidden_rqst     = 'y' AND o.scope_code = 0))         
        ORDER BY o.lst_updt DESC");  

    $stmt1->execute(['public_rqst'     => $public_rqst, 
                     'membership_rqst' => $membership_rqst,
                     'private_rqst'    => $private_rqst,
                     'hidden_rqst'     => $hidden_rqst,
                     'userid1'         => $uid,
                     'userid2'         => $uid,
                     'admin'           => $admin]); 
    
    echo "<div class='row bg-light text-dark d-flex justify-content-center pt-2'>";
        echo "<h4>Video ==></h4>";
        echo "<a class='btn btn-sm btn-dark' onmouseover='m_over(this)' onmouseout='m_out(this)' role='button'  href='newVideo.php'>New</a>";
    echo "</div>";                

    while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
        $vFile = "'members/mbr".$row['owner']."/video/".$row['video_file']."'";
        if (isset($row['poster_file'])) {
            $pFile = "'members/mbr".$row['owner']."/image/".$row['poster_file']."'"; 
        } else {
            $pFile = NULL; 
        } 

        $obj   = $row['obj'];
        $title = $row['title'];
        $desc = "<h4>".$title."</h4><p>".$row['description']."</p>";


        $rowId = "rowId".$obj; 
        echo "<div class='row p-0' id='".$rowId."' onmouseover='highlight(this)' onmouseout='normal(this)'>";
        echo "<div class='d-flex col-md-2 justify-content-start p-2 align-items-center'>";
            echo "<a class='btn btn-sm btn-dark' onmouseover='m_over(this)' onmouseout='m_out(this)' role='button'  href='showVid.php?obj_id=".$obj."'>View</a>";
            echo "<a class='btn btn-sm btn-dark' onmouseover='m_over(this)' onmouseout='m_out(this)' role='button'  href='editVideo.php?obj_id=".$obj."'>Edit</a>";
            echo "<a class='btn btn-sm btn-dark' onmouseover='m_over(this)' onmouseout='m_out(this)' role='button' onclick='return verifyDelete(\"".$title."\")' href='deleteVid.php?obj_id=".$obj."'>Delete</a>";
        echo "</div>"; 

        echo "<div class='col-md-2'>";
        if (isset($pFile)) {
            echo "<a href='showVidj.php?obj='".$obj."'>";     
            echo "     <img src=".$pFile." width='120' height='120' alt='a poster'>";
            echo "</a>"; 
        }        
        echo "</div>";

        echo "<div class='col-md-6 p-2'>";
        echo "$desc";
        echo "</div>"; 

        echo '</div>';
    } 
    return true; 
}

function listCartoons_Manage($pdo, $filter) {
    // Guests, Users not currently signed and regular members do not have  
    // cartoons to manage
    // administrators can manage all cartoons including those belonging to 
    // "inactive" members and all hidden or private cartoons    

    $signed_in_mbr   = (isset($_SESSION['mbr_id']))   ? $_SESSION['mbr_id']   : NULL; 
    $signed_in_type  = (isset($_SESSION['mbr_type'])) ? $_SESSION['mbr_type'] : ' '; 
    $admin           = ($signed_in_type == 'a') ? 'y' : 'n';
    $guest           = (isset($_SESSION['mbr_id'])) ? 'n' : 'y';    

    $hidden_rqst     = ($filter['hidden'])     ? 'y' : 'n';
    $public_rqst     = ($filter['public'])     ? 'y' : 'n'; 
    $membership_rqst = ($filter['membership']) ? 'y' : 'n';
    $private_rqst    = ($filter['private'])    ? 'y' : 'n';    
    
    $uid             =  $filter['uid'];

    $stmt1 = $pdo->prepare("SELECT
            o.obj_id          AS 'obj',
            o.mbr_id          AS 'owner',
            o.title           AS 'title',
            o.obj_type        AS 'obj_type',
            o.scope_code      AS 'scope', 
            o.obj_description AS 'description',
            o.lst_updt        AS 'lst_updt', 
            m.user_id         AS 'createdBy' 
        FROM Members AS m 
        INNER JOIN Objects AS o 
           ON m.mbr_id  = o.mbr_id  
        WHERE
            o.obj_type = 3 
          AND 
        --  filter by authority of the user making the request   
        --  guests are allowed only public works   
            ((:guest1 = 'y' AND o.scope_code = 3 AND ((:uid1 = '' OR :uid2 = m.user_id))) 
               OR
        --  members are allowed their own works plus the public and membership works of other users       
              ((:guest2 = 'n') AND (:uid3 = m.user_id OR (:uid4 = '' AND o.scope_code > 1)))
               OR
        --  admins are allowed access to all works by all members       
               ((:admin = 'y') AND (:uid5 = m.user_id OR :uid6 = '')))  
        --  filter by scope                                      
          AND ((:public_rqst     = 'y' AND o.scope_code = 3) OR 
               (:membership_rqst = 'y' AND o.scope_code = 2) OR 
               (:private_rqst    = 'y' AND o.scope_code = 1) OR 
               (:hidden_rqst     = 'y' AND o.scope_code = 0))         
          ORDER BY (o.lst_updt) DESC");  

    $stmt1->execute(['public_rqst'     => $public_rqst, 
                     'membership_rqst' => $membership_rqst,
                     'private_rqst'    => $private_rqst,
                     'hidden_rqst'     => $hidden_rqst,
                     'uid1'            => $uid,
                     'uid2'            => $uid,
                     'uid3'            => $uid,
                     'uid4'            => $uid,
                     'uid5'            => $uid,
                     'uid6'            => $uid,
                     'guest1'          => $guest,
                     'guest2'          => $guest,
                     'admin'           => $admin]);
    
    // return the Cartoon that were fetched from the database              
    return $stmt1; 
}

function displayCartoonRows($cartoonRows) {
    $previous_obj = 0; 
    foreach ($cartoonRows as $record) { 
        $obj   = $record['obj'];
        $parms = "obj_id=$obj"; 
        $parms2 = "&prev_obj=$previous_obj";
        $previous_obj = $obj; 
        $desc  = "<h4>".$record['title']."</h4><p>".$record['description']."</p>";
        $rowId = "rowId".$obj; 
        $title = $record['title'];

        echo "<div class='row p-0' id='".$rowId."' onmouseover='highlight(this)' onmouseout='normal(this)'>";

        echo "<div class='d-flex col-md-2 justify-content-start p-2 align-items-center'>";
            echo "<a class='btn btn-sm btn-dark' onmouseover='m_over(this)' onmouseout='m_out(this)' role='button'  href='showCartoon.php?".$parms."'>View</a>";
            echo "<a class='btn btn-sm btn-dark' onmouseover='m_over(this)' onmouseout='m_out(this)' role='button'  href='editCartoon.php?".$parms."'>Edit</a>";
            echo "<a class='btn btn-sm btn-dark' onmouseover='m_over(this)' onmouseout='m_out(this)' role='button'  href='seqFrames.php?".$parms."'>Re-Seq</a>";
            echo "<a class='btn btn-sm btn-dark' onmouseover='m_over(this)' onmouseout='m_out(this)' onclick='return verifyDelete(\"".$title."\")' role='button'  href='deleteCartoon.php?".$parms.$parms2."'>Delete</a>";
        echo "</div>";  
    
        echo "<div class='col-md-2 p-2'>";
        echo "<h5>".$record['title']."</h5>";
        echo "</div>"; 
        
        echo "<div class='col-md-1 p-2'>";
        echo "<h5>".$record['createdBy']."</h5>";
        echo "</div>"; 
        
        echo "<div class='col-md-4 p-2'>";
        echo "$desc";
        echo "</div>"; 
                
        echo "<div class='col-md-1 p-2'>";
        echo $record['lst_updt'];
        echo "</div>"; 

        echo '</div>';  // end of the row
    }    
}
            
function getVid($pdo, $obj_id) {
    // display a video identified by its object id (obj_id)
    //
    // This routine will fetch a video only for those privileged to manage 
    // the it, (Update or delete).
    // A "Privileged" persons is either the site administrator or the video's
    // owner (the mbr that uploaded it).   

    $signed_in_mbr  = (isset($_SESSION['mbr_id']))   ? $_SESSION['mbr_id']   : NULL; 
    $signed_in_type = (isset($_SESSION['mbr_type'])) ? $_SESSION['mbr_type'] : NULL;

    $stmt1 = $pdo->prepare("SELECT
        o.title           AS 'title',
        o.obj_description AS 'description',     
        p.file_name       AS 'vFileName',
        p.mime_code       AS 'mime',
        m.mbr_id          AS 'owner',
        m.user_id         AS 'createdBy', 
        m.mbr_type        as 'typeOfOwner',
        pp.file_name      AS 'pFileName'
        FROM Members AS m 
        INNER JOIN Objects AS o 
        ON m.mbr_id  = o.mbr_id
        INNER JOIN Parts   AS p
        ON o.obj_id  = p.obj_id
        LEFT OUTER JOIN Parts AS pp
        ON (o.obj_id  = pp.obj_id and pp.file_type = 'poster')          
        WHERE    p.file_type = 'video'               
        and  o.obj_type  = 4 
        and (m.mbr_id = :signed_in_mbr or :signed_in_type = 'a')            
    and  o.obj_id    = :obj_id");       

    $stmt1->execute(['obj_id' => $obj_id,
        'signed_in_mbr' => $signed_in_mbr,
        'signed_in_type' => $signed_in_type]); 
        
    $vid_dtls = $stmt1->fetch(PDO::FETCH_ASSOC);
    if ($vid_dtls) { 
        $vFile = "'members/mbr".$vid_dtls['owner']."/video/".$vid_dtls['vFileName']."'";
        
        echo "<div class='media-body ml-4'>";
        echo "<h3>".$vid_dtls['title']."</h3><h4>by :".$vid_dtls['createdBy']."</h4>";
        echo "</div>"; 
       
        if (isset($vid_dtls['pFileName'])) {
            $pFile = "'members/mbr".$vid_dtls['owner']."/image/".$vid_dtls['pFileName']."'"; 
        } else {
            $pFile = NULL; 
        }
        echo "<!-- ".$vid_dtls['title']."-->";
        echo "<div class='media pull-left mt-2 mb-4 border'>";
        echo "<video width='85%' height='85%' ";
        if (isset($pFile)) {
            echo "poster=".$pFile;
        }
        echo "controls>";
        echo "<source src=".$vFile." type='".$vid_dtls['mime']."'>";
        echo "Your browser does not support the video tag.";
        echo "</video>";
        echo "</div>";
        
        echo "<p>";
        echo $vid_dtls['description'];
        echo "</p>";
    }
} 

function getComments($pdo, $obj) {
    // retreive comments made on or about object ($obj)
    $stmnt = $pdo->prepare("SELECT
                     com_id    AS 'c_id', 
                     obj_id    AS 'o_obj',
                     com_by    AS 'made_by', 
                     com_sts   AS 'sts', 
                     lst_updt  AS 'date', 
                     com_text  AS 'comment'
                   FROM comments
                  WHERE obj_id = :obj
                  ORDER BY lst_updt DESC"); 
    
    $stmnt->execute(['obj' => $obj]);  
    $comment_rows = $stmnt->fetchAll();
    return $comment_rows; 
}

function getCartoon($pdo, $obj_id) {
    // display a Cartoon identified by its object id (obj_id)
    //
    // This routine will fetch a Cartoon only if it is in scope 
    // Scope defines who can the view it
    //  
    // scope: 
    //   0 None       - No viewing allowed except by the Zup administrator
    //   1 Private    - Viewing by Cartoon Owner or the Zup administrator only
    //   2 Membership - Viewing only by signed in Zup mmembers 
    //   3 Public     - viewing by everyone, signed in or not 
    // 
    // The cartoon, if eligible for viewing will be rendered in HTML code
    // for a carousel       

    $signed_in_mbr  = (isset($_SESSION['mbr_id']))   ? $_SESSION['mbr_id']   : 0; 
    $signed_in_type = (isset($_SESSION['mbr_type'])) ? $_SESSION['mbr_type'] : '';

    $stmt1 = $pdo->prepare("SELECT
                           o.mbr_id     AS 'owner',
                           o.title      AS 'cTitle',
                           o.scope_code AS 'scope',
                           o.obj_description AS 'comic_description',
                           p.seq        AS 'seq', 
                           p.file_name  AS 'frame',
                           p.mime_code  AS 'mime' 
                           FROM Objects AS o 
                           INNER JOIN Parts AS p ON p.obj_id = o.obj_id 
                           WHERE o.obj_id = :obj_id 
                           AND o.obj_type = 3
                           AND p.file_type = 'image'                               
                           AND (((o.scope_code = 1) AND (o.mbr_id = :signed_in_owner)) OR
                                ((o.scope_code = 2) AND (0 <> :signed_in_mbr))         OR
                                 (o.scope_code = 3)                                    OR
                                 ('a' = :signed_in_type)) 
                           ORDER BY p.seq");              

    $stmt1->execute(['obj_id' => $obj_id,
        'signed_in_owner' => $signed_in_mbr,
        'signed_in_mbr'   => $signed_in_mbr,
        'signed_in_type'  => $signed_in_type]);   
        
    return $stmt1; 
}               

function showInCarousel($frameCount, $frames) {    
    //  carousel indicators
    echo '<div id="cartoon1" class="carousel slide" data-ride="carousel" data-interval="false">';
    echo '<ol class="carousel-indicators">';
    $f = 0;
    $active = "class='active'";
    while ($f < $frameCount)  {
        echo "<li data-target='#cartoon1' data-slide-to='".$f++."' $active></li>";   
    }
    echo "</ol>";
    
    //  carousel inner
    echo '<div class="carousel-inner mw-50 mh-50">'; 
    $active = 'active';  
    while ($row = $frames->fetch(PDO::FETCH_ASSOC)) {
        if ($active == 'active') {
            echo "<h2 class='text-center'>".$row['cTitle']."</h2>"; 
            $blurb = $row['comic_description']; 
        }
        
        $frame = "members/mbr".$row['owner']."/image/".$row['frame']; 
        echo '<div class="carousel-item '.$active.'">';
        echo '<img src="'.$frame.'" class="d-block w-50 mx-auto" style="height:550px;"  alt="...">';
        echo '</div>';
        $active = ''; 
        
    }
    echo '</div>';
    
    //  carousel controls
    echo '<a class="carousel-control-prev bg-secondary" href="#cartoon1" role="button" data-slide="prev">';
    echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
    echo '<span class="sr-only">Previous</span>';
    echo '</a>';
    echo '<a class="carousel-control-next bg-secondary" href="#cartoon1" role="button" data-slide="next">';
    echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
    echo '<span class="sr-only">Next</span>';
    echo '</a>';
    
    echo '</div>'; 
    echo "<h4 class='text-center'>".$blurb."</h4>";    
}

function getCartoonPartIds($pdo, $obj_id) {
    // get all the part numbers (part_id) for an object 
    // parts_list is comma delimited lists of the part_ids
    // return an array
    //      $getResult['Cartoon_title' => '', 'part_count' => 0, parts_list' => '', 'owner' => ''];
    
    $getResult = ['Cartoon_title' => 'Cartoon has no Frames', 'part_count' => 0, 'parts_list' => '0', 'seq_list' => '', 'owner' => ''];
    
    $stmt = $pdo->prepare("SELECT  o.title    AS 'title',
                                   o.mbr_id   AS 'owner',
                                   p.part_id  AS 'part_id',
                                   p.seq      AS 'seqNumber' 
                           FROM Objects AS o 
                          INNER JOIN Parts AS p ON p.obj_id = o.obj_id 
                          WHERE o.obj_id = :obj_id 
                            AND o.obj_type = 3
                            AND p.file_type = 'image'                               
                          ORDER BY p.seq");  
                           
    
    $stmt->execute(['obj_id' => $obj_id]); 
    $i          = 0; 
    $p_id_A     = []; 
    $frameCount = $stmt->rowCount();
    
    while ($row = $stmt->fetch()) {
        $part          = $row['part_id'];     
        $p_id_A[$i++]  = $row['part_id'];
        if ($i == 1) {
             $title = $row['title'];  
             $owner = $row['owner'];  
        }
    }
    if ($frameCount > 0) {
        $getResult['parts_list']    = implode(',',$p_id_A);
        $getResult['part_count']    = $frameCount; 
        $getResult['Cartoon_title'] = $title; 
        $getResult['owner']         = $owner; 
    }
    
    return $getResult;  
}

function getCartoonFrames($pdo, $obj_id) {
    // Fetch the cartoon frames for editing
    //
    // This routine will fetch a Cartoon only if it is in scope 
    // Scope defines who can the view it
    //  
    // scope: 
    //   0 None       - No viewing allowed except by the Zup administrator
    //   1 Private    - Viewing by Cartoon Owner or the Zup administrator only
    //   2 Membership - Viewing only by signed in Zup mmembers 
    //   3 Public     - viewing by everyone, signed in or not 
    //
    // return 
    //   frames - an array of frame data     
    
    $errMsg         = '';    
    $signed_in_mbr  = (isset($_SESSION['mbr_id']))   ? $_SESSION['mbr_id']   : 0; 
    $signed_in_type = (isset($_SESSION['mbr_type'])) ? $_SESSION['mbr_type'] : '';

    $stmt1 = $pdo->prepare("SELECT
                           o.mbr_id     AS 'owner',
                           o.scope_code AS 'scope',
                           p.part_id    AS 'part_id', 
                           p.seq        AS 'seq', 
                           p.seq        AS 'oSeq', 
                           ' '          AS 'frameMsg',
                           p.file_name  AS 'oFrame',
                           ' '          AS 'frame',
                           p.mime_code  AS 'mime' 
                           FROM Objects AS o 
                           INNER JOIN Parts AS p ON p.obj_id = o.obj_id 
                           WHERE o.obj_id = :obj_id 
                           AND o.obj_type = 3
                           AND p.file_type = 'image'                               
                           AND (((o.scope_code = 1) AND (o.mbr_id = :signed_in_owner)) OR
                                ((o.scope_code = 2) AND (0 <> :signed_in_mbr))         OR
                                 (o.scope_code = 3)                                    OR
                                 ('a' = :signed_in_type)) 
                           ORDER BY p.seq");  
                           
    
    $stmt1->execute(['obj_id' => $obj_id,
        'signed_in_owner' => $signed_in_mbr,
        'signed_in_mbr'   => $signed_in_mbr,
        'signed_in_type'  => $signed_in_type]); 
                         
    $i      = 0;
    $frames = [];
    while ($fRow = $stmt1->fetch()) {
        $frames[$i++] = $fRow;
    }
    return $frames; 
}
   
function buildSeqFramesForm($frames, $checked) {
        $j = 0;
        for ($j = 0; $j < count($frames); $j++) {
            $fRow = $frames[$j]; 
       
            $part_id     = $fRow['part_id']; 
            $fileName    = $fRow['frame']; 
            $oFile       = $fRow['oFrame'];
            $oldSeq      = $fRow['oSeq'];
            $seq         = $fRow['seq'];
            $frameMsg    = $fRow['frameMsg'];
            $imgPath     = 'members/mbr'.$fRow['owner'].'/image/';
            $seqId       = 'seq'.$fRow['part_id'];
            $seqName     = 'seq'.$fRow['part_id']; 
            $oldSeqName  = 'oSeq'.$fRow['part_id']; 
            $oFileName   = 'oFile'.$fRow['part_id'];
            $frameId     = 'frame'.$fRow['part_id']; 
            $frameName   = 'frame'.$fRow['part_id']; 
            $ovrwrtId    = 'ovrwrt'.$fRow['part_id']; 
            $ovrwrtName  = 'ovrwrt'.$fRow['part_id']; 
            $rowId       = 'rowId'.$fRow['part_id'];  
                       
            // frameMsg used when an upload files failes to validate or to upload. 
            echo '<h4 id="frameMsg'.$part_id.'">'.$frameMsg.'</h4>';               
            echo '<div class="form-group row align-items-center" id="'.$rowId.'">'; 
            // errMsg is set by the javascript validation when sequence number is 0 or duplicate
            echo '<div id="errMsg'.$part_id.'"></div>';      
            echo '<div class="col-sm-2"><h5 class="text-center">'.$oFile.'</h5>';
            echo '<input type="hidden" name="'.$oFileName.'" value="'.$oFile.'">'; 
            echo '</div>';
            echo '<div class="col-sm-1">';
            echo '<img src="'.$imgPath.$oFile.'" class="img-fluid img-thumbnail w-100" alt="...">';   
            echo '</div>';  
            echo '<div class="col-sm-1 bg-light">';
            echo '<label class="control-label">Sequence'; 
            echo '<input class="form-control my-red" type="number"'; 
            echo 'name="'.$seqName.'" max="2000" min="0" step="1"';   
            echo 'value="'.$seq.'">';
            echo '</label>';                           
            echo '<input type="hidden" name="'.$oldSeqName.'" value="'.$oldSeq.'">';
            echo '</div>'; 
            echo '<div class="col-sm-4">'; 
            echo '<input class="form-control" id="'.$frameId.'" type="file"'; 
            echo 'name="'.$frameName.'" accept='; 
            echo '"image/apng, image/jpg, image/jpeg, image/tif, image/tiff, image/bmp, image/png, image/gif, image/jfif, image/webp">'; 
            echo '<input type="hidden" name="MAX_FILE_SIZE" value="5000000">'; 
            echo '</div>'; 
            echo '<div class="col-sm-1 bg-light text-center form-group form-check pb-2">';
            echo '<label class="control-label" for="'.$ovrwrtId.'">Over Write</label>';
            echo '<input class="form-control" id="'.$ovrwrtId.'" type="checkbox"';        
            echo ' name="'.$ovrwrtName.'"';
            if (isset($checked[$part_id])) {
                echo ' checked="checked"'; 
            }
            echo '>'; 
            echo '</div>';
            echo '</div>';
        }      
}

function deleteVid($pdo, $obj_id) {
    //  delete a video object from the database
    //  database cascade rules should remove all associated Parts and Comment rows.
    //  
    //  return 
    //    a count of the number of object rows deleted;  

    $signed_in_mbr  = (isset($_SESSION['mbr_id']))   ? $_SESSION['mbr_id']   : NULL; 
    $signed_in_type = (isset($_SESSION['mbr_type'])) ? $_SESSION['mbr_type'] : NULL;

    if (!isset($signed_in_mbr)) {
        return 0;
    }

    $stmt1 = $pdo->prepare("DELETE FROM Objects WHERE obj_id = :obj_id");                            

    $stmt1->execute(['obj_id' => $obj_id]);


    $deleteCount = $stmt1->rowCount();
    return $deleteCount;                                                         
}

function deleteCartoon($pdo, $obj_id) {
    //  delete a Cartoon object from the database
    //  database cascade rules should remove all associated Parts and Comment rows.
    //  
    //  return 
    //    a count of the number of object rows deleted;  

    $signed_in_mbr  = (isset($_SESSION['mbr_id']))   ? $_SESSION['mbr_id']   : NULL; 
    $signed_in_type = (isset($_SESSION['mbr_type'])) ? $_SESSION['mbr_type'] : NULL;

    if (!isset($signed_in_mbr)) {
        return 0;
    }

    $stmt1 = $pdo->prepare("DELETE FROM Objects WHERE obj_id = :obj_id");                            

    $stmt1->execute(['obj_id' => $obj_id]);


    $deleteCount = $stmt1->rowCount();
    return $deleteCount;                                                         
}

function deleteVidFiles($pdo, $mbr, $video, $poster) {
    $vidPath = "./members/mbr".$mbr."/video/";
    remove_file_from_members_dir($pdo, $mbr, $video, $vidPath);
    
    if (!($poster == '')) {
        $posterPath = "./members/mbr".$mbr."/image/";
        remove_file_from_members_dir($pdo, $mbr, $poster, $posterPath);
    }
    return true;
}

function getVidFileNames($pdo, $obj_id) {
    //  Get the video file names associated with a Video object ($obj_id).

    //  Files names will be returned only if the current "Signed In" member is authorized 
    //  to deleted the files: 
    //      - the "Signed In" member must be either the owner of the files or an administrator
    //      - the "Signed In" person must be in good standing, (mbr_sts = 'a') 
    //      - the "Signed In" member imust be a contributor, trusted member, or administrator
    //        they cannot be a regular member (mbr_type = 'm')
    //
    //  Return an $result array 
    //      $result['success'] 
    //         true  - no errors   
    //         false - No video file names found  
    //      $result['video'] 
    //         the file name of the video file
    //      $result['poster'] 
    //         the files name of the poster file 
    //      $result['owner'] 
    //         the mbr_id of the person owning the files 
    //      $result['msg']
    //          a message indicating the status of the call   

    $result = ['success'=>false,'video'=>'','poster'=>'','owner'=>'','msg'=>'Unknown Error'];

    $signed_in_mbr  = (isset($_SESSION['mbr_id']))   ? $_SESSION['mbr_id']   : NULL; 
    $signed_in_type = (isset($_SESSION['mbr_type'])) ? $_SESSION['mbr_type'] : NULL;

    if (!isset($signed_in_mbr)) {
        $result['success'] = false; 
        $result['msg']     = 'You must be "Signed In" to use this transaction';
        return $result;
    }

    $stmt1 = $pdo->prepare("SELECT  p.file_name AS vFile, pp.file_name AS pFile, 
        o.mbr_id AS owner
        FROM Parts AS p
        INNER JOIN Objects AS o 
        ON o.obj_id  = p.obj_id
        LEFT OUTER JOIN Parts AS pp
        ON (p.obj_id  = pp.obj_id and pp.file_type = 'poster')    
        WHERE p.obj_id   = :obj_id 
        AND o.obj_type = 4
        AND ((EXISTS  
        (SELECT mm.mbr_id from Members AS mm
        INNER JOIN Objects AS oo
        ON mm.mbr_id = oo.mbr_id
        WHERE oo.obj_id = o.obj_id   
        AND mm.mbr_sts = 'a' 
        AND mm.mbr_id  = :signed_in_mbr
        AND mm.mbr_type <> 'm'))
        OR
    (:signed_in_type = 'a'))");


    $stmt1->execute(['obj_id' => $obj_id,
        'signed_in_mbr' => $signed_in_mbr,
        'signed_in_type' => $signed_in_type]);

    $row = $stmt1->fetch();
    if ($row) {
        $result['success'] = true;
        $result['video']   = $row['vFile'];
        $result['poster']  = (isset($row['pFile'])) ? $row['pFile'] : '';
        $result['owner']   = $row['owner']; 
        $result['msg']     = 'File names succesfully found';
    } else {
        $result['success'] = false;
        $result['msg']     = 'There are no Video files associated with this object that you are authorized to delete'; 
    }

    return $result;
} 

function deleteFrame($pdo, $part) {
    //  delete a frame from the Parts table
    //  This routine assumes the frames image file in the members directory has already been delt with
    //  
    //  return 
    //    The number of rows deleted; 
    
    $signed_in_mbr  = (isset($_SESSION['mbr_id']))   ? $_SESSION['mbr_id']   : NULL; 
    $signed_in_type = (isset($_SESSION['mbr_type'])) ? $_SESSION['mbr_type'] : ' ';

    if (!isset($signed_in_mbr)) {
        return 0;       // you must be a signed in member to delete a parts record
    }
    
    if (isset($signed_in_type)) { 
        if ($signed_in_type == 'm') {
            return 0;   // you must be more than a regular member to delete parts record
        }  
    }
    
    $stmt = $pdo->prepare("DELETE FROM Parts WHERE part_id = :part_id");                            
    $stmt->execute(['part_id' => $part]);

    $deleteCount = $stmt->rowCount();
    return $deleteCount;       
}

function updateFrame($pdo, $fRcd) {
    // update the parts record with the new Frame data
    // This routine assumes the frames image file in the members directory has already been delt with
    //
    // $fRcd is a associative array of the current data in the parts table 
    //  
    //  return 
    //    The number of rows affected by the update; 
    
    $signed_in_mbr  = (isset($_SESSION['mbr_id']))   ? $_SESSION['mbr_id']   : NULL; 
    $signed_in_type = (isset($_SESSION['mbr_type'])) ? $_SESSION['mbr_type'] : ' ';

    if (!isset($signed_in_mbr)) {
        return 0;       // you must be a signed in member to update a parts record
    }
    
    if (isset($signed_in_type)) { 
        if ($signed_in_type == 'm') {
            return 0;   // you must be more than a regular member to update parts record
        }  
    }
    
    $part_id   = $fRcd['part_id'];
    $file_name = $fRcd['frame']; 
    $mime      = $fRcd['mime']; 
    $seq       = $fRcd['seq']; 
    
    $stmt = $pdo->prepare("UPDATE Parts
                              SET file_name = :file_name,
                                  mime_code = :mime, 
                                  seq       = :seq
                            WHERE part_id = :part_id"); 
                                                       
    $stmt->execute(['part_id'   => $part_id, 
                    'file_name' => $file_name,
                    'mime'      => $mime,      
                    'seq'       => $seq]); 
                    
    $updtCount = $stmt->rowCount();
    return $updtCount;       
}

function getCartoonFileNames($pdo, $obj_id) {
    //  Get the video file names associated with a Cartoon object ($obj_id).

    //  Files names will be returned only if the current "Signed In" member is authorized 
    //  to deleted the files: 
    //      - the "Signed In" member must be either the owner of the files or an administrator
    //      - the "Signed In" person must be in good standing, (mbr_sts = 'a') 
    //      - the "Signed In" member imust be a contributor, trusted member, or administrator
    //        they cannot be a regular member (mbr_type = 'm')
    //
    //  Return an $result array 
    //      $result['success'] 
    //         true  - no errors   
    //         false - No Cartoon file names found  
    //      $result['files[]'] 
    //         An array of all Cartoon files associated whith this Cartoon
    //      $result['owner'] 
    //         the mbr_id of the person owning the files 
    //      $result['msg']
    //          a message indicating the status of the call   

    $f = [];
    $result = ['success'=>false,'files'=>$f,'owner'=>'','msg'=>'Unknown Error'];
                                            
    $signed_in_mbr  = (isset($_SESSION['mbr_id']))   ? $_SESSION['mbr_id']   : NULL; 
    $signed_in_type = (isset($_SESSION['mbr_type'])) ? $_SESSION['mbr_type'] : NULL;

    if (!isset($signed_in_mbr)) {
        $result['success'] = false; 
        $result['msg']     = 'You must be "Signed In" to use this transaction';
        return $result;
    }

    $stmt1 = $pdo->prepare("SELECT
            p.file_name AS cFile, 
            o.mbr_id AS owner
         FROM Parts AS p
        INNER JOIN Objects AS o 
           ON o.obj_id  = p.obj_id
        LEFT OUTER JOIN Parts AS pp
          ON (p.obj_id  = pp.obj_id and pp.file_type = 'poster')    
        WHERE p.obj_id  = :obj_id 
         AND o.obj_type = 3 
         AND ((EXISTS  
                (SELECT mm.mbr_id from Members AS mm
                    INNER JOIN Objects AS oo
                    ON mm.mbr_id = oo.mbr_id
                    WHERE oo.obj_id = o.obj_id   
                      AND mm.mbr_sts = 'a' 
                      AND mm.mbr_id  = :signed_in_mbr
                      AND mm.mbr_type <> 'm'))
                    OR
                      (:signed_in_type = 'a'))");

    $stmt1->execute(['obj_id'         => $obj_id,
                     'signed_in_mbr'  => $signed_in_mbr,
                     'signed_in_type' => $signed_in_type]);
                     
    $fileCount = $stmt1->rowCount();
    if ($fileCount == 0) {
        $result['success'] = false;
        $result['msg']     = 'There are no Cartoon files associated with this object that you are authorized to delete';     
    } else { 
        $result['success'] = true;
        $result['msg']     = $fileCount.' Cartoon files found';
        $i = 0;
        while ($cartoonRow = $stmt1->fetch()) {
            if ($i == 0) { 
                $result['owner'] = $cartoonRow['owner']; 
            }
            $f[$i++] = $cartoonRow['cFile']; 
        }
        $result['files'] = $f;   
    }
    return $result;
}  

function deleteCartoonFiles($pdo, $mbr, $files) {
    $cartoonPath = "./members/mbr".$mbr."/image/";
    foreach($files as $f) {
        remove_file_from_members_dir($pdo, $mbr, $f, $cartoonPath);
    }
    return true;
}  

function deleteWholeCartoon($pdo, $obj_id) { 
    // delete the entire cartoon including the
    //    objecr record
    //    parts records 
    //    and the image files in the members directory 
    //
    //  return a count of the cartoons deleted;
    
    $cartoonDltCount = 0; 
    $rslt  = getCartoonFileNames($pdo, $obj_id);
    $files = $rslt['files'];
    $sts   = $rslt['success']; 
    $owner = $rslt['owner']; 
    $msg   = $rslt['msg'];
  
    if ($sts) { 
       // get rid of the cartoon files in the members directory 
       if (deleteCartoonFiles($pdo,$owner,$files)) {  // remove cartoon files from the members directory  
           $count = deleteCartoon($pdo, $obj_id);     // remove the cartoon object from the database 
       }    
    $msg = "$count Cartoon Objects were removed from the database"; 
    }
    
    return $cartoonDltCount;     
}

function getVideoDtls($pdo, $obj) {
    // get the video details for the editForm 
    // 
    // return a $dtls array
    //   $dtls ['success']       - TRUE if object found, FALSE if not
    //   $dtls['mbr_id']         - the Video owner Id 
    //   $dtls['oType']          - the Video owner type (ie, member, contibutor, trusted, or administrator)     
    //   $dtls['oSts']           - the Video owner status (ie. active or on Hold)
    //   $dtls['title']          - title of the Video
    //   $dtls['scope']          - Video viewing scope (ie. none, private, membership, or public)
    //   $dtls['description']    - a tect desciption of the Video 
    //   $dtls['oldVid']         - the file name of the Video  
    //   $dtls['oldPoster']      - the file name of the poster image (NULL if there is no poster)
    //   $dtls['poster_part_id'] - row identifier for the poster file in the parts table (NULL if there is no poster)
    //   $dtls['video_part_id']  - row identifier for the video file in the parts table
    $dtls = ['found' => false, 'mbr_id' => '', 'oType' => '', 'oSts' => '', 'title' => '',
        'scope' => 0, 'description' => '',
        'oldVid' => '', 'video_part_id' => '', 'oldPoster' => NULL, 'poster_part_id' => '']; 

    $stmt1 = $pdo->prepare("SELECT
        o.title           AS 'title',
        o.obj_description AS 'description',
        o.scope_code      AS 'scope',     
        p.file_name       AS 'vFileName',
        p.part_id         AS 'video_part_id',
        m.mbr_id          AS 'mbr_id',
        m.mbr_type        AS 'owner_type',
        m.mbr_sts         AS 'owner_sts', 
        pp.file_name      AS 'pFileName',
        pp.part_id        AS 'poster_part_id'
        FROM Members AS m 
        INNER JOIN Objects AS o 
        ON m.mbr_id  = o.mbr_id
        INNER JOIN Parts   AS p
        ON o.obj_id  = p.obj_id
        LEFT OUTER JOIN Parts AS pp
        ON (o.obj_id  = pp.obj_id and pp.file_type = 'poster')          
        WHERE    p.file_type = 'video'               
        and  o.obj_type  = 4           
    and  o.obj_id    = :obj_id");       

    $stmt1->execute(['obj_id' => $obj]);


    $row = $stmt1->fetch(PDO::FETCH_ASSOC);

    if ($row) { 
        $dtls['mbr_id']         = $row['mbr_id'];
        $dtls['oType']          = $row['owner_type'];
        $dtls['oSts']           = $row['owner_sts']; 
        $dtls['title']          = $row['title'];
        $dtls['scope']          = $row['scope'];
        $dtls['description']    = $row['description'];
        $dtls['oldVid']         = $row['vFileName'];
        $dtls['video_part_id']  = $row['video_part_id'];
        $dtls['oldPoster']      = $row['pFileName'];
        $dtls['poster_part_id'] = $row['poster_part_id'];

        $dtls['found']          = true;
    }

    return $dtls;  
}

function getObjDtls($pdo, $obj) {
    // get the object title, type, scope, description, and last update
    // plus the current date
    
    $stmt = $pdo->prepare("SELECT M.user_id,
                                  O.mbr_id,  
                                  O.title,
                                  O.obj_type, 
                                  T.description AS 'type',
                                  O.scope_code,
                                  O.obj_description,
                                  O.lst_updt,
                                  CURRENT_DATE AS 'now' 
                             FROM objects AS O, 
                                  members AS M,
                                  otype   AS T 
                            WHERE O.obj_id    = :obj 
                              AND O.mbr_id    = M.mbr_id
                              AND O.obj_type  = T.obj_type
                              AND M.mbr_sts   = 'a'");

    $stmt->execute(['obj' => $obj]); 
    
    $objDtls = $stmt->fetch();
    
    return $objDtls;                         
}

function insertComment($pdo, $obj, $user_id, $com_sts, $com_text) {
    $com_text    = clean_input($com_text);
    $result = ['success' => false, 'msg' => 'Insert Failed', 'id' => NULL]; 

    $stmt1 = $pdo->prepare('INSERT INTO comments
        (obj_id,
         com_by,
         com_sts,
         com_text)
        VALUES (:obj_id, :com_by, :com_sts, :com_text)');
    $stmt1->execute(['obj_id'   => $obj, 
                     'com_by'   => $user_id,
                     'com_sts'  => $com_sts,
                     'com_text' => $com_text]);
    // get the new comment com_id
    $com_id = $pdo->lastInsertId();
    $insertCount = $stmt1->rowCount();
    if ($insertCount ==  1) {  
        $result['success'] = true;
        $result['msg']     = 'New Comment successfuly inserted';
        $result['id']      = $com_id;
    }  else  {
        $result['success'] = false;
        $result['msg']     = 'Insert new Comment failed'; 
        $result['id']      = NULL;
    }
    
    return $result; 
}

function remove_file_from_members_dir($pdo, $mbr, $file, $path) {
    //  remove a file from the members directory  
    //  return an array
    //    $remove['success']  - true  if file was removed or did not need to be removed  
    //                        - false if not removed due to an error 
    //    $remove['msg']      - a text message  

    $remove = ['success' => false, 'msg' => '']; 
    $filePath = $path.$file;
    
    // Count the number of times the file is used by this member     
    // delete the file if it exists and
    // is not in use by another of the members objects 
    $countFileUse = countFileUses($pdo, $file, $mbr);
    if ($countFileUse > 1) {
        $remove['success'] = true;
        $remove['msg']     = "$file needed by other objects, file KEPT"; 
    } else {
        if (file_exists($filePath)) {
             clearstatcache();    
             $remove['success'] = unlink($filePath);
        }   
    } 
   
    $remove['msg'] = ($remove['success']) ? '' : 'Failed to remove file';  
    return $remove;
}

function countFileUses($pdo, $file, $mbr) {
    //  count the number of times a member re-uses the same file in different objects
    $uses       = 0;
    $reUsedFile = ''; 
    
    $stmt = $pdo->prepare("SELECT
         count(*) AS 'uses' , p.file_name AS 'file'
         FROM Objects as o, 
              Parts   as p
         WHERE o.obj_id = p.obj_id
         AND   o.mbr_id = :mbr_id  
         AND   p.file_name = :file_name
         AND EXISTS  
            (SELECT * 
             FROM Objects AS oo,
                  Parts   AS pp 
             WHERE oo.obj_id = pp.obj_id   
              AND pp.file_name = p.file_name
              AND pp.mime_code = p.mime_code
              AND pp.obj_id <> p.obj_id 
              AND oo.mbr_id = o.mbr_id)
         GROUP BY p.file_name"); 
         
    $stmt->execute(['file_name' => $file, 
                    'mbr_id'    => $mbr]);
    
    $row = $stmt->fetch();
    if ($row) {
        $reUsedFile = $row['file'];
        $uses       = $row['uses'];
    }                
                   
    return $uses;                      
}

function delete_part($pdo, $part_id) {
    //  delete the Parts record for the Video Poster   
    //  return an array
    //    $remove['success']  - true  if Poster Parts record was deleted  
    //                        - false if not  
    //    $remove['msg']      - a text message  

    $remove = ['success' => false, 'msg' => '']; 

    $stmt1 = $pdo->prepare("DELETE FROM Parts 
    WHERE part_id = :part_id");        

    $stmt1->execute(['part_id' => $part_id]);
    $deleteCount = $stmt1->rowCount();
    if ($deleteCount == 0) {
        $remove['success'] = false;
        $remove['msg']     = 'Part('.$part_id.') was not deleted';
    } else {
        $remove['success'] = true;
        $remove['msg']     = 'Part deleted';
    }
    return $remove; 
}

function insert_new_part($pdo, $fileName, $mime, $seq, $file_type, $obj_id) {
    // insert a new part for the Video 
    // returns an array 
    //    $result['success]'  - True if successfull , false if not
    //    $result['msg']      - A text message
    //    $result['id']       - The id for row in the parts table 

    $result = ['success' => false, 'msg' => '', 'id' => 0];
    $fileName    = clean_input($fileName);
    $mime        = clean_input($mime);
    $seq         = clean_input($seq);
    $file_type   = clean_input($file_type);

    $stmt1 = $pdo->prepare('INSERT INTO Parts
        (obj_id, file_name, file_type, mime_code, seq)
        VALUES (:obj_id, :fileName, :file_type, :mime_code, :seq)');
    try {    
        $stmt1->execute(
           ['obj_id'    => $obj_id, 
            'fileName'  => $fileName,
            'file_type' => $file_type,
            'mime_code' => $mime,
            'seq'       => $seq]);
    } catch(PDOException $e) {
        $dupKeyErr = "Integrity constraint violation: 1062 Duplicate entry";
        if (strpos($e->getMessage(), $dupKeyErr) !== FALSE) {
            $result['msg'] = "Sequence $seq is in use. Please select a different sequence for this file";
        } else {
            $result['msg'] = $e->getMessage(); 
            throw $e;
        }
        
        $result['success'] = false; 
        $result['id']      = NULL;     
        return $result;
        
    }    
    // get the new part_id
    $id = $pdo->lastInsertId();
    $insertCount = $stmt1->rowCount();
    if ($insertCount > 0) {  
        $result['success'] = true;
        $result['id']      = $id;
    }  else  {
        $result['success'] = false;
        $result['msg']     = 'Insert new Part failed'; 
        $result['id']      = NULL;
    }
    return $result; 
}   

function update_part($pdo, $part_id, $fileName, $mime) {
    // insert a new part for the Video 
    // returns an array 
    //    $result['success]'  - True if successfull , false if not
    //    $result['msg']      - A text message
    //    $result['id']       - The id for row in the parts table 

    $result = ['success' => false, 'msg' => '', 'id' => $part_id];
    $fileName    = clean_input($fileName);
    $mime        = clean_input($mime); 

    $stmt1 = $pdo->prepare('UPDATE Parts
        SET file_name = :fileName, mime_code = :mime, lst_updt = CURRENT_TIMESTAMP
    WHERE part_id = :part_id'); 
    $stmt1->execute(['part_id'   => $part_id, 
        'fileName'  => $fileName,
        'mime'      => $mime]);
    $updatetCount = $stmt1->rowCount();
    if ($updatetCount > 0) { 
        $result['success'] = true;
    }  else  {
        $result['success'] = false;
        $result['msg']     = 'Update failed';
    }
    return $result; 
}

function update_object($pdo, $obj_id, $title, $scope, $description) {
    // appy update to an Object  
    // returns an array 
    //    $result['success]'  - True if successfull , false if not
    //    $result['msg']      - A text message

    $result = ['success' => false, 'msg' => ''];
    $description = clean_input($description);
    $title       = clean_input($title);
    $scope       = clean_input($scope); 

    $stmt1 = $pdo->prepare('UPDATE Objects
        SET title = :title, scope_code = :scope, obj_description = :description, lst_updt = CURRENT_TIMESTAMP
    WHERE obj_id = :obj_id'); 
    $stmt1->execute(['obj_id'      => $obj_id, 
                     'title'       => $title,
                     'scope'       => $scope,
                     'description' => $description]);
    $updatetCount = $stmt1->rowCount();
    if ($updatetCount > 0) { 
        $result['success'] = true;
        $result['msg']     = $title.' has been successfully updated'; 
    }  else  {
        $result['success'] = false;
        $result['msg']     = $title.' update failed';
    }
    return $result; 

}

function updateVideo($pdo, $newDtls) { 
    $obj_id         = $newDtls['obj_id'];      
    $title          = $newDtls['title'];
    $scope          = $newDtls['scope'];
    $description    = $newDtls['description'];
    $oldVid         = $newDtls['oldVid']; 
    $oldPoster      = $newDtls['oldPoster'];
    $video_part_id  = $newDtls['video_part_id'];
    $poster_part_id = $newDtls['poster_part_id'];
    $newVid         = $newDtls['newVid'];
    $vMime          = $newDtls['vMime']; 
    $newPoster      = $newDtls['newPoster'];
    $pMime          = $newDtls['pMime']; 

    $newVid         = ($newVid == '')         ? NULL : $newVid;
    $newPoster      = ($newPoster == '')      ? NULL : $newPoster;
    $poster_part_id = ($poster_part_id == '') ? NULL : $poster_part_id;
    $video_part_id  = ($video_part_id == '')  ? NULL : $video_part_id;
    
    $seq            = 1;

    $continue       = true;

    if (isset($newPoster)) {
        if (isset($poster_part_id)) {
            $result = update_part($pdo, $poster_part_id, $newPoster, $pMime);
        } else {
            $result = insert_new_part($pdo, $newPoster, $pMime, $seq, 'poster', $obj_id);
        }
        if ($result['success']) { 
            $poster_part_id = $result['id'];
            $oldPoster = $newPoster; 
        } else {
            $pMsg = $result['msg'];
            $continue = false;
        }    
    }

    if (isset($newVid)) {
        $result = update_part($pdo, $video_part_id, $newVid, $vMime);
        if ($result['success']) { 
            $oldVid = $newVid; 
        } else {
            $vMsg = $result['msg'];
            $continue = false;
        }       
    }          

    if ($continue) {
        $result = update_object($pdo, $obj_id, $title, $scope, $description);
        if ($result['success']) {
            $msg     = $result['msg'];
        } else {
            $contine = false; 
            $msg     = $result['msg'];
        }
    }
    if ($continue) {
        $msg = 'Video '.$title.' has been Updated'; 
    }  else {
        $msg = 'Update failed'; 
    }

    return $msg;
}  

function getMbrList($pdo) {
    // get a list of all Members 
    // output a formatted list 

    $stmt1 = $pdo->prepare("SELECT
        m.mbr_id          AS 'mbr_id',
        m.user_id         AS 'user_id',
        m.name            AS 'name',     
        m.email           AS 'email',
        m.signup_dt       AS 'signup_dt',
        m.mbr_type        AS 'type',
        m.mbr_sts         AS 'sts', 
        m.lst_updt        AS 'lst_updt'
        FROM Members AS m
    WHERE m.user_id <> 'Guest'");           

    $stmt1->execute();

    while ($result = $stmt1->fetch(PDO::FETCH_ASSOC)) {     
        $edit        = "editMember(".$result['mbr_id'].")";   
        echo "<tr id=rowId".$result['mbr_id']." onclick='$edit' onmouseover='highlight(this)' onmouseout='normal(this)'>"; 
        echo "<th scope='row'>".$result['user_id']."</th>";
        echo "<td>".$result['mbr_id']."</td>";
        echo "<td>".$result['name']."</td>";
        echo "<td>".$result['email']."</td>";
        echo "<td>".$result['sts']."</td>";
        echo "<td>".$result['type']."</td>";
        echo "<td>".$result['signup_dt']."</td>";
        echo "<td>".$result['lst_updt']."</td>";
        echo "</tr>";                                                                      
    }
    echo "<tr><td colspan='8'></td></tr>";
}

function get_A_member($pdo, $mbr_id) {
    $stmt1 = $pdo->prepare("SELECT
        user_id, name, email, signup_dt,  mbr_type,  mbr_sts,  lst_updt 
        FROM Members          
    WHERE    mbr_id = :mbr_id");               

    $stmt1->execute(['mbr_id' => $mbr_id]);
    $mbr_rcd = $stmt1->fetch(PDO::FETCH_ASSOC);
    return $mbr_rcd; 
}

function get_user_record($pdo, $user_id) {
    $userCount = 0; 
    $userDtls = ['ok' => false, 'msg' => '', 'mbr_id' => NULL, 'name' => NULL, 'email' => NULL, 'user_id' => $user_id]; 
    $stmt = $pdo->prepare("SELECT
        mbr_id, name, email, CURRENT_DATE() as 'today'
        FROM Members          
       WHERE user_id = :user_id");               

    $stmt->execute(['user_id' => $user_id]);
    $mbr_rcd = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $userCount = $stmt->rowCount();
    if ($userCount != 1) {
         $userDtls['msg'] = 'User Id not valid, Not found on file'; 
    } else {
         $userDtls['msg']      = 'An email will be sent to '.$mbr_rcd['name']; 
         $userDtls['mbr_id']   = $mbr_rcd['mbr_id'];  
         $userDtls['name']     = $mbr_rcd['name'];  
         $userDtls['email']    = $mbr_rcd['email'];  
         $userDtls['today']    = $mbr_rcd['today'];
         $userDtls['ok']       = true;
    }
    
    return $userDtls; 
}

function delete_u_MbrRcd($pdo, $mbr_id) {
    // delete a member record if it has a status of 'u' (undefined)
    $deleteCount = 0;
    $mbr_id      = clean_input($mbr_id);

    $stmt = $pdo->prepare("DELETE FROM Members
                            WHERE mbr_id  = :mbr_id
                              AND mbr_sts = 'u'"); 
    $stmt->execute(['mbr_id' => $mbr_id]); 
    $deleteCount = $stmt->rowCount();
    if ($deleteCount != 1) {
        $err = $mbrCount.' records for unverified member ID - '.$mbr_id.' - deleted';
        trigger_error($err, E_USER_NOTICE);
    }
    return $deleteCount; 
}

function updateMbrRcd($pdo, $mbrRecord) {
    $editMemberMsg = 'Update Member record failed'; 

    $mbr_id    = clean_input($mbrRecord['mbr_id']);
    $mbr_name  = clean_input($mbrRecord['mbr_name']);
    $email     = clean_input($mbrRecord['email']);
    $mbr_type  = clean_input($mbrRecord['mbr_type']);
    $mbr_sts   = clean_input($mbrRecord['mbr_sts']);
    
    $today     = new DateTime();

    if (isset($mbrRecord['password'])) {
        // include the password
        $password = clean_input($mbrRecord['password']);
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE Members
            SET password = :hash,
                name     = :mbr_name,
                email    = :email,
                mbr_type = :mbr_type,
                mbr_sts  = :mbr_sts,
                lst_updt = CURRENT_TIMESTAMP
            WHERE mbr_id = :mbr_id');
        $stmt->execute(
           ['mbr_id'      => $mbr_id, 
            'hash'        => $hash,
            'mbr_name'    => $mbr_name,
            'email'       => $email,
            'mbr_type'    => $mbr_type,
            'mbr_sts'     => $mbr_sts]);  
        $editMemberMsg = 'Members record updated, Password was re-set';      
    } else {
        // do not include password 
        $stmt = $pdo->prepare('UPDATE Members
            SET  name     = :mbr_name,
                 email    = :email,
                 mbr_type = :mbr_type,
                 mbr_sts  = :mbr_sts,
                 lst_updt = CURRENT_TIMESTAMP
            WHERE mbr_id  = :mbr_id');
        $stmt->execute(
           ['mbr_id'      => $mbr_id, 
            'mbr_name'    => $mbr_name,
            'email'       => $email,
            'mbr_type'    => $mbr_type,
            'mbr_sts'     => $mbr_sts]); 
        $editMemberMsg = 'Members record updated';     
    }
  
    $updateCount = $stmt->rowCount();
    if ($updateCount != 1) {
        $editMemberMsg = 'Update failed'; 
    }

    return $editMemberMsg; 
}

function update_mbr_hash($pdo, $mbr_id) {
    // return the new hash code
    // Update members hash 
    $today = new DateTime(); 
    $hash  = password_hash($today->getTimestamp(), PASSWORD_DEFAULT); 
    $stmt  = $pdo->prepare('UPDATE Members
        SET hash     = :hash,
            lst_updt = CURRENT_TIMESTAMP
        WHERE mbr_id = :mbr_id');
    $stmt->execute(
       ['mbr_id' => $mbr_id, 
        'hash'   => $hash]);  
        
    $updateCount = $stmt->rowCount();
    if ($updateCount != 1) {
        return false; 
    }
    return $hash;
}

function updateMbrProfile($pdo, $mbrRecord) {
    $editProfileMsg = 'Update Member Profile  failed'; 

    $mbr_id    = clean_input($mbrRecord['mbr_id']);
    $mbr_name  = clean_input($mbrRecord['mbr_name']);
    $email     = clean_input($mbrRecord['email']);

    $stmt1 = $pdo->prepare('UPDATE Members
        SET name = :mbr_name, email = :email, lst_updt = CURRENT_TIMESTAMP
    WHERE mbr_id = :mbr_id'); 
    $stmt1->execute(['mbr_id'      => $mbr_id, 
                     'mbr_name'    => $mbr_name,
                     'email'       => $email,]);
    $updatetCount = $stmt1->rowCount();
    $editProfileMsg = ($updatetCount > 0) ? 'Member Profile has been updated' : 'Update failed'; 

    return $editProfileMsg; 
}

function updateMbrSts($pdo, $mbr_id, $sts) {
    $mbr_id    = clean_input($mbr_id);
    $sts       = clean_input($sts);

    $stmt = $pdo->prepare('UPDATE Members
          SET mbr_sts = :sts, lst_updt = CURRENT_TIMESTAMP, hash = NULL
        WHERE mbr_id = :mbr_id'); 
    $stmt->execute(['mbr_id'      => $mbr_id, 
                     'sts'        => $sts]);
    $updateCount = $stmt->rowCount();
    if ($updateCount == 1)
        return true;

    return false; 
}

function get_userIds($pdo, $email) {
    // return an array 
    //   ok   - true or false  
    //   msg  - text message describing the results
    //   uids - an array of userid's associated with email address 
    
    $dtls      = ['ok' => false, 'msg' => '', 'uids' => []];
    $in_email  = clean_input($email);
    $uids      = null; 
    
    $stmt = $pdo->prepare("SELECT user_id
                             FROM Members
                            WHERE email = :email");           

    $stmt->execute(['email' => $in_email]);
    
    $uidCount = $stmt->rowCount();
    if ($uidCount < 1) {
        $dtls['ok']   = false; 
        $dtls['msg']  = 'No accounts asscociated with '.$in_email.' address found on file'; 
        $dlts['uids'] = NULL; 
        return $dtls; 
    }
    $i = 0;
    while ($users = $stmt->fetch()) {
        $uids[$i++] = $users['user_id']; 
    }
  
    $dtls['ok']   = true; 
    $dtls['msg']  = $uidCount.' accounts found asscociated with '.$in_email;  
    $dtls['uids'] = $uids; 
    
    return $dtls; 
}
?>
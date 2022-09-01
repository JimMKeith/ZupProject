<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;
  
  function zupMailOut($to, $sender_name, $subject, $htmlBody, $attach) {
// -----------------------------------------------------------------------
// send an email from Zup to a Zup member
//  to          - is the recients address
//  sender_name - Persons name sendin the email
//  subject     - Subject of the email
//  htmlBody    - Body of the email in HTML format
//  attach      - email attachment

    $altBody = strip_tags($htmlBody);  // body of the email in plain text

    require 'vendor/autoload.php';

    //Create a new PHPMailer instance
    $mail = new PHPMailer;
    try {  
        //Tell PHPMailer to use SMTP
        $mail->isSMTP();

        // -------------- set up debugging -------------------
        //Enable SMTP debugging
        //  SMTP::DEBUG_OFF = off (for production use)
        //  SMTP::DEBUG_CLIENT = client messages
        //  SMTP::DEBUG_SERVER = client and server messages
        // -----------  end set up debugging -----------------

        // -----------------------------------------------------
        // uncomment the following statement to enable debugging   
        //   $mail->SMTPDebug = SMTP::DEBUG_SERVER;  
        // -----------------------------------------------------
     
        // --------------- server settinss ------------------
        //Set the hostname of the mail server
        $mail->Host = 'cp311.zenutech.com';
        $mail->Port = 465;
        //Set the encryption mechanism to use - STARTTLS or SMTPS
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = 'zup@programmingbc.com';
        //Password to use for SMTP authentication
        $mail->Password = '13DoctorZ13';
        //Set who the message is to be sent from
        $mail->setFrom('zup@programmingbc.com', 'Jim Keith');
        //Set an alternative reply-to address
        $mail->addReplyTo('jimkeith808@hotmail.com', 'First Last');
        //Set who the message is to be sent to
        $mail->addAddress("$to", "$sender_name");

        //Set the subject line
        $mail->Subject = "$subject";

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        //$mail->msgHTML(file_get_contents("$htmlBody"), __DIR__);
        $mail->msgHTML($htmlBody, __DIR__);

        //Replace the plain text body with one created manually
        $mail->AltBody = "$altBody";

        //Attach an image file if it exists
        echo "======================================================<br>";
        echo "=============== $attach ================<br>";
        echo "======================================================<br>";
        if (is_file($attach)) { 
            $mail->AddAttachment($attach);
        } else {
            echo "======================================================<br>";
            echo "=============== $attach is not a file ================<br>";
            echo "======================================================<br>";
            $err = $attach.'  is not a file ';
            trigger_error($err, E_USER_NOTICE);
        }
        
        //send the message, check for errors
        $mail->send();
        echo 'Message sent!';
        return true;
    } 
    catch (Exception $e) {  
        echo 'Mailer Error: '. $mail->ErrorInfo;
        return false;
    }      
// ----------------- End zupMailOut --------------------------------------    
}
?>
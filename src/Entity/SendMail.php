<?php
  namespace App\Entity;

  //Import PHPMailer classes into the global namespace
  //These must be at the top of your script, not inside a function
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;
  use PHPMailer\PHPMailer\Exception;
  class SendMail {

    /**
     * 
     *  @var $mail -
     *    global variable
     */
    public $mail;

  /**
     * Constructor to initialise global variable.
     * 
     *  @param string $mail-
     *    Mail id to be checked.
     */
    function __construct(string $mail){
      $this->mail = $mail;
    }


    function mailer() {

      //Create an instance; passing `true` enables exceptions
      $mail = new PHPMailer(TRUE);

      try {
        //Server settings.
        //Enable verbose debug output.
        //Send using SMTP.                  
        $mail->isSMTP();         
        //Set the SMTP server to send through.                                  
        $mail->Host       = 'smtp.gmail.com';            
        //Enable SMTP authentication.     
        $mail->SMTPAuth   = TRUE;                  
        //SMTP username                 
        $mail->Username   = 'aritri.dey@innoraft.com';             
        //SMTP password        
        $mail->Password   = 'nwihefeexabanjyt';   
        //Enable implicit TLS encryption.                         
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
        $mail->SMTPSecure = 'tls';
        //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS.
        $mail->Port       = 587;                                    
        /* port 465 can also be used insted of 587, but it is less secure.*/ 
                                          
        //Recipients
        $mail->setFrom('aritri.dey@innoraft.com', 'Aritri Dey');
        //Add a recipient.
        $mail->addAddress($this->mail);     

        //Content
        $mail->isHTML(TRUE);   
        //Set email format to HTML.                               
        $mail->Subject = 'Reset Password mail';
        $mail->Body    = '<b>Hello </b>' . $this->mail. '<br> Link to reset password-<br>http://127.0.0.1:8000/resetPasswordForm';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        $flag = TRUE;
      } 
      catch (Exception $e) {
        $flag = FALSE;
        // echo "<br>Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      }

      return $flag;

    }

  }
?>
<?php
  namespace App\Services;

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  /**
   * Class to send mail to user.
   */
  class SendMail 
  {

    /**
     * 
     *  @var $mail -
     *    global variable
     */
    private $mail;

  /**
     * Constructor to initialise global variable.
     * 
     *  @param string $mail-
     *    Mail id to be checked.
     */
    public function __construct(string $mail){
      $this->mail = $mail;
    }

    /**
     * Function to check for valid email id and send mail to user.
     */
    public function mailer() {

      // Create an instance; passing `true` enables exceptions.
      $mail = new PHPMailer(TRUE);

      try {
        // Server settings.
        // Enable verbose debug output.
        // Send using SMTP.                  
        $mail->isSMTP();                                         
        $mail->Host       = 'smtp.gmail.com';                 
        $mail->SMTPAuth   = TRUE;                                  
        $mail->Username   = $_ENV['MY_MAIL'];                 
        $mail->Password   = $_ENV['MAIL_PASSWORD'];                           
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
        $mail->SMTPSecure = 'tls';
        // TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS.
        $mail->Port       = 587;                                    
        $mail->setFrom('aritri.dey@innoraft.com', 'Aritri Dey');
        $mail->addAddress($this->mail);     
        $mail->isHTML(TRUE);                                 
        $mail->Subject = 'Reset Password mail';
        $mail->Body    = '<b>Hello </b>' . $this->mail. '<br> Link to reset password-<br>http://127.0.0.1:8000/newpassword';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        return TRUE;
      } 
      catch (Exception $e) {
        return FALSE;
      }
    }
  }

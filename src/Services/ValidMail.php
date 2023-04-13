<?php 
namespace App\Services;

use App\Services\CheckMail;

/**
 * This class is used to check validity of mail id.
 */
class ValidMail 
{

  /**
   *  @var string $mail
   *    Stores the mail to be checked.
   */
  private $mail;  

  /**
   * Constructor to initialise global variable.
   *
   *  @param string $mail
   *    Stroes mail to be checked.
   */
  public function __construct(string $mail) {
    $this->mail = $mail;
  }

  /**
   * Function to check validity of mail ID by calling CheckMail class.
   * 
   *  @return string
   *    Returns error message if validation is not successful.
   */
  public function validMail() {
    $mailObj = new CheckMail($this->mail);
    $flag = $mailObj->checkMail();
    if (!$flag) {
      return "Enter a valid email id.";
    }
    return "";
  }
}

?>

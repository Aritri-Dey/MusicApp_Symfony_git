<?php 
namespace App\Services;

use App\Services\CheckMail;

/**
 * This class is used to validate all form fields in the update form.
 */
class ValidMail  {

  /**
   * @var string $ mail
   *    Stores the mail to be checked.
   */
  private $mail;
  function __construct(string $mail) {
    $this->mail = $mail;
  }

  /**
   * Function to check validity of mail ID by calling CheckMail class.
   * 
   *  @return string
   *    Returns error message if validation is not successful.
   */
  function validMail() {
    $mailObj = new CheckMail($this->mail);
      $flag = $mailObj->check();
      if ($flag == FALSE) {
        return "Enter a valid email id.";
      }
      return "";
  }
}
?>

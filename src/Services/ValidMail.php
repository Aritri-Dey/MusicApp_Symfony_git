<?php 
namespace App\Services;

use App\Services\CheckMail;

/**
 * This class is used to validate all form fields in the update form.
 */
class ValidMail  {
  private $mail;
  function __construct(string $mail) {
    $this->mail = $mail;
  }
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

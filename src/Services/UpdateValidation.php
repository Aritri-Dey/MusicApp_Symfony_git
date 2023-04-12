<?php 
namespace App\Services;

use Respect\Validation\Validator as v;
use App\Entity\CheckMail;

/**
 * This class is used to validate all form fields in the update form.
 */
class UpdateValidation {

  /**
   *  @var string $oldmail
   *    Stores oldmail entered by user.
   */
  private $oldmail;
  /**
   *  @var string $newmail
   *    Stores newmail entered by user.
   */
  private $newmail;
  /**
   *  @var string $number
   *    Stores number entered by user.
   */
  private $number;
  /**
   *  @var string $genre
   *    Stores genre entered by user.
   */
  private $genre;

  /**
   * Constructor to initialise global variables.
   * 
   *  @param string $oldmail
   *    Stores oldmail entered by user.
   * @param string $newmail
   *    Stores newmail entered by user.
   * @param string $number
   *    Stores number entered by user.
   * @param string $genre
   *    Stores genre entered by user.
   */
  function __construct(string $oldmail, string $newmail, string $number, string $genre) {
    $this->oldmail = $oldmail;
    $this->newmail = $newmail;
    $this->number = $number;
    $this->genre = $genre;
  }

  /**
   * Function to validate form data of update form.
   * 
   *  @return string
   *    Returns message according to validation error.
   */
  function validateData() {
    if (!v::notEmpty()->validate($this->oldmail)){
      return "Please enter old email";
    }
    if (!v::notEmpty()->validate($this->newmail)){
      return "Please enter new email";
    }
    else {
      $mailObj = new CheckMail($this->newmail);
      $flag = $mailObj->check();
      if ($flag == FALSE) {
        return "Enter a valid email id.";
      }
    }

    if (!v::notEmpty()->validate($this->number)){
      return "Please enter contact number";
    }
    else {
      if (!v::regex('/^[0-9+]{13}+$/')->validate($this->number)) {
        return "Enter a valid phone number.";
      }
    }
    if (!v::notEmpty()->validate($this->genre)){
      return "Please select a genre";
    }
    return "";
  }
}
?>

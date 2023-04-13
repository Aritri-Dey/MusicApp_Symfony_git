<?php 
namespace App\Services;

use Respect\Validation\Validator as v;
use App\Services\CheckMail;

/**
 * This class is used to validate the email fields of the update form..
 */
class UpdateValidation 
{

  /**
   *  @var string $oldMail
   *    Stores oldmail entered by user.
   */
  private $oldMail;
  /**
   *  @var string $newMail
   *    Stores newmail entered by user.
   */
  private $newMail;
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
   *  @param string $oldMail
   *    Stores oldmail entered by user.
   *  @param string $newMail
   *    Stores newmail entered by user.
   */
  public function __construct(string $oldMail, string $newMail) {
    $this->oldMail = $oldMail;
    $this->newMail = $newMail;
  }

  /**
   * Function to validate form data of update form.
   * 
   *  @return string
   *    Returns message according to validation error.
   */
  public function validateData() {
    if (!v::notEmpty()->validate($this->oldMail)) {
      return "Please enter old email";
    }
    else if (!v::notEmpty()->validate($this->newMail)) {
      return "Please enter new email";
    }
    else if (v::notEmpty()->validate($this->newMail)) {
      $obj = new ValidMail($this->newMail);
      return $obj->validMail();
    }
    return "";
  }
}

?>

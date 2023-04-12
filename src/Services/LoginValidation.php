<?php 
namespace App\Services;

use Respect\Validation\Validator as v;

/**
 * This class is used to validate all form fields in the login form.
 */
class LoginValidation {

  /**
   *  @var string $username
   *    Stores username entered by user.
   */
  private $username;
  /**
   *  @var string $email
   *    Stores email entered by user.
   */
  private $email;
  /**
   *  @var string $password
   *    Stores password entered by user.
   */
  private $password;

  /**
   * Constructor to initialise global variables.
   * 
   *  @param string $username
   *    Stores username entered by user.
   * @param string $email
   *    Stores email entered by user.
   * @param string $password
   *    Stores password entered by user.
   */
  function __construct(string $username , string $email, string $password) {
    $this->username = $username;
    $this->email = $email;
    $this->password = $password;
  }

  /**
   * Function to validate form data of login form.
   * 
   *  @return string
   *    Returns message according to validation error.
   */
  function validateData() {
    if (!v::notEmpty()->validate($this->username)){
      return "Please enter username";
    }
    if (!v::notEmpty()->validate($this->email)){
      return "Please enter email";
    }
    if (!v::notEmpty()->validate($this->password)){
      return "Please enter a password";
    }
    return "";
  }
}
?>

<?php 
namespace App\Services;

use Respect\Validation\Validator as v;

/**
 * This class is used to validate the number and genre fields..
 */
class NumberGenreValidation 
{

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
   *  @var string $number
   *    Stores contact number entered by user.
   */
  private $number;
  /**
   *  @var string $genre
   *    Stores genre selected by user.
   */
  private $genre;

  /**
   * Constructor to initialise global variables.
   * 
   *  @param string $number
   *    Stores contact number entered by user.
   *  @param string $genre
   *    Stores genre selected by user.
   */
  public function __construct(string $number, string $genre) {
    $this->number = $number;
    $this->genre = $genre;
  }

  /**
   * Function to validate form data of registration form.
   * 
   *  @return string
   *    Returns message according to validation error.
   */
  public function validateData() {
    if (!v::notEmpty()->validate($this->number)) {
      return "Please enter contact number";
    }
    else if (!v::regex('/^[0-9+]{13}+$/')->validate($this->number)) {
      return "Enter a valid phone number.";
    }
    if (!v::notEmpty()->validate($this->genre)) {
      return "Please select a genre";
    }
    return "";
  }
}
?>

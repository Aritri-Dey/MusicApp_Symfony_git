<?php
namespace App\Services;

use Respect\Validation\Validator as v;
use App\Services\CheckMail;

/**
 * Class to validate form fields.
 */
class Validation {

  /**
   * Function to validate username field.
   * 
   *  @param string $data
   *    Stores username to be validated.
   * 
   *  @return string
   *    Returns error message.
   */
  function validateNameEmpty(string $data) {
    if (!v::notEmpty()->validate($data)) {
      return "Please enter username.";
    }
    return "";
  }

  /**
   * Function to validate phone number field.
   * 
   *  @param string $data
   *    Stores phone number to be validated.
   * 
   *  @return string
   *    Returns error message.
   */
  function validateNumberEmpty(string $data) {
    if (!v::notEmpty()->validate($data)) {
      return "Please enter phone number.";
    }
    return "";
  }

  /**
   * Function to validate password field.
   * 
   *  @param string $data
   *    Stores password to be validated.
   * 
   *  @return string
   *    Returns error message.
   */
  function validatePasswordEmpty(string $data) {
    if (!v::notEmpty()->validate($data)) {
      return "Please enter a passowrd.";
    }
    return "";
  }

  /**
   * Function to validate email field.
   * 
   *  @param string $data
   *    Stores email to be validated.
   * 
   *  @return string
   *    Returns error message.
   */
  function validateEmailEmpty(string $data) {
    if (!v::notEmpty()->validate($data)) {
      return "Please enter email id.";
    }
    return "";
  }

  /**
   * Function to validate genre field.
   * 
   *  @param string $data
   *    Stores genre to be validated.
   * 
   *  @return string
   *    Returns error message.
   */
  function validateGenreEmpty(array $data) {
    if (!v::notEmpty()->validate($data)) {
      return "Please select a genre";
    }
    return "";
  }

  /**
   * Function to check whether email is valid.
   * 
   *  @param string $data
   *    Stores email to be validated.
   * 
   *  @return string
   *    Returns error message.
   */
  function validEmail(string $data) {
    $mailObj = new CheckMail($data);
    $flag = $mailObj->checkMail();
    if (!$flag) {
      return "Enter a valid email id.";
    }
    return "";
  }

  /**
   * Function to check whether phone number is valid.
   * 
   *  @param string $data
   *    Stores phone number to be validated.
   * 
   *  @return string
   *    Returns error message.
   */
  function validNumber(string $data) {
    if (!v::regex('/^[0-9+]{13}+$/')->validate($data)) {
      return "Enter a valid phone number.";
    }
    return "";
  }
}

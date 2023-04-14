<?php
  namespace App\Services;

  /**
   * Class to check whether mail is valid or not using API.
   */
  class CheckMail 
  {
    
    /**
     *  @var string 
     *    Stores the mail id to be checked.
     */
    private $mail;

    /**
     * Constructor to initialise global variable.
     * 
     *  @param string $mail
     *    Mail id to be checked.
     */
    public function __construct(string $mail) {
     $this->mail = $mail;
    }

    /**
     * Function to check whether mail id entered by user is valid
     * or not using API.
     * 
     *  @return bool
     *    Returns true or false according to condition.
     */
    public function checkMail() {
      // Set email address
      $emailAddress = $this->mail;
      $password = $_ENV['API_KEY'];
      // Set API Access Key
      $curl = curl_init();
      curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.apilayer.com/email_verification/check?email=$emailAddress",
        CURLOPT_HTTPHEADER => array(
          "Content-Type: text/plain",
          "apikey: $password"
        ),
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => TRUE,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
      ));
      $response = curl_exec($curl);
      curl_close($curl);
      $validationResult = json_decode($response, TRUE);
      // If validation is successful the $flag is set to TRUE, else $flag is set to FALSE.
      if ($validationResult && $validationResult["smtp_check"]) {
        return TRUE; 
      }
      return FALSE;
    }
  }

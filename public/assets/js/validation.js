/**
 * Function for frontend validation of registration form.
 * 
 *  @return bool
 *    TRUE or FLASE depending on condition satisfaction. 
 */
function checkEmptyReg() {
  var username = document.forms["registration-form"]["username"].value;
  var email = document.forms["registration-form"]["email"].value;
  var number = document.forms["registration-form"]["number"].value;
  var password = document.forms["registration-form"]["password"].value;

  var alphabetRegex = /^[a-zA-Z]+$/;
  var phoneRegex = /^\+91\d{10}$/;
  var emailRegex = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

  if (username == null || username == "") {
    $("#err").text("Enter username");
     return false;
    }
    if (email == null || email == "") {
      $("#err").text("Enter a email id");
      return false;
    }
  if (number == null || number == "") {
    $("#err").text("Enter contact number");
    return false;
  }
  if (password == null || password == "") {
    $("#err").text("Enter a password");
    return false;
  }
  if(!alphabetRegex.test(username)) {
    $("#errorname").text("Should contain only alphabet.");
    return false;
  }
  if(!phoneRegex.test(number)) {
    $("#errorphone").text("Enter a valid hone number.");
    return false;
  }
}

/**
 * Function for frontend validation of login form.
 * 
 *  @return bool
 *    TRUE or FLASE depending on condition satisfaction. 
 */
function checkEmptyLogin() {

  var username = document.forms["loginform"]["username"].value;
  var email = document.forms["loginform"]["email"].value;
  var password = document.forms["loginform"]["password"].value;

  var alphabetRegex = /^[a-zA-Z]+$/;
  var phoneRegex = /^\+91\d{10}$/;
  var emailRegex = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

  if (username == null || username == "") {
    $("#err").text("Please enter username.");
    return false;
  }
  if (email == null || email == "") {
    $("#err").text("Please enter email.");
    return false;
  }
  if (password == null || password == "") {
    $("#err").text("Please enter passowrd.");
    return false;
  }
  
}

function checkEmptyReg() {
  var username = document.forms["registration-form"]["username"].value;
  var email = document.forms["registration-form"]["email"].value;
  var number = document.forms["registration-form"]["number"].value;
  var password = document.forms["registration-form"]["password"].value;

  var alphabetRegex = /^[a-zA-Z]+$/;
  var phoneRegex = /^\+91\d{10}$/;
  var emailRegex = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

  if (username == null || username == "") {
     alert("Please enter username");
     return false;
    }
    if (email == null || email == "") {
      alert("Please enter email");
      return false;
    }
  if (number == null || number == "") {
    alert("Please enter number");
    return false;
  }
  if (password == null || password == "") {
    alert("Please enter password");
    return false;
  }
  if(!alphabetRegex.test(username)) {
    $("#errorname").text("Should contain only alphabet.");
    // alert("Username can only contain alphabet");
    return false;
  }
}

function checkEmptyLogin() {

  var username = document.forms["loginform"]["username"].value;
  var email = document.forms["loginform"]["email"].value;
  var password = document.forms["loginform"]["password"].value;

  var alphabetRegex = /^[a-zA-Z]+$/;
  var phoneRegex = /^\+91\d{10}$/;
  var emailRegex = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

  if (username == null || username == "") {
    alert("Please enter username");
    return false;
  }
  if (email == null || email == "") {
    alert("Please enter email");
    return false;
  }
  if (password == null || password == "") {
    alert("Please enter password");
    return false;
  }
  
}

// $(function() {

// });




// $(function() {

//   $("#name").keyup(function() {
//     var alphabetRegex = /^[a-zA-Z]+$/;
//     var val = this.value;
//     if (!alphabetRegex.test(val)) {
//       $("#errorname").text("only alphabets");
//     }
//     else {

//     }
//   });
// })



// $(function() {
//   $.ajax({           
//     type:"post",
//     url: "/test",
//     data: 
//     {
//       user: "ok"
//     },
//     dataType:"text",
//     success: function(data) {
//       console.log(data);
//     },
//     error: function(event) {
//       console.log("asd");
//     }
//   });
// })


/**
 * Function to open and close popup.
 */
$(document).ready(function(){
  $('#updatebtn').click(function(){
    $(".popup").css("display","block");
  })

  $('#close').click(function(){
    $(".popup").css("display","none");
  })
})

/**
 * Function to add animation to image on button click.
 */
function playBtn() {
  document.getElementById('player').play(); 
  $('#playBtn').css('display','none'); 
  $('#pauseBtn').css('display','block');

  document.getElementById('image').classList.add("animate");
}

/**
 * Function to stop animation to image on button click.
 */
function pauseBtn() {
  document.getElementById('player').pause(); 
  $('#playBtn').css('display','block'); 
  $('#pauseBtn').css('display','none');

  document.getElementById('image').classList.remove("animate");
}

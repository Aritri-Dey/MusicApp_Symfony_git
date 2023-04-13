function playBtn() {
  document.getElementById('player').play(); 
  $('#playbtn').css('display','none'); 
  $('#pausebtn').css('display','block');

  document.getElementById('image').classList.add("animate");
}

function pauseBtn() {
  document.getElementById('player').pause(); 
  $('#playbtn').css('display','block'); 
  $('#pausebtn').css('display','none');

  document.getElementById('image').classList.remove("animate");
}

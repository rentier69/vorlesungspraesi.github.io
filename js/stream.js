var pc = new RTCPeerConnection();

window.onload = function () {
  document.getElementById('streamBtn').onclick = function(){
    const mediastream = startCapture();
    
  }
}


if(navigator.getDisplayMedia || navigator.mediaDevices.getDisplayMedia) {
  function onGettingSteam(stream) {
      video.srcObject = stream;
      videosContainer.insertBefore(video, videosContainer.firstChild);
  }
}



pc.onaddstream = function(obj) {
  var vid = document.createElement("video");
  document.appendChild(vid);
  vid.srcObject = obj.stream;
}

// Helper functions
function endCall() {
  var videos = document.getElementsByTagName("video");
  for (var i = 0; i < videos.length; i++) {
    videos[i].pause();
  }

  pc.close();
}

function error(err) {
  endCall();
}



function startCapture(displayMediaOptions) {
  let captureStream = null;
 
  return navigator.mediaDevices.getDisplayMedia(displayMediaOptions)
     .catch(err => { console.error("Error:" + err); return null; });
 }

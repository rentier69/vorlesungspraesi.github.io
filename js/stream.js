var pc = new RTCPeerConnection();
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

navigator.mediaDevices.getUserMedia({ audio: true, video: false })
.then(function(stream) {
  /* use the stream */
})
.catch(function(err) {
  /* handle the error */
});

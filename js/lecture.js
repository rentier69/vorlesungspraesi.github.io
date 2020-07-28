function getData(methodType, url, post_data, dataType = "json", id) {
    //https://api.jquery.com/jquery.ajax/
    return $.ajax({
        url: url,
        method: methodType,
        dataType: dataType,
        cache: false, /* ggfs. später wieder entfernen */
        data: post_data
    });
}
//input_ids als array übergeben!
//bsp: onclick="enableInput(['newNameSource','buttonSaveUsername'])"
function enableInput(input_ids) {
    input_ids.forEach(input => {
        document.getElementById(input).removeAttribute('disabled');
    });
}
function disableInput(input_ids) {
    input_ids.forEach(input => {
        document.getElementById(input).setAttribute('disabled', 'true');
    });
}

function sendMessage(v_id, username) {
    data = {
        "v_id": v_id,
        "nachricht": document.getElementById('chatMessage').value,
        "username": username
    }
    getData("post", "api/lecture-api.php?action=postMessage", data, "text").done(getMessages(v_id));
    document.getElementById('chatMessage').value = "";
    changeChatButton();
}
function getMessages(v_id) {
    data = {
        "v_id": v_id
    }
    getData("post", "api/lecture-api.php?action=getMessage", data, "text").done(fillChat);
}

function fillChat(data) {
    let chatbox = document.getElementById("chatbox");
    while(chatbox.firstChild){
        chatbox.removeChild(chatbox.firstChild);
    }
    let messages = JSON.parse(data);
    for (let message of messages) {
        html = '<div class="message my-2">';
        html += '<div class="message_head d-flex">';
        html += '<div class="message_sender w-50">'+ message.benutzername +'</div>';
        html +=  '<div class="message_time w-50 text-right">'+ message.nachricht_zeitstempel +'</div>';
        html +=  '</div> ';
        html +=  '<div class="message_body bg-info p-2">';
        html +=  '<span>'+ message.nachricht +'<span>';
        html +=  '</div>';
        html +=  '</div>';
        chatbox.innerHTML += html;
    }
}

function changeChatButton() {
    if (document.getElementById("chatMessage").value.length > 0) {
        document.getElementById("sendMessage").disabled = false;
    } else {
        document.getElementById("sendMessage").disabled = true;
    }
}

//Aktualisieren und Scrollen(TO DO) des Chatfensters
function reloadChat(){
    getMessages(v_id);
}
var RefreshChatInterval;
function changeChatRefresh(){
    if(document.getElementById("liveChat").checked==true){
    RefreshChatInterval=setInterval(reloadChat, 2000);
    }else{
        clearInterval(RefreshChatInterval);
    }
}
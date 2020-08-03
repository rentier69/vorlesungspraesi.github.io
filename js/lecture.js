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
    $('#chatMessage').val("");
    changeStateChatButton();
}

function getChatQuestion(nachricht_id) {
    getData("get", "api/lecture-api.php?action=getChatQuestionById&nachricht_id=" + nachricht_id, null, "json").done(function (frage) {
        var html_frage = '<span>' + frage.frage_titel + '<span>';
        //unterscheidung single und multi choice udn freitext
        if (frage.frage_typ_id == 2 || frage.frage_typ_id == 3) {
            //frage_typ ist single choice oder multi choice
            //container mit id erstellen, der asynchron mit var html_antwort befüllt werden kann
            html_frage += '<div id="chatQuestionAnswerList_' + nachricht_id + '"></div>';

            getData("get", "api/lecture-api.php?action=getAllAnswerOptionsByQId&q_id=" + frage.frage_id + "&nachricht_id=" + nachricht_id, null, "json").done(function (answers) {
                var html_antwort = "";
                if (frage.frage_typ_id == 2) {
                    //single choice                               
                    answers.forEach(answer => {
                        html_antwort += '<div class="custom-control custom-radio">';
                        // html_antwort += '<fieldset>'
                        html_antwort += '<input type="radio" class="custom-control-input" value="' + answer.antwort + '" name="' + frage.frage_id + '" id="' + answer.antwort + '">';
                        html_antwort += '<label class="custom-control-label" for="' + answer.antwort + '">' + answer.antwort + '</label>';
                        // html_antwort += '</fieldset>'
                        html_antwort += '</div>';
                    });
                } else {
                    //multi choice
                    answers.forEach(answer => {
                        html_antwort += '<div class="custom-control custom-checkbox">';
                        // html_antwort += '<input type="checkbox" class="custom-control-input" name="' + answer.antwort + '" id="' + answer.antwort + '">';
                        html_antwort += '<input type="checkbox" class="custom-control-input" name="' + frage.frage_id + '[]" value="' + answer.antwort + '" id="' + answer.antwort + '">';
                        html_antwort += '<label class="custom-control-label" for="' + answer.antwort + '">' + answer.antwort + '</label>';
                        html_antwort += '</div>';
                    });
                }
                $("#chatQuestionAnswerList_" + nachricht_id).html(html_antwort);
            });
        } else {
            //freitext
            html_frage += '<textarea class="form-control" rows="3" maxlength="255" name="' + frage.frage_id + '" id="questionAnswer" placeholder="Antwort eingeben"></textarea>';
        }
        // speichern button gibt es immer
        html_frage += '<button class="btn btn-outline-light btn-block form-control mt-1" name="answer_send" onclick="event.preventDefault(); storeChatQuestionAnswer(chatQuestionForm_' + nachricht_id + ');" id="answer_send">Antwort speichern</button>';

        $("#chatQuestionForm_" + nachricht_id).html(html_frage);
    });
}

function storeChatQuestionAnswer(form_id) {
    formData = $("#" + form_id.id).serializeArray();
    formData.unshift({name:"username",value: username}); // an anfang des arrays setzen
    //formData darf nur username und ein Array mit Antworten enthalten
    getData("post", "api/lecture-api.php?action=storeChatQuestionAnswer", formData, "text").done(function (response) {        
        $('#' + form_id.id).find('input, textarea, button').attr('disabled','disabled');
    });
}

var mostRecentMessageID = 0; //only get latest messages - not all - modified in getMessages()

function getMessages(v_id) {
    data = {
        "v_id": v_id,
        "mostRecentMessageID": mostRecentMessageID
    }
    getData("post", "api/lecture-api.php?action=getMessage", data, "json").done(function (messages) {
        // let messages = JSON.parse(data);
        for (let message of messages) {
            //unterschiedliche styles, wenn eigene Nachricht
            if (message.benutzername == username) {
                html_nachricht = '<div class="message my-2 ml-2">';
                html_nachricht += '<div class="message_head message_head_own d-flex">';
            } else {
                html_nachricht = '<div class="message my-2">';
                html_nachricht += '<div class="message_head d-flex">';
            }

            html_nachricht += '<div class="message_sender w-50">' + message.benutzername + '</div>';
            html_nachricht += '<div class="message_time w-50 text-right">' + message.nachricht_zeitstempel + '</div>';
            html_nachricht += '</div> '; // ende message_head

            //unterschiedliche styles, wenn eigene Nachricht
            if (message.benutzername == username) {
                html_nachricht += '<div class="message_body message_body_own bg-success p-2">';
            } else {
                html_nachricht += '<div class="message_body bg-info p-2">';
            }

            if (message.frage) {
                //wenn nachrichtentyp = frage, dann hier weitermachen, sonst nur nachricht einfügen
                html_nachricht += '<form id="chatQuestionForm_' + message.nachricht_id + '" class="mb-0"></form>';
                getChatQuestion(message.nachricht_id);
            } else {
                html_nachricht += '<span>' + message.nachricht + '<span>';
            }

            html_nachricht += '</div>'; //ende message_body
            html_nachricht += '</div>'; //ende message
            $('#chatbox').append(html_nachricht);
            mostRecentMessageID = message.nachricht_id;
        }
    });
}

function changeStateChatButton() {
    if (document.getElementById("chatMessage").value.length > 0) {
        document.getElementById("sendMessage").disabled = false;
    } else {
        document.getElementById("sendMessage").disabled = true;
    }
}

//Aktualisieren und Scrollen(TO DO) des Chatfensters
function reloadChat() {
    getMessages(v_id);
}

function changeChatRefresh() {
    if (document.getElementById("liveChat").checked == true) {
        RefreshChatInterval = setInterval(reloadChat, 2000);
    } else {
        clearInterval(RefreshChatInterval);
    }
}
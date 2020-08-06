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

function getChatQuestion(message) {
    getData("get", "api/lecture-api.php?action=getChatQuestionById&nachricht_id=" + message.nachricht_id + '&benutzername=' + username, null, "json").done(function (frage) {
        var html_frage = '<span>' + frage.frage_titel + '<span>';
        //unterscheidung single und multi choice udn freitext
        if (frage.frage_typ_id == 2 || frage.frage_typ_id == 3) {
            //frage_typ ist single choice oder multi choice
            //container mit id erstellen, der asynchron mit var html_antwort befüllt werden kann
            html_frage += '<div id="chatQuestionAnswerList_' + message.nachricht_id + '"></div>';

            getData("get", "api/lecture-api.php?action=getAllAnswerOptionsByQId&q_id=" + frage.frage_id + "&nachricht_id=" + message.nachricht_id, null, "json").done(function (answers) {
                var html_antwort = "";
                if (frage.frage_typ_id == 2) {
                    //single choice                               
                    answers.forEach(answer => {
                        html_antwort += '<div class="custom-control custom-radio">';
                        // html_antwort += '<fieldset>'
                        html_antwort += '<input type="radio" class="custom-control-input" value="' + answer.antwort + '" name="' + message.nachricht_id + '_' + frage.frage_id + '" id="' + message.nachricht_id + '_' + frage.frage_id + '_' + answer.antwort + '">'; //
                        html_antwort += '<label class="custom-control-label" for="' + message.nachricht_id + '_' + frage.frage_id + '_' + answer.antwort + '">' + answer.antwort + '</label>';
                        // html_antwort += '</fieldset>'
                        html_antwort += '</div>';
                    });
                } else {
                    //multi choice
                    answers.forEach(answer => {
                        html_antwort += '<div class="custom-control custom-checkbox">';
                        // html_antwort += '<input type="checkbox" class="custom-control-input" name="' + answer.antwort + '" id="' + answer.antwort + '">';
                        html_antwort += '<input type="checkbox" class="custom-control-input" value="' + answer.antwort + '" name="' + message.nachricht_id + '_' + frage.frage_id + '[]"  id="' +message. nachricht_id + '_' + frage.frage_id + '_' + answer.antwort + '">';
                        html_antwort += '<label class="custom-control-label" for="' + message.nachricht_id + '_' + frage.frage_id + '_' + answer.antwort + '">' + answer.antwort + '</label>';
                        html_antwort += '</div>';
                    });
                }
                $("#chatQuestionAnswerList_" + message.nachricht_id).html(html_antwort);
            });
        } else {
            //freitext
            html_frage += '<textarea class="form-control" rows="3" maxlength="255" name="' + message.nachricht_id + '_' + frage.frage_id + '" placeholder="Antwort eingeben"></textarea>';
        }
        // speichern button gibt es immer
        html_frage += '<button class="btn btn-outline-light btn-block form-control mt-1" id="answer_send_' + message.nachricht_id + '" name="answer_send" onclick="event.preventDefault(); storeChatQuestionAnswer('+message.nachricht_id+');">Antwort speichern</button>';

        // deaktiveren button nur anfügen, wenn man der sender ist
        if(username == message.benutzername){
            html_frage += '<button class="btn btn-outline-light btn-block form-control mt-1" id="deactivate_' + message.nachricht_id + '" name="deactivate" onclick="event.preventDefault(); deactivateChatQuestion(' + message.nachricht_id + ');">Frage deaktivieren</button>';
        }

        //html frage in element mit entsprechender id einfügen
        $("#chatQuestionForm_" + message.nachricht_id).html(html_frage);

        //to-do optional: erst prüfen, nachdem antwort optionen eingefügt wurden.
        if(!frage.frage_aktiv || frage.antwort_in_db){
            disableChatQuestion(message.nachricht_id);
        }        
    });
}

function disableChatQuestion(nachricht_id){
    $('#chatQuestionForm_' + nachricht_id).find('input, textarea, button').attr('disabled','disabled');    
}

function deactivateChatQuestion(nachricht_id){
    data ={
        "nachricht_id": nachricht_id
    }
    getData("post", "api/lecture-host-api.php?action=deactivateChatQuestion", data, "text").done(function (response) {
        disableChatQuestion(nachricht_id);
    });
}

function storeChatQuestionAnswer(nachricht_id) {
    formData = $("#chatQuestionForm_" + nachricht_id).serializeArray();
    formData.unshift({name:"username",value: username}); // an anfang des arrays setzen
    //formData darf nur username und ein Array mit Antworten enthalten
    console.log(formData);
    getData("post", "api/lecture-api.php?action=storeChatQuestionAnswer", formData, "text").done(function (response) {
        disableChatQuestion(nachricht_id);
    });
}

function checkForInactiveQuestions(v_id) {
    data = {
        "v_id": v_id,
    }
    
    getData("post", "api/lecture-api.php?action=getInactiveQuestions", data, "json").done(function (messages) {
        messages.forEach(message => {
            console.log(message);
            disableChatQuestion(message.nachricht_id);
        });
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
                getChatQuestion(message);
            } else {
                html_nachricht += '<span>' + message.nachricht + '<span>';
            }

            html_nachricht += '</div>'; //ende message_body
            html_nachricht += '</div>'; //ende message

            $('#chatbox').append(html_nachricht);

            //höchste abgerufene nachricht_id merken, damit beim nächsten Intervall nur die neusten Nachrichten geholt werden.
            mostRecentMessageID = message.nachricht_id;            
        }
        //prüfen, ob inzwischen fragen deaktiviert wurden
        checkForInactiveQuestions(v_id);
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
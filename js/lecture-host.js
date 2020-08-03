/*
Bildschirm freigeben (ggfs auch bei gemeinsam)
Aufgaben auswählen und stellen
Vorlesung starten
*/

function addLoadingOverlay(){
    document.getElementById("loadingOverlay").classList.add("loading-overlay","d-flex" ,"justify-content-center");
    spinnerElement = document.getElementById("loadingSpinner");
    spinnerElement.classList.add("spinner-border", "text-primary", "align-self-center");
    spinnerElement.style.display = "block";
}

function removeLoadingOverlay(){
    //aufrufen, wenn alle benötigten Daten geladen sind
    document.getElementById("loadingOverlay").classList.remove("loading-overlay","d-flex" ,"justify-content-center");
    spinnerElement = document.getElementById("loadingSpinner");
    spinnerElement.classList.remove("spinner-border", "text-primary", "align-self-center");
    spinnerElement.style.display = "none";
}

function deleteChat(){
    data = {
        "v_id": v_id
    }
    getData("post", "api/lecture-host-api.php?action=deleteChat", data, "text");
    $('#chatbox').empty();
}

// in lecture-host.php verschoben
// function closeLecture(v_id) {
//     //irgendwie bei schließen des Browserfensters ausführen - erledigt - eventListener in lecture-host.php
//     data = {
//         "v_id": v_id
//     }
//     console.log(data);
//     getData("post", "api/lecture-host-api.php?action=closeLecture", data, "text");
// }

function getLectureQuestions(v_id){
    getData("get", "api/backend-api.php?mode=lectures&action=getActiveQuestions&v_id=" + v_id, null, "json").done(function(questions){
        questions.forEach(question => {
            var questionAnswerListUL = "questionAnswerList" + question.frage_id;
            html = '<a href="#" onclick="postQuestion('+ question.frage_id +')" class="list-group-item list-group-item-action flex-column align-items-start">';
            html += '<div class="d-flex w-100 justify-content-between">';
            html += '<h5 class="mb-1">' + question.frage_titel +'</h5>';
            html += '<small>'+ question.frage_typ_titel +'</small>';
            html += '</div>';
            html += '<ul class="list-group" id="'+ questionAnswerListUL +'"></ul>';
            //html += '<small>Donec id elit non mi porta.</small>';
            html += '</a>';
            $('#listLectureQuestions').append(html);
            getData("get", "api/backend-api.php?mode=lecturequestion&action=getAllAnswerOptionsByQId&q_id=" + question.frage_id, null, "json").done(function(answers){
                answers.forEach(answer => {
                    $('#' + questionAnswerListUL).append('<li class="list-group-item">' + answer.antwort + '</li>');
                });
            });
        });
        removeLoadingOverlay();
    });
}
function postQuestion(q_id) {
    data = {
        "v_id": v_id,
        "q_id": q_id,
        "username": username
    }
    getData("post", "api/lecture-host-api.php?action=postChatQuestion", data, "text").done(reloadChat());  
    $('#lectureQuestionModal').modal('hide')
}
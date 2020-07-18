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

function closeLecture(v_id) {
    //irgendwie bei schließen des Browserfensters ausführen
    data = {
        "v_id": v_id
    }
    getData("post", "lecture-api.php?action=closeLecture", data, "text").done(prepareHomePage);
}

function getActiveLectures() {
    getData("get", "lecture-api.php?action=getActiveLectures", null).done(prepareFrontendUI);
}

function prepareFrontendUI(data) {
    //ggfs. als Tabelle darstellen
    for (let row of data) {
        option = '<option value="' + row.vorlesung_id + '">' + row.vorlesung_name + " - " + row.zeit_gestartet + '</option>'
        document.getElementById("lectureToJoin").innerHTML += option;
    }
}

function sendMessage(v_id) {
    data = {
        "v_id": v_id,
        "text": document.getElementById('chatMessage')

    }
    getData("post", "lecture-api.php?action=postMessage", data, "text").done();
}
function getMessage(v_id) {
    getData("post", "lecture-api.php?action=postMessage", data, "text").done();
}



//invalid-feedback und valid-feedback werden beide benötigt und müssen immer die ID error_<field_id> und valid_<field_id> haben 
var checkField = function (field_id, field_name, submit_id, form_id, check_in_db) {
    var field = document.getElementById(field_id);
    if (field.value.length == 0) {
        setErrorMessage(field, field_id, field_name + ' eingeben');
    } else if (field.value.length < 4) {
        setErrorMessage(field, field_id, field_name + ' muss mind. 4 Zeichen lang sein')
    } else {
        if (check_in_db == true) {
            //Prüfen, ob Wert bereits in DB. Liefert true falls ja
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "functions.php?" + field_id + "=" + field.value, true);
            setErrorMessage(field, field_id, 'Fehler bei der Datenbankabfrage')
            xhr.send();
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    if (!xhr.responseText) {
                        removeErrorMessage(field, field_id);
                    } else {
                        setErrorMessage(field, field_id, field_name + ' bereits vergeben')
                    }
                }
                changeSubmitButton(submit_id, form_id);
            };

        } else {
            removeErrorMessage(field, field_id);
        }
    }
    changeSubmitButton(submit_id, form_id);
};

var removeErrorMessage = function (field, field_id) {
    field.setCustomValidity('');
    document.getElementById("error_" + field_id).setAttribute("hidden", true);
    document.getElementById("valid_" + field_id).removeAttribute("hidden");
};

var setErrorMessage = function (field, field_id, error_message) {
    document.getElementById("valid_" + field_id).setAttribute("hidden", true);
    field.setCustomValidity(error_message);
    var old_error_div = document.querySelector('#error_' + field_id);
    var new_error_div = document.createElement('div');
    new_error_div.appendChild(document.createTextNode(error_message));
    new_error_div.setAttribute('id', 'error_' + field_id);
    new_error_div.setAttribute('class', 'invalid-Feedback');
    old_error_div.parentNode.replaceChild(new_error_div, old_error_div);
    new_error_div.removeAttribute("hidden");
};

var checkPassword = function (password_id, password_repeat_id, submit_id, form_id) {
    password = document.getElementById(password_id);
    password_repeat = document.getElementById(password_repeat_id);

    if (password.value.length != 0) {
        if (password.value.length < 6) {
            setErrorMessage(password, password_id, "Passwort muss mind. 6 Zeichen lang sein");
        } else {
            removeErrorMessage(password, password_id);
            if (password.value == password_repeat.value) {
                removeErrorMessage(password_repeat, password_repeat_id);
            } else {
                setErrorMessage(password_repeat, password_repeat_id, "Passwörter müssen übereinstimmen")
            }
        }
    } else {
        setErrorMessage(password, password_id, "Bitte Passwort eingeben");
    }
    changeSubmitButton(submit_id, form_id);

};


var changeSubmitButton = function (submit_id, form_id) {
    if (submit_id != null) {
        if (document.querySelector("#" + form_id + ':invalid') === null) {
            document.getElementById(submit_id).disabled = false;
        } else {
            document.getElementById(submit_id).disabled = true;
        }
    }
};
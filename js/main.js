/*
Generelle Funktionen
 Form Val
 notification
 ggfs. Header / Footer
*/

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

function changePassword(){
    data = {
        "pw": document.getElementById("passwordChange").value
    }
    getData("post", "/vl1/components/api/lecture-api.php?action=changePassword", data, "text").done(function(){
        addNotification("success","Passwort erfolgreich geändert!");
    });
    document.getElementById("formChangePassword").reset();
};

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
            setErrorMessage(field, field_id, 'Warten auf Datenbankabfrage')
            var url = "/vl1/components/functions.php?" + field_id + "=" + field.value;
            getData("get",url,null,"text").done(function(data){
                console.log(data);
                if (!data) {
                    removeErrorMessage(field, field_id);
                } else {
                    setErrorMessage(field, field_id, field_name + ' bereits vergeben')
                }
                changeSubmitButton(submit_id, form_id);
            });
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

function addNotification(type, title, bodyText = ""){
    console.log("drin");
    getData("get", "/vl1/components/static/notification.html", null, "html").done(function (data) {
        var parser = new DOMParser();
        //parsen, damit notification vor einblenden angepasst werden kann
        var notification = parser.parseFromString(data, 'text/html');
        var notification_id = "notification" + Date.now();

        //id muss einzigartig sein, damit zeitgesteuerter fade-out funktioniert
        notification.getElementById("notification").id = notification_id;
        switch (type) {
            case "success":
                notification.getElementById(notification_id).classList.add("border-success");
                notification.getElementById("notificationType").classList.add("bg-success");
                notification.getElementById("notificationIcon").classList.add("fa-check");
                break;
            case "danger":
                notification.getElementById(notification_id).classList.add("border-danger");
                notification.getElementById("notificationType").classList.add("bg-danger");
                notification.getElementById("notificationIcon").classList.add("fa-exclamation-circle");
                break;
            case "warning":
                notification.getElementById(notification_id).classList.add("border-warning");
                notification.getElementById("notificationType").classList.add("bg-warning");
                notification.getElementById("notificationIcon").classList.add("fa-exclamation-circle");
                break;        
            default:
                break;
        }
        notification.getElementById("notificationHeader").innerHTML = title;
        notification.getElementById("notificationBodyText").innerHTML = bodyText;
        //notification.getElementById("closeNotification").onclick = "document.getElementById('" + notification_id + "').style.display = 'none'";
        notification.getElementById("closeNotification").setAttribute("onclick","document.getElementById('" + notification_id + "').style.display = 'none'");
        //nicht sichtbar machen, damit fade-in klappt
        notification.getElementById(notification_id).style.display = "none";
        document.getElementById("inAppNotifications").innerHTML += notification.documentElement.innerHTML;
        $('#' + notification_id).fadeIn("fast", "linear", function(){
            timeoutNotification(notification_id)
        });        
    });
}
function timeoutNotification(notification_id){
    //5000ms warten, bis Benachrichtigung ausgeblendet wird
    setTimeout(function(){
        $('#' + notification_id).fadeOut("slow", "linear");
    }, 3000);
}
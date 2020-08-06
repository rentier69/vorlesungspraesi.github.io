//Feedbackmeldungen des zu prüfenden Feldes müssen immer die ID error_<field_id_string> oder valid_<field_id_string> haben 

var checkField = function (field_id_string, field_name, submit_id_string, pruefung_alter_wert, old_value, form_id_string) {
    var field_id = document.getElementById(field_id_string);
    document.getElementById("valid_"+field_id_string).removeAttribute("hidden");
    changeSubmitButton(submit_id_string, form_id_string);
    if (field_id.value == '') {
        field_id.setCustomValidity('Benutzername eingeben');
        var oldDiv = document.querySelector('#error_' + field_id_string);
        var newDiv = document.createElement('div');
        newDiv.appendChild(document.createTextNode(field_name + " eingeben"));
        oldDiv.parentNode.replaceChild(newDiv, oldDiv);
        newDiv.setAttribute('id', 'error_' + field_id_string);
        newDiv.setAttribute('class', 'invalid-Feedback');
    } else {
        if (field_id.value.length < 4) {
            field_id.setCustomValidity('Benutzername muss mind. 4 Zeichen lang sein');
            var oldDiv = document.querySelector('#error_' + field_id_string);
            var newDiv = document.createElement('div');
            newDiv.appendChild(document.createTextNode(field_name + " muss mind. 4 Zeichen lang sein"));
            oldDiv.parentNode.replaceChild(newDiv, oldDiv);
            newDiv.setAttribute('id', 'error_' + field_id_string);
            newDiv.setAttribute('class', 'invalid-Feedback');
        } else {
            //falls neuer Benutzername = alter Benutzername
            if (pruefung_alter_wert) {
                if (field_id.value == old_value) {
                    field_id.setCustomValidity('');
                    document.getElementById("valid_"+field_id_string).setAttribute("hidden", "true");
                } else {

                    //Prüfen, ob Username bereits in DB. Liefert true falls ja
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", "functions.php?" + field_id_string + "=" + field_id.value, true);
                    xhr.send();

                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            if (!xhr.responseText) {
                                field_id.setCustomValidity('');

                            } else {
                                field_id.setCustomValidity(field_name + " bereits vergeben");
                                var oldDiv = document.querySelector('#error_' + field_id_string);
                                var newDiv = document.createElement('div');
                                newDiv.appendChild(document.createTextNode(field_name + " bereits vergeben"));
                                oldDiv.parentNode.replaceChild(newDiv, oldDiv);
                                newDiv.setAttribute('id', 'error_' + field_id_string);
                                newDiv.setAttribute('class', 'invalid-Feedback');
                            }
                            changeSubmitButton(submit_id_string, form_id_string);

                        }

                    };
                }
            } else {
                //Prüfen, ob Username bereits in DB. Liefert true falls ja
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "functions.php?" + field_id_string + "=" + field_id.value, true);
                xhr.send();

                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        if (!xhr.responseText) {
                            field_id.setCustomValidity('');

                        } else {
                            field_id.setCustomValidity(field_name + " bereits vergeben");
                            var oldDiv = document.querySelector('#error_' + field_id_string);
                            var newDiv = document.createElement('div');
                            newDiv.appendChild(document.createTextNode(field_name + " bereits vergeben"));
                            oldDiv.parentNode.replaceChild(newDiv, oldDiv);
                            newDiv.setAttribute('id', 'error_' + field_id_string);
                            newDiv.setAttribute('class', 'invalid-Feedback');
                        }
                        changeSubmitButton(submit_id_string, form_id_string);
                    }
                };
            }
        }
    }
};

var checkPassword = function(password_id_string, passwordRepeat_id_string, submit_id_string, form_id_string) {
    password=document.getElementById(password_id_string);
    passwordRepeat=document.getElementById(passwordRepeat_id_string);
    if (password.value.length != 0) {
        if (password.value.length < 6) {
            password.setCustomValidity('Passwort muss mind. 6 Zeichen lang sein');
            var oldDiv = document.querySelector('#error_'+password_id_string);
            var newDiv = document.createElement('div');
            newDiv.appendChild(document.createTextNode("Passwort muss mind. 6 Zeichen lang sein"));
            oldDiv.parentNode.replaceChild(newDiv, oldDiv);
            newDiv.setAttribute('id', 'error_'+password_id_string);
            newDiv.setAttribute('class', 'invalid-Feedback');
        } else {
            if (password.value == passwordRepeat.value) {
                password.setCustomValidity('');
                passwordRepeat.setCustomValidity('');
                changeSubmitButton(submit_id_string, form_id_string);
            } else {
                password.setCustomValidity('');
                passwordRepeat.setCustomValidity('Passwörter müssen übereinstimmen');
                var oldDiv = document.querySelector('#error_'+passwordRepeat_id_string);
                var newDiv = document.createElement('div');
                newDiv.appendChild(document.createTextNode("Passwörter müssen übereinstimmen"));
                oldDiv.parentNode.replaceChild(newDiv, oldDiv);
                newDiv.setAttribute('id', 'error_'+passwordRepeat_id_string);
                newDiv.setAttribute('class', 'invalid-Feedback');
            }
        }
    } else {
        password.setCustomValidity('Passwort eingeben');
        var oldDiv = document.querySelector('#error_'+password_id_string);
        var newDiv = document.createElement('div');
        newDiv.appendChild(document.createTextNode("Passwort eingeben"));
        oldDiv.parentNode.replaceChild(newDiv, oldDiv);
        newDiv.setAttribute('id', 'error_'+password_id_string);
        newDiv.setAttribute('class', 'invalid-Feedback');
    }
};


var changeSubmitButton = function (submit_id_string, form_id_string) {
    if (submit_id_string != null) {
        if (document.querySelector("#"+form_id_string+':invalid') === null) {
            document.getElementById(submit_id_string).disabled = false;
        } else {
            document.getElementById(submit_id_string).disabled = true;
        }
    }
};
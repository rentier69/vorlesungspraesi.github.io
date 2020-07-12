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
function searchInTwoColumns(tableId) {
    // Declare variables
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById(tableId);
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {

        td0 = tr[i].getElementsByTagName("td")[0];
        td1 = tr[i].getElementsByTagName("td")[1];
        if (td0 || td1) {

            txtValue0 = td0.textContent || td0.innerText;
            txtValue1 = td1.textContent || td1.innerText;
            if (txtValue0.toUpperCase().indexOf(filter) > -1 || txtValue1.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
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
//item_id benötigt für die Bearbeitung von best. Benutzern / Gruppen / Vorlesungen
function changeMode(mode, item_id) {
    var activeNav = nav.querySelector('.active');
    activeNav.classList.remove("active");
    
    //loading overlay hinzufügen
    addLoadingOverlay();

    switch (mode) {
        case "home":
            getData("get", "static/home.html", null, "html").done(function (data) {
                setStaticHtml(data);
                getData("get", "backend-api.php?mode=lectures&action=getAll", null).done(prepareHomePage);
            });
            document.getElementById("nav_home").classList.add("active");
            break;
        case "lectures":
            getData("get", "static/lectures.html", null, "html").done(function (data) {
                setStaticHtml(data);
                loadLectureList(null);
            });
            document.getElementById("nav_lectures").classList.add("active");
            break;
        case "editlecture":
            getData("get", "static/editlecture.html", null, "html").done(function (data) {
                setStaticHtml(data);
                initializeEditLecture(item_id);
            });
            document.getElementById("nav_lectures").classList.add("active");
            break;
        case "createquestion":
            getData("get", "static/createquestion.html", null, "html").done(function (data) {
                setStaticHtml(data);
                initializeCreateQuestion();
            });
            document.getElementById("nav_lectures").classList.add("active");
            break;
        case "editquestion":
            getData("get", "static/editquestion.html", null, "html").done(function (data) {
                setStaticHtml(data);
                initializeEditQuestion(item_id);
            });
            document.getElementById("nav_lectures").classList.add("active");
            break;
        case "users":
            getData("get", "static/users.html", null, "html").done(function (data) {
                setStaticHtml(data);
                loadUserList(null);
            });
            document.getElementById("nav_users").classList.add("active");
            break;
        case "edituser":
            getData("get", "static/edituser.html", null, "html").done(function (data) {
                setStaticHtml(data);
                initializeEditUser(item_id);
            });
            document.getElementById("nav_users").classList.add("active");
            break;
        case "groups":
            getData("get", "static/groups.html", null, "html").done(function (data) {
                setStaticHtml(data);
                loadGroupList(null);
            });            
            document.getElementById("nav_groups").classList.add("active");
            break;
        case "editgroup":
        getData("get", "static/editgroup.html", null, "html").done(function (data) {
            setStaticHtml(data);
            initializeEditGroup(item_id);
        });
        document.getElementById("nav_groups").classList.add("active");
        break;
        default:
            break;
    }
}
function setStaticHtml(data) {
    document.getElementById("main").innerHTML = data;
}

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

function addNotification(type, title, bodyText = ""){
    getData("get", "static/notification.html", null, "html").done(function (data) {
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

function prepareHomePage(data) {    
    // Build HTML table with given data
    var selBody = "";
    for (let row of data) {
        option = '<option value="' + row.vorlesung_id + '">' + row.vorlesung_name + '</option>';
        selBody += option;
    }
    // Put table into HTML container
    document.getElementById("lectureToStart").innerHTML = selBody;
    removeLoadingOverlay();    
}

/*
Funktionen für Benutzerverwaltung
*/
function createUser() {    
    var data = {
        username: document.getElementById('username').value,
        pw: document.getElementById('password1').value,
        user_type: document.getElementById('user_type').value
    }
    getData("post", "backend-api.php?mode=users&action=create", data).done(function(data){
        changeMode('users');
        addNotification("success","Benutzer " + data.benutzer_id + " " + data.benutzername + " wurde erfolgreich erstellt!");
    });
}
function loadUserList(data) {
    if (data == null) {
        getData("get", "backend-api.php?mode=users&action=getAll", null).done(loadUserList);
    } else {        
        // Build HTML table with given data
        var tbody = "";
        for (let row of data) {
            tbody += '<tr class="clickable" onclick="changeMode(\'edituser\',' + row.benutzer_id + ')">';
            tbody += "<td>" + row.benutzer_id + "</td>";
            tbody += "<td>" + row.benutzername + "</td>";
            tbody += "<td>" + row.aktiv + "</td>";
            tbody += "<td>" + row.datum_registriert + "</td>";
            tbody += "<td>" + row.datum_letzterlogin + "</td>";
            tbody += "</tr>";
        }

        // Put table into HTML container
        document.getElementById("allUsersBody").innerHTML += tbody;
        removeLoadingOverlay();
    }
}
function initializeEditUser(id) {
    editUser.benutzer_id = id;
    editUser.queryDetails();
    editUser.queryGroupMembership();
    editUser.prepareUserInterface();
}
function closeEditUser() {
    editUser.benutzer_id = null;
    editUser.details = null;
    editUser.groupMembership = null;
    changeMode('users');
}
function userCallbackHandler() {
    editUser.details = null;
    editUser.groupMembership = null;
    editUser.queryDetails();
    editUser.queryGroupMembership();
    editUser.prepareUserInterface();
}
var editUser = {
    benutzer_id: null,
    details: null,
    groupMembership: null,
    queryDetails: function () {
        var url = "backend-api.php?mode=users&action=getById&u_id=" + editUser.benutzer_id;
        getData("get", url, null).done(editUser.setDetails);
    },
    queryGroupMembership: function () {
        var url = "backend-api.php?mode=users&action=getGroupMembership&u_id=" + editUser.benutzer_id;
        getData("get", url, null).done(editUser.setGroupMembership);
    },
    setDetails: function (data) {
        editUser.details = data;
    },
    setGroupMembership: function (data) {
        editUser.groupMembership = data;
    },
    prepareUserInterface: function () {
        if (editUser.details != null && editUser.groupMembership != null) {
            document.getElementById("main-top-heading").innerHTML = "Benutzer bearbeiten: " + editUser.details.benutzername;
            document.getElementById("newName").value = editUser.details.benutzername;
            if (editUser.details.aktiv == 1) {
                document.getElementById("button_deactivate").removeAttribute("hidden");
                document.getElementById("button_activate").setAttribute("hidden", true);
            } else {
                document.getElementById("button_activate").removeAttribute("hidden");
                document.getElementById("button_deactivate").setAttribute("hidden", true);
            }

            document.getElementById("memberOfBody").innerHTML = "";
            document.getElementById("notMemberOfBody").innerHTML = "";

            for (let row of editUser.groupMembership) {
                editUser.addToMemberhipTable(row);
            }
            removeLoadingOverlay();
        } else {
            //warten bis variablen durch ajax callback befüllt wurden
            setTimeout(editUser.prepareUserInterface, 50);
        }
    },
    addToMemberhipTable: function (row) {
        var newRow = '<tr g_id="' + row.gruppe_id + '">';
        newRow += "<td>" + row.gruppe_id + "</td>";
        newRow += "<td>" + row.gruppe_kuerzel + "</td>";
        newRow += "<td>" + row.gruppenname + "</td>";

        if (row.memberOf == 1) {
            newRow += '<td><i class="fas fa-minus clickable" onclick="editUser.removeFromGroup(' + row.gruppe_id + ')"></td>';
            newRow += "</tr>";
            document.getElementById("memberOfBody").innerHTML += newRow
        } else if (row.memberOf == 0) {
            newRow += '<td><i class="fas fa-plus clickable" onclick="editUser.addToGroup(' + row.gruppe_id + ')"></td>';
            newRow += "</tr>";
            document.getElementById("notMemberOfBody").innerHTML += newRow
        }
    },
    activate: function () {
        var url = "backend-api.php?mode=users&action=activate";
        var data = {
            id: editUser.benutzer_id
        }
        getData("post", url, data, "text").done(function(data){
            addNotification("success","Benutzer " + editUser.details.benutzername + " wurde aktiviert!");
            userCallbackHandler();
        });
    },
    deactivate: function () {
        var url = "backend-api.php?mode=users&action=deactivate";
        var data = {
            id: editUser.benutzer_id
        }
        getData("post", url, data, "text").done(function(data){
            addNotification("warning","Benutzer " + editUser.details.benutzername + " wurde deaktiviert!");
            userCallbackHandler();
        });
    },
    rename: function () {
        var url = "backend-api.php?mode=users&action=rename";
        var data = {
            name: document.getElementById("newName").value,
            id: editUser.benutzer_id
        }
        getData("post", url, data, "text").done(function(){
            addNotification("success","Benutzer " + editUser.benutzer_id + " wurde umbenannt!");
            userCallbackHandler();
        });
        disableInput(['newName', 'buttonSaveUsername'])
    },
    resetPasswort: function () {
        var url = "backend-api.php?mode=users&action=resetPw";
        var data = {
            id: editUser.benutzer_id,
            pw: document.getElementById("password1").value
        }
        getData("post", url, data, "text").done(function(){
            addNotification("success","Passwort von " + editUser.details.benutzername + " wurde zurückgesetzt!");
            userCallbackHandler();
        });
        document.getElementById("formPasswordReset").reset();
    },
    addToGroup: function (group_id) {
        var url = "backend-api.php?mode=users&action=addToGroup";
        var data = {
            u_id: editUser.benutzer_id,
            g_id: group_id
        }
        getData("post", url, data, "text").done(function(){
            addNotification("success",editUser.details.benutzername + " der Gruppe " + group_id + " hinzugefügt");
            userCallbackHandler();
        });
    },
    removeFromGroup: function (group_id) {
        var url = "backend-api.php?mode=users&action=removeFromGroup";
        var data = {
            u_id: editUser.benutzer_id,
            g_id: group_id
        }
        getData("post", url, data, "text").done(function(){
            addNotification("warning",editUser.details.benutzername + " von der Gruppe " + group_id + " entfernt");
            userCallbackHandler();
        });
    },
    delete: function () {
        var url = "backend-api.php?mode=users&action=delete";
        var data = {
            id: editUser.benutzer_id
        }
        getData("post", url, data, "text").done(function(){
            addNotification("danger",editUser.details.benutzername + " gelöscht!");
            closeEditUser();
        });
    }
};
/*
Funktionen für Gruppenverwaltung
*/
function loadGroupList(data) {
    if (data == null) {
        getData("get", "backend-api.php?mode=groups&action=getAll", null).done(loadGroupList);
    } else {
        // Build HTML table with given data
        var tbody = "";
        for (let row of data) {
            tbody += '<tr class="clickable" onclick="changeMode(\'editgroup\',' + row.gruppe_id + ')">';
            tbody += "<td>" + row.gruppe_id + "</td>";
            tbody += "<td>" + row.gruppe_kuerzel + "</td>";
            tbody += "<td>" + row.gruppenname + "</td>";
            tbody += "</tr>";
        }

        // Put table into HTML container
        document.getElementById("allGroupsBody").innerHTML = tbody;
        removeLoadingOverlay();
    }
}
function createGroup(){
    var data = {
        kuerzel: document.getElementById('kuerzel').value,
        name: document.getElementById('kursname').value,
    }
    getData("post", "backend-api.php?mode=groups&action=create", data).done(function(data){
        addNotification("success","Gruppe " + data.gruppe_id + " " + data.gruppenname + " wurde erfolgreich erstellt!");
        changeMode('groups');
    });
}
function initializeEditGroup(id) {
    editGroup.gruppen_id = id;
    editGroup.queryDetails();
    editGroup.queryGroupMembership();
    editGroup.prepareUserInterface();
}
function closeEditGroup() {
    editGroup.gruppen_id = null;
    editGroup.details = null;
    editGroup.groupMembership = null;
    changeMode('groups');
}
function groupCallbackHandler() {
    editGroup.details = null;
    editGroup.groupMembership = null;
    editGroup.queryDetails();
    editGroup.queryGroupMembership();
    editGroup.prepareUserInterface();
}
var editGroup = {
    gruppen_id: null,
    details: null,
    groupMembership: null,
    queryDetails: function () {
        var url = "backend-api.php?mode=groups&action=getById&g_id=" + editGroup.gruppen_id;
        getData("get", url, null).done(editGroup.setDetails);
    },
    queryGroupMembership: function () {
        var url = "backend-api.php?mode=groups&action=getGroupMembership&g_id=" + editGroup.gruppen_id;
        getData("get", url, null).done(editGroup.setGroupMembership);
    },
    setDetails: function (data) {
        editGroup.details = data;
    },
    setGroupMembership: function (data) {
        editGroup.groupMembership = data;
    },
    prepareUserInterface: function () {
        if (editGroup.details != null && editGroup.groupMembership != null) {
            document.getElementById("main-top-heading").innerHTML = "Gruppe bearbeiten: " + editGroup.details.gruppenname;
            document.getElementById("newKuerzel").value = editGroup.details.gruppe_kuerzel;
            document.getElementById("newName").value = editGroup.details.gruppenname;
            
            document.getElementById("memberBody").innerHTML = "";
            document.getElementById("notMemberBody").innerHTML = "";

            for (let row of editGroup.groupMembership) {
                editGroup.addToMemberhipTable(row);
            }
            removeLoadingOverlay();
        } else {
            //warten bis variablen durch ajax callback befüllt wurden
            setTimeout(editGroup.prepareUserInterface, 50);
        }
    },
    addToMemberhipTable: function (row) {
        //var newRow = '<tr class="clickable" onclick="changeMode(\'edituser\',' + row.benutzer_id + ')">';
        var newRow = '<tr>';
        newRow += "<td>" + row.benutzer_id + "</td>";
        newRow += "<td>" + row.benutzername + "</td>";
        newRow += "<td>" + row.aktiv + "</td>";
        newRow += "<td>" + row.datum_registriert + "</td>";
        newRow += "<td>" + row.datum_letzterlogin + "</td>";

        if (row.memberOf == 1) {
            newRow += '<td><i class="fas fa-minus clickable" onclick="editGroup.removeFromGroup(' + row.benutzer_id + ')"></td>';
            newRow += "</tr>";
            document.getElementById("memberBody").innerHTML += newRow
        } else if (row.memberOf == 0) {
            newRow += '<td><i class="fas fa-plus clickable" onclick="editGroup.addToGroup(' + row.benutzer_id + ')"></td>';
            newRow += "</tr>";
            document.getElementById("notMemberBody").innerHTML += newRow
        }
    },
    rename: function () {
        var url = "backend-api.php?mode=groups&action=rename";
        var data = {
            g_id : editGroup.gruppen_id,
            kuerzel: document.getElementById("newKuerzel").value,
            name: document.getElementById("newName").value
        }
        getData("post", url, data,"text").done(function(){
            addNotification("success","Gruppe " + editGroup.gruppen_id + " wurde umbenannt!");
            groupCallbackHandler();
        });        
        disableInput(['newName', 'newKuerzel','group_rename']);
    },
    addToGroup: function (benutzer_id) {
        var url = "backend-api.php?mode=groups&action=addToGroup";
        var data = {
            u_id: benutzer_id,
            g_id: editGroup.gruppen_id
        }
        getData("post", url, data, "text").done(function(){
            addNotification("success",data.u_id + " wurde der Gruppe " + editGroup.details.gruppenname + " hinzugefügt");
            groupCallbackHandler();
        });
    },
    removeFromGroup: function (benutzer_id) {
        var url = "backend-api.php?mode=groups&action=removeFromGroup";
        var data = {
            u_id: benutzer_id,
            g_id: editGroup.gruppen_id
        }
        getData("post", url, data,"text").done(function(){
            addNotification("warning",data.u_id + " wurde von der Gruppe " + editGroup.details.gruppenname + " entfernt");
            groupCallbackHandler();
        });
    },
    delete: function () {
        var url = "backend-api.php?mode=groups&action=delete";
        var data = {
            g_id: editGroup.gruppen_id
        }
        getData("post", url, data, "text").done(function(data){
            addNotification("danger",editGroup.details.gruppenname + " gelöscht!");
            closeEditGroup();
        });
    }
};

/*
Funktionen für Vorlesungsverwaltung
*/
function createLecture(data) {
    var data = {
        name: document.getElementById('newLecture').value,
    }
    getData("post", "backend-api.php?mode=lectures&action=create", data).done(function(data){
        changeMode('lectures');
        addNotification("success","Vorlesung " + data.vorlesung_id + " " + data.vorlesung_name + " wurde erfolgreich erstellt!");
    });
}
function loadLectureList(data) {
    if (data == null) {
        getData("get", "backend-api.php?mode=lectures&action=getAll", null).done(loadLectureList);
    } else {
        // Build HTML table with given data
        var tbody = "";
        for (let row of data) {
            tbody += '<tr class="clickable" onclick="changeMode(\'editlecture\',' + row.vorlesung_id + ')">';
            tbody += "<td>" + row.vorlesung_id + "</td>";
            tbody += "<td>" + row.vorlesung_name + "</td>";
            tbody += "</tr>";
        }
        // Put table into HTML container
        document.getElementById("allLecturesBody").innerHTML = tbody;
        removeLoadingOverlay();
    }
}
function initializeEditLecture(id) {
    editLecture.vorlesung_id = id;
    editLecture.details = null;
    editLecture.questions = null;
    editLecture.assignedGroups = null;
    editLecture.queryDetails();
    editLecture.queryQuestions();
    editLecture.queryAssignedGroups();
    editLecture.prepareUserInterface();
}
function closeEditLecture() {
    editLecture.vorlesung_id = null;
    editLecture.details = null;
    editLecture.questions = null;
    editLecture.assignedGroups = null;
    changeMode('lectures');
}
function editLectureCallbackHandler() {
    editLecture.details = null;
    editLecture.questions = null;
    editLecture.assignedGroups = null;
    editLecture.queryDetails();
    editLecture.queryQuestions();
    editLecture.queryAssignedGroups();
    editLecture.prepareUserInterface();
}
var editLecture = {
    vorlesung_id: null,
    details: null,
    questions: null,
    assignedGroups: null,

    queryDetails: function () {
        var url = "backend-api.php?mode=lectures&action=getById&v_id=" + editLecture.vorlesung_id;
        getData("get", url, null).done(editLecture.setDetails);
    },
    queryQuestions: function () {
        var url = "backend-api.php?mode=lectures&action=getQuestions&v_id=" + editLecture.vorlesung_id;
        getData("get", url, null).done(editLecture.setQuestions);
    },
    queryAssignedGroups: function () {
        var url = "backend-api.php?mode=lectures&action=getAssignedGroups&v_id=" + editLecture.vorlesung_id;
        getData("get", url, null).done(editLecture.setAssignedGroups);
    },
    setDetails: function (data) {
        editLecture.details = data;
    },
    setQuestions: function (data) {
        editLecture.questions = data;
    },
    setAssignedGroups: function (data) {
        editLecture.assignedGroups = data;
    },
    prepareUserInterface: function () {
        if (editLecture.details != null && editLecture.questions != null && editLecture.assignedGroups != null) {
            document.getElementById("main-top-heading").innerHTML = "Vorlesung bearbeiten: " + editLecture.details.vorlesung_name;
            document.getElementById("newName").value = editLecture.details.vorlesung_name;

            document.getElementById("questionsBody").innerHTML = "";
            document.getElementById("assignedToBody").innerHTML = "";
            document.getElementById("notAssignedToBody").innerHTML = "";

            for (let row of editLecture.questions) {
                editLecture.addToQuestionTable(row);
            }
            for (let row of editLecture.assignedGroups) {
                editLecture.addToAssignedToTable(row);
            }
            removeLoadingOverlay();
        } else {
            //warten bis variablen durch ajax callback befüllt wurden
            setTimeout(editLecture.prepareUserInterface, 50);
        }
    },
    addToAssignedToTable: function (row) {
        var newRow = '<tr g_id="' + row.gruppe_id + '">';
        newRow += "<td>" + row.gruppe_id + "</td>";
        newRow += "<td>" + row.gruppe_kuerzel + "</td>";
        newRow += "<td>" + row.gruppenname + "</td>";

        if (row.assignedTo == 1) {
            newRow += '<td><i class="fas fa-minus clickable" onclick="editLecture.unassignFromGroup(' + row.gruppe_id + ')"></td>';
            newRow += "</tr>";
            document.getElementById("assignedToBody").innerHTML += newRow
        } else if (row.assignedTo == 0) {
            newRow += '<td><i class="fas fa-plus clickable" onclick="editLecture.assignToGroup(' + row.gruppe_id + ')"></td>';
            newRow += "</tr>";
            document.getElementById("notAssignedToBody").innerHTML += newRow
        }
    },
    addToQuestionTable: function (row) {
        var newRow = '<tr>';
        newRow += "<td>" + row.frage_id + "</td>";
        newRow += '<td><a href="#" onclick="changeMode(\'editquestion\',' + row.frage_id + ')">' + row.frage_titel + "</a></td>";
        newRow += "<td>" + row.frage_typ_titel + "</td>";
        newRow += "<td>" + row.aktiv + "</td>";
        newRow += "<td>" + row.vorherige_version_id + "</td>";
        //newRow += "<td>" + row.fragenummer + "</td>";
        newRow += "<td>" + editLecture.addSortSelectInput(row.fragenummer, row.frage_id) + "</td>";
        newRow += "</tr>";

        document.getElementById("questionsBody").innerHTML += newRow
    },
    addSortSelectInput : function (rank, q_id){
        var select = '<select class="custom-select" onchange="(editLecture.onSortSelect(this.value))" id="questionRank">';

        //wenn noch keine nummer vergeben, baue select element im standard auf: -;1;2;3;...
        if(rank == null){
            select += "<option disabled selected value='" + '{"q_id":"' + q_id + '","rank":"null"}' + "'>-</option>"; 
            for (let i = 1; i < editLecture.questions.length+1; i++){
                select += "<option value='" + '{"q_id":"' + q_id + '","rank":"' + i + '"}' + "'>" + i + "</option>";                   
            }
        //wenn nummer vergeben, deaktiviere im select die nummer, die, die Frage eh schon hat
        }else if (rank != null){
            for (let i = 1; i < editLecture.questions.length+1; i++){
                if(rank == i){
                    select += "<option disabled selected value='" + '{"q_id":"' + q_id + '","rank":"' + rank + '"}' + "'>" + rank + "</option>";
                }else{
                    select += "<option value='" + '{"q_id":"' + q_id + '","rank":"' + i + '"}' + "'>" + i + "</option>";
                }    
            }
            //wenn vergebene nummer größer als die Anzahl der Fragen der Vorlesung ist setzt einfach die Nummer von Datenbank (rank)
            if(rank > editLecture.questions.length){
                select += "<option disabled selected value='" + '{"q_id":"' + q_id + '","rank":"' + rank + '"}' + "'>" + rank + "</option>";
            }
        }       
        select += '</select>';
        return select;
    },
    onSortSelect : function (value){
        selected = JSON.parse(value);
        var url = "backend-api.php?mode=lecturequestion&action=setRank";
        var data = {
            "q_id": selected.q_id,
            "rank": selected.rank
        }
        getData("post", url, data,"text").done(function() {
            addNotification("success","Reihenfolge geändert","Frage: " + data.q_id + "<br>Rang: " + data.rank);
            editLectureCallbackHandler();
        });
    },
    rename: function () {
        var url = "backend-api.php?mode=lectures&action=rename";
        var data = {
            name: document.getElementById("newName").value,
            v_id: editLecture.vorlesung_id
        }
        getData("post", url, data, "text").done(function () {
            editLectureCallbackHandler();
            addNotification("success","Vorlesung " + editLecture.vorlesung_id + " wurde umbenannt!");
        });
        disableInput(['newName', 'buttonSaveLectureName'])
    },
    assignToGroup: function (group_id) {
        var url = "backend-api.php?mode=lectures&action=assignToGroup";
        var data = {
            v_id: editLecture.vorlesung_id,
            g_id: group_id
        }
        getData("post", url, data, "text").done(function () {
            editLectureCallbackHandler();
            addNotification("success","Vorlesung " + editLecture.vorlesung_id + " der Gruppe " + data.g_id + " zugeordnet");
        });
    },
    unassignFromGroup: function (group_id) {
        var url = "backend-api.php?mode=lectures&action=unassignFromGroup";
        var data = {
            v_id: editLecture.vorlesung_id,
            g_id: group_id
        }
        getData("post", url, data, "text").done(function () {
            editLectureCallbackHandler();
            addNotification("warning","Vorlesung " + editLecture.vorlesung_id + " von der Gruppe " + data.g_id + " entfernt");
        });
    },
    delete: function () {
        var url = "backend-api.php?mode=lectures&action=delete";
        var data = {
            v_id: editLecture.vorlesung_id
        }
        getData("post", url, data, "text").done(function(data){
            addNotification("danger", editLecture.details.vorlesung_name + " gelöscht!");
            closeEditLecture();
        });        
    }
}

function initializeCreateQuestion() {
    createQuestion.queryQuestionTypes();
    createQuestion.prepareUserInterface();
}
function closeCreateQuestion() {
    questionTypes = null;
    changeMode('editlecture',editLecture.vorlesung_id);
}
var createQuestion = {
    questionTypes: null,

    queryQuestionTypes: function () {
        var url = "backend-api.php?mode=lecturequestion&action=getQuestionTypes";
        getData("get", url, null).done(createQuestion.setQuestionTypes);
    },
    setQuestionTypes: function (data) {
        createQuestion.questionTypes = data;
    },
    prepareUserInterface: function () {
        if (createQuestion.questionTypes != null) {
            var default_option = '<option disabled selected value="">Fragentyp wählen</option>';
            document.getElementById("question_type").innerHTML = default_option;
            document.getElementById("question_text").focus();
            var option = "";
            for (let row of createQuestion.questionTypes) {
                option = '<option value="' + row.frage_typ_id + '">' + row.frage_typ_titel + '</option>'
                document.getElementById("question_type").innerHTML += option;
            }
            removeLoadingOverlay();
        } else {
            //warten bis variablen durch ajax callback befüllt wurden
            setTimeout(createQuestion.prepareUserInterface, 50);
        }
    },
    typeChanged: function (value) {
        //document.getElementById('question_text').setAttribute('readonly', 'readonly');
        //document.getElementById('question_type').setAttribute('readonly', 'readonly');
        optionsContainer = document.getElementById('question_options');
        switch (value) {
            case '1':
                optionsContainer.innerHTML = "";
                document.getElementById('setOptions').style.visibility = "hidden";
                createQuestion.setTypeHelp(value);
                break;
            case '2':
                if (optionsContainer.innerHTML == "") {
                    createQuestion.question_option_add('question_options');
                }
                document.getElementById('setOptions').style.visibility = "visible";
                createQuestion.setTypeHelp(value);
                break;
            case '3':
                if (optionsContainer.innerHTML == "") {
                    createQuestion.question_option_add('question_options');
                }
                document.getElementById('setOptions').style.visibility = "visible";
                createQuestion.setTypeHelp(value);
                break;
            default:
                break;
        }
        document.getElementById('save_question').removeAttribute('disabled');
    },
    setTypeHelp: function (type) {
        for (var i = 0; i < createQuestion.questionTypes.length; i++) {
            var obj = createQuestion.questionTypes[i];
            if (obj.frage_typ_id == type) {
                document.getElementById('question_type_help').innerHTML = obj.frage_typ_beschreibung;
            }
        }
    },
    question_option_add: function (div_id) {
        var inp = document.createElement("input");
        inp.className = "form-control mt-1";
        inp.name = "question_option[]"
        inp.id = "question_option";
        inp.type = "text";
        document.getElementById(div_id).appendChild(inp);
        inp.focus();
    },
    question_option_remove: function (div_id) {
        var div = document.getElementById(div_id);
        if (div.childNodes.length != 0) {
            div.removeChild(div.lastChild);
        }
    },
    reset_page: function () {
        changeMode(createQuestion);
    },
    save: function () {
        var url = "backend-api.php?mode=lecturequestion&action=create";
        var data = {
            "v_id": editLecture.vorlesung_id,
            "question_text": document.getElementById("question_text").value,
            "question_type": document.getElementById("question_type").value,
            //"question_option": []
        }
        if(data.question_type != 1){
            data.question_option = []
            document.getElementById('question_options').childNodes.forEach(function (item) {
                if (item.value != null) {
                    data.question_option.push(item.value);
                }
            });
        }        
        getData("post", url, data).done(function(response){
            addNotification("success", "Frage " + response.frage_id + " wurde erfolgreich erstellt!")
            changeMode('editlecture', editLecture.vorlesung_id);
        });        
    }
}

function initializeEditQuestion(id) {
    editQuestion.questionId = id;

    editQuestion.queryQuestionDetails();
    editQuestion.queryQuestionAnswerOptions();
    editQuestion.queryQuestionTypes();
    editQuestion.queryHasGivenAnswers();
    editQuestion.prepareUserInterface();
}
function closeEditQuestion() {
    editQuestion.questionId = null;
    editQuestion.questionDetails = null;
    editQuestion.questionTypes = null;
    editQuestion.questionAnswerOptions = null;
    editQuestion.hasGivenAnswers = null;
    changeMode('editlecture', editLecture.vorlesung_id);
}
var editQuestion = {
    questionId: null,
    questionDetails: null,
    questionTypes: null,
    questionAnswers: null,
    hasGivenAnswers: null,

    queryQuestionTypes: function () {
        var url = "backend-api.php?mode=lecturequestion&action=getQuestionTypes";
        getData("post", url, null).done(editQuestion.setQuestionTypes);
    },
    queryQuestionDetails: function () {
        var url = "backend-api.php?mode=lecturequestion&action=getById&q_id=" + editQuestion.questionId;
        getData("post", url, null).done(editQuestion.setQuestionDetails);
    },
    queryQuestionAnswerOptions: function () {
        var url = "backend-api.php?mode=lecturequestion&action=getAllAnswerOptionsByQId&q_id=" + editQuestion.questionId;
        getData("post", url, null).done(editQuestion.setQuestionAnswerOptions);
    },
    queryHasGivenAnswers: function () {
        var url = "backend-api.php?mode=lecturequestion&action=hasGivenAnswer&q_id=" + editQuestion.questionId;
        getData("post", url, null).done(editQuestion.setHasGivenAnswers);
    },
    setQuestionTypes: function (data) {
        editQuestion.questionTypes = data;
    },
    setQuestionDetails: function (data) {
        editQuestion.questionDetails = data;
    },
    setQuestionAnswerOptions: function (data) {
        editQuestion.questionAnswerOptions = data;
    },
    setHasGivenAnswers: function (data) {
        editQuestion.hasGivenAnswers = data;
    },
    prepareUserInterface: function () {
        if (editQuestion.questionTypes != null && editQuestion.questionDetails != null && editQuestion.hasGivenAnswers != null) {

            document.getElementById('main-top-heading').innerHTML = "Frage bearbeiten: " + editQuestion.questionDetails.frage_titel;
            document.getElementById('question_text').value = editQuestion.questionDetails.frage_titel;

            //wenn schon antworten gespeichert sind -> Warnung sichtbar machen
            if (editQuestion.hasGivenAnswers) {
                var changeQuestionAlert = document.getElementById('changeQuestionAlert');
                changeQuestionAlert.style.visibility = "visible";
                changeQuestionAlert.style.height = "";
            }

            //fragentyp select aufbauen
            var option = "";
            for (let row of editQuestion.questionTypes) {
                if(editQuestion.questionDetails.frage_typ_id == row.frage_typ_id){
                    option = '<option selected value="' + row.frage_typ_id + '">' + row.frage_typ_titel + '</option>'
                }else{
                    option = '<option value="' + row.frage_typ_id + '">' + row.frage_typ_titel + '</option>'
                }                
                document.getElementById("question_type").innerHTML += option;
            }
            
            editQuestion.setTypeHelp(editQuestion.questionDetails.frage_typ_id);

            //nur bei Fragen ungleich Typ Freitext durchführen
            if (editQuestion.questionDetails.frage_typ_id != 1) {
                if(editQuestion.questionAnswerOptions != null){
                    document.getElementById('setOptions').style.visibility = "visible";

                    // if (editQuestion.questionDetails.frage_typ_id == 2) {
                    //     var select_typ_body = '<option selected value="2">Single Choice</option><option value="3">Multiple Choice</option>'
                    // } else if (editQuestion.questionDetails.frage_typ_id == 3) {
                    //     var select_typ_body = '<option selected value="3">Multiple Choice</option><option value="2">Single Choice</option>'
                    // }
                    // document.getElementById('question_type').innerHTML = select_typ_body;

                    var count = 1;
                    for (let option of editQuestion.questionAnswerOptions) {
                        var question_option_input_id = '';
                        var question_option = '';
                        question_option_input_id = "question_option_" + count;
                        question_option = '<div class="input-group mb-3">' +
                            '<input disabled type="text" class="form-control" id="' + question_option_input_id + '" placeholder="Anwortmöglichkeit" value="' + option["antwort"] + '">' +
                            '<div class="input-group-append">' +
                            '<button class="btn btn-secondary" type="button" onclick="enableInput([\'' + question_option_input_id + '\'])"><i class="fas fa-edit"></i></button>' +
                            '</div>' +
                            '</div>';
                        document.getElementById('question_options').innerHTML += question_option;
                        count++;
                    }
                    //removeLoadingOverlay();           
                }else {
                    //warten bis variablen durch ajax callback befüllt wurden
                    setTimeout(editQuestion.prepareUserInterface, 50);
                }
            }
            removeLoadingOverlay();
        } else {
            //warten bis variablen durch ajax callback befüllt wurden
            setTimeout(editQuestion.prepareUserInterface, 50);
        }
    },
    typeChanged: function (value) {
        optionsContainer = document.getElementById('question_options');
        switch (value) {
            case '1':
                optionsContainer.innerHTML = "";
                document.getElementById('setOptions').style.visibility = "hidden";
                editQuestion.setTypeHelp(value);
                break;
            case '2':
                if (optionsContainer.innerHTML == "") {
                    editQuestion.question_option_add('question_options');
                }
                document.getElementById('setOptions').style.visibility = "visible";
                editQuestion.setTypeHelp(value);
                break;
            case '3':
                if (optionsContainer.innerHTML == "") {
                    editQuestion.question_option_add('question_options');
                }
                document.getElementById('setOptions').style.visibility = "visible";
                editQuestion.setTypeHelp(value);
                break;
            default:
                break;
        }
    },
    setTypeHelp: function (type) {
        for (var i = 0; i < editQuestion.questionTypes.length; i++) {
            var obj = editQuestion.questionTypes[i];
            if (obj.frage_typ_id == type) {
                document.getElementById('question_type_help').innerHTML = obj.frage_typ_beschreibung;
            }
        }
    },
    question_option_add: function (div_id) {
        var inp = document.createElement("input");
        inp.className = "form-control mt-1";
        inp.name = "question_option[]"
        inp.id = "question_option";
        inp.type = "text";
        document.getElementById(div_id).appendChild(inp);
        inp.focus();
    },
    question_option_remove: function (div_id) {
        var div = document.getElementById(div_id);
        if (div.childNodes.length != 0) {
            div.removeChild(div.lastChild);
        }
    },
    delete: function () {
        var url = "backend-api.php?mode=lecturequestion&action=delete";
        var data = {
            "q_id": editQuestion.questionId
        }
        getData("post", url, data, "text").done(function () {
            addNotification("danger", "Frage " + data.q_id + " gelöscht!");
            closeEditQuestion();
        });
        
    },
    save: function () {
        if (editQuestion.hasGivenAnswers) {
            var url = "backend-api.php?mode=lecturequestion&action=createNewVersion";
        } else {
            var url = "backend-api.php?mode=lecturequestion&action=modifyExistingVersion";
        }
        var data = {
            "q_id": editQuestion.questionId,
            "v_id": editQuestion.questionDetails.vorlesung_id,
            "question_text": document.getElementById("question_text").value,
            "question_type": document.getElementById("question_type").value
        }

        //rank hinzufügen, falls ungleich null
        if(editQuestion.questionDetails.fragenummer != null){
            data.question_rank = editQuestion.questionDetails.fragenummer;
        }

        //antwortmöglichkeiten hinzufügen
        if(data.question_type != 1){
            var question_option = [];
            document.getElementById('question_options').childNodes.forEach(function (item) {
                //nochmal, da inputs in inputgroup sind
                item.childNodes.forEach(function (item) {
                    if (item.value != null) {
                        question_option.push(item.value);
                    }
                });
                //mit plus hinzugefügte optionen sind nicht in inputgroup
                if (item.value != null) {
                    question_option.push(item.value);
                }
            });
            data.question_option = question_option;
        }
        getData("post", url, data).done(function (response) {
            //benachrichtung anders, wenn neue Version erstellt wird
            if (editQuestion.hasGivenAnswers) {
                addNotification("success", "Neue Frage " + response.frage_id + " erstellt!");
            }else{
                addNotification("success", "Frage " + response.frage_id + " gespeichert!");
            }            
            closeEditQuestion();
        });
    }
    /*
    Funktion implementieren, die prüft, ob Änderungen vorgenommen wurden, um dann den Speichern Button zu aktiviern
    ggfs. mit onblur event
    */
}
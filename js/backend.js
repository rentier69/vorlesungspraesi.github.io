function getData(methodType, url, post_data, dataType = "json", id) {
    //https://api.jquery.com/jquery.ajax/
    return $.ajax({
        url: url,
        method: methodType,
        dataType: dataType,
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
        document.getElementById(input).setAttribute('disabled','true');
    });
}
//item_id benötigt für die Bearbeitung von best. Benutzern / Gruppen / Vorlesungen
function changeMode(mode,item_id){
    var activeNav = nav.querySelector('.active');
    activeNav.classList.remove("active");
    switch (mode) {               
        case "home":
            getData("get", "static/home.html", null, "html").done(setStaticHtml);
            document.getElementById("nav_home").classList.add("active");
            break;     
        case "lectures":
            getData("get", "static/lectures.html", null, "html").done(setStaticHtml);
            document.getElementById("nav_lectures").classList.add("active");                        
            break;
        case "users":
            getData("get", "static/users.html", null, "html").done(setStaticHtml);
            loadUserList();
            document.getElementById("nav_users").classList.add("active");                        
            break;
        case "useredit":
            getData("get", "static/useredit.html", null, "html").done(function(data){
                setStaticHtml(data);
                initializeUserEdit(item_id);
            });
            document.getElementById("nav_users").classList.add("active");
            break;
        case "groups":
            getData("get", "static/groups.html", null, "html").done(setStaticHtml);
            document.getElementById("nav_groups").classList.add("active");                        
            break;                
        default:
            break;
    }
}
function setStaticHtml(data){
    document.getElementById("main").innerHTML = data;
}
/*
Funktionen für Benutzerverwaltung
*/
function createUser(data) {
    if (data == null) {
        var data = {
            username: document.getElementById('username').value,
            pw: document.getElementById('password1').value,
            user_type: document.getElementById('user_type').value
        }

        getData("post", "backend-api.php?mode=users&action=create", data).done(createUser);
    } else {
        //var data = JSON.parse(data);

        // Build HTML table row with given data
        newRow = '<tr onclick="changeMode(\'useredit\',' + data.benutzer_id + ')">';
        newRow += "<td>" + data.benutzer_id + "</td>";
        newRow += "<td>" + data.benutzername + "</td>";
        newRow += "<td>" + data.aktiv + "</td>";
        newRow += "<td>" + data.datum_registriert + "</td>";
        newRow += "<td>" + data.datum_letzterlogin + "</td>";
        newRow += "</tr>";

        // Put table into HTML container
        document.getElementById("allUsersBody").innerHTML += newRow;

        document.getElementById("formUserCreate").reset();
    }
}
function loadUserList(data) {
    if (data == null) {
        getData("get", "backend-api.php?mode=users&action=getAll", null).done(loadUserList);
    } else {
        // Build HTML table with given data
        var tbody = "";
        for (let row of data) {
            tbody += '<tr onclick="changeMode(\'useredit\',' + row.benutzer_id + ')">';
            tbody += "<td>" + row.benutzer_id + "</td>";
            tbody += "<td>" + row.benutzername + "</td>";
            tbody += "<td>" + row.aktiv + "</td>";
            tbody += "<td>" + row.datum_registriert + "</td>";
            tbody += "<td>" + row.datum_letzterlogin + "</td>";
            tbody += "</tr>";
        }

        // Put table into HTML container
        document.getElementById("allUsersBody").innerHTML += tbody;
    }
}
function searchUsers() {
    // Declare variables
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("allUsers");
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
function initializeUserEdit(id){
    userEdit.benutzer_id = id;
    userEdit.queryDetails();
    userEdit.queryGroupMembership();
    userEdit.prepareUserInterface();
}
function closeUserEdit(){
    userEdit.benutzer_id = null;
    userEdit.details = null;
    userEdit.groupMembership = null;
}
function userCallbackHandler(){
    userEdit.details = null;
    userEdit.groupMembership = null;
    userEdit.queryDetails();
    userEdit.queryGroupMembership();
    userEdit.prepareUserInterface();
}
var userEdit = {
    benutzer_id: null,
    details : null,
    groupMembership: null,
    queryDetails : function() {
        var url = "backend-api.php?mode=users&action=getById&u_id=" + userEdit.benutzer_id;
        getData("get", url, null).done(userEdit.setDetails);
    },
    queryGroupMembership : function (){
        var url = "backend-api.php?mode=users&action=getGroupMembership&u_id=" + userEdit.benutzer_id;
        getData("get", url, null).done(userEdit.setGroupMembership);
    },
    setDetails : function(data) {
        userEdit.details = data;
    },
    setGroupMembership : function(data){
        userEdit.groupMembership = data;
    },
    prepareUserInterface : function () {
        if(userEdit.details != null && userEdit.groupMembership != null ){
            document.getElementById("main-top-heading").innerHTML = "Benutzer bearbeiten: " + userEdit.details.benutzername;
            document.getElementById("newName").value = userEdit.details.benutzername;
            if (userEdit.details.aktiv == 1) {
                document.getElementById("button_deactivate").removeAttribute("hidden");
                document.getElementById("button_activate").setAttribute("hidden", true);
            } else {
                document.getElementById("button_activate").removeAttribute("hidden");
                document.getElementById("button_deactivate").setAttribute("hidden", true);
            }

            document.getElementById("memberOfBody").innerHTML = "";
            document.getElementById("notMemberOfBody").innerHTML = "";

            for (let row of userEdit.groupMembership) {
                userEdit.addToMemberhipTable(row);
            }
        }else{
            //warten bis variablen durch ajax callback befüllt wurden
            setTimeout(userEdit.prepareUserInterface, 50);
        }        
    },
    addToMemberhipTable : function(row){
        var newRow = '<tr g_id="' + row.gruppe_id + '">';
        newRow += "<td>" + row.gruppe_id + "</td>";
        newRow += "<td>" + row.gruppe_kuerzel + "</td>";
        newRow += "<td>" + row.gruppenname + "</td>";

        if(row.memberOf == 1){
            newRow += '<td><i class="fas fa-user-minus" onclick="userEdit.removeFromGroup(' + row.gruppe_id +')"></td>';
            newRow += "</tr>";
            document.getElementById("memberOfBody").innerHTML += newRow
        }else if(row.memberOf == 0){
            newRow += '<td><i class="fas fa-user-plus" onclick="userEdit.addToGroup(' + row.gruppe_id +')"></td>';
            newRow += "</tr>";
            document.getElementById("notMemberOfBody").innerHTML += newRow
        }        
    },
    activate : function () {
        var url = "backend-api.php?mode=users&action=activate";
        var data = {
            id: userEdit.benutzer_id
        }
        getData("post", url, data).done(userCallbackHandler);
    },
    deactivate : function () {
        var url = "backend-api.php?mode=users&action=deactivate";
        var data = {
            id: userEdit.benutzer_id
        }
        getData("post", url, data).done(userCallbackHandler);
    },
    rename : function () {
        var url = "backend-api.php?mode=users&action=rename";
        var data = {
            name: document.getElementById("newName").value,
            id: userEdit.benutzer_id
        }
        getData("post", url, data).done(userCallbackHandler);
        disableInput(['newName','buttonSaveUsername'])
    },
    resetPasswort : function () {
        var url = "backend-api.php?mode=users&action=resetPw";
        var data = {
            id: userEdit.benutzer_id,
            pw: document.getElementById("password1").value                
        }
        getData("post", url, data).done(userCallbackHandler);
    },
    addToGroup : function (group_id) {
        var url = "backend-api.php?mode=users&action=addToGroup";
        var data = {
            u_id: userEdit.benutzer_id,
            g_id: group_id
        }
        getData("post", url, data).done(userCallbackHandler);
    },
    removeFromGroup : function (group_id) {
        var url = "backend-api.php?mode=users&action=removeFromGroup";
        var data = {
            u_id: userEdit.benutzer_id,
            g_id: group_id
        }
        getData("post", url, data).done(userCallbackHandler);
    },
    delete : function (){
            var url = "backend-api.php?mode=users&action=delete";
            var data = {
                id: userEdit.benutzer_id
            }
            getData("post", url, data).done();
    }
};
/*
Funktionen für Gruppenverwaltung
*/

/*
Funktionen für Vorlesungsverwaltung
*/
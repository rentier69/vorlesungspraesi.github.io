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

function closeLecture(v_id){
    //irgendwie bei schließen des Browserfensters ausführen
    data = {
        "v_id": v_id
    }
    getData("post", "lecture-api.php?action=closeLecture", data, "text").done(prepareHomePage);
}

function getActiveLectures(){
    getData("get", "lecture-api.php?action=getActiveLectures", null).done(prepareFrontendUI);
}

function prepareFrontendUI(data){
    //ggfs. als Tabelle darstellen
    for(let row of data){
        option = '<option value="' + row.vorlesung_id + '">' + row.vorlesung_name + " - " + row.zeit_gestartet +  '</option>'
        document.getElementById("lectureToJoin").innerHTML += option;
    }
}

function sendMessage(v_id){
    data = {
        "v_id": v_id,
        "text": document.getElementById('chatMessage')
        
    }
    getData("post", "lecture-api.php?action=postMessage", data, "text").done();
}
function getMessage(v_id) {
    getData("post", "lecture-api.php?action=postMessage", data, "text").done();
}
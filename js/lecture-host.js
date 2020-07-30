/*
Bildschirm freigeben (ggfs auch bei gemeinsam)
Aufgaben auswählen und stellen
Vorlesung starten
*/


function deleteChat(){
    data = {
        "v_id": v_id
    }
    getData("post", "api/lecture-host-api.php?action=deleteChat", data, "text");
}

function closeLecture(v_id) {
    //irgendwie bei schließen des Browserfensters ausführen - erledigt - eventListener in lecture-host.php
    data = {
        "v_id": v_id
    }
    deleteChat();
    getData("post", "api/lecture-host-api.php?action=closeLecture", data, "text");
}
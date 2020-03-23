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

function createUser(event){
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', "backend-api.php?mode=users&action=create", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    var user = "username=" + document.getElementById('username').value + "&pw=" + document.getElementById('password1').value +  "&user_type=" + document.getElementById('user_type').value;
    xhr.send(user);

    xhr.onload = function () {
        // Parse JSON data first
        // Will give you back an array
        var data = JSON.parse(this.response);
        console.log(data);

        // Build HTML table row with given data
        
        newRow = "<tr>";
        newRow += "<td>" + data.benutzer_id + "</td>";
        newRow += "<td>" + data.benutzername + "</td>";
        newRow += "<td>" + data.aktiv + "</td>";
        newRow += "<td>" + data.datum_registriert + "</td>";
        newRow += "<td>" + data.datum_letzterlogin + "</td>";
        newRow += "</tr>";
            

        // Put table into HTML container
        document.getElementById("allUsersBody").innerHTML += newRow;
        
        document.getElementById("formUserCreate").reset();
    };
}
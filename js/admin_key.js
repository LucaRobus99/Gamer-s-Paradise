const Url_gameId = new URLSearchParams(window.location.search);
const id = Url_gameId.get('id');


function removeKeyFromTable(keyId) {
    var buttonToDelete = $('[data-key-id="' + keyId + '"]');
    var rowToRemove = buttonToDelete.closest('tr');
    rowToRemove.remove();
}
function deleteKey(keyId) {
    console.log(keyId);
    $.ajax({
        type: "POST",
        url: "../controllers/admin_key.php",
        data: {
            action: 'delete_key',
            idKey: keyId
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                // Rimuovi la riga della tabella corrispondente alla chiave eliminata
                removeKeyFromTable(keyId);
                $('#error').empty();
            }else{
                $('#error').empty(); // Rimuove eventuali messaggi precedenti
                $('#error').append('<div class="alert alert-danger">' + response.message + '</div>');


            }
        },
        error: function (error) {
            console.log(error);
        }
    });
}



function displayNewKey(key) {
    var tableBody = $("#chiaviTabella");
    var row = $("<tr>");
    var keyCell = $("<td>").text(key.key_value);
    var statoCell = $("<td>").text(key.acquired === '0' ? 'Disponibile' : 'Acquistata');
    var azioniCell = $("<td>");

    var eliminaButton = $("<button>")
        .text("Elimina")
        .addClass("btn btn-danger")
        .attr("data-key-id", key.id);

    eliminaButton.on("click", function () {
        var keyId = $(this).attr("data-key-id");

        deleteKey(keyId);

    });

    azioniCell.append(eliminaButton);
    row.append(keyCell, statoCell, azioniCell);
    tableBody.append(row);
}



function addKey(id,key_value){
    $.ajax({
        type: "POST",
        url: "../controllers/admin_key.php",
        data: {
            action: 'add_key',
            key_value:key_value,
            idGame: id // Sostituisci con l'ID del gioco desiderato
        },
        dataType:'json',
        success: function(response) {
            if(response.success){
                displayNewKey(response.lastkey);
                $('#error').empty();
            }else{
                $('#error').empty(); // Rimuove eventuali messaggi precedenti
                $('#error').append('<div class="alert alert-danger">' + response.message + '</div>');

            }


        },
        error: function(error) {
            console.log(error);
        }
    });
}



function renderKeysTable(data) {
    var tableBody = $("#chiaviTabella");

    tableBody.empty(); // Svuota la tabella prima di riempirla con i nuovi dati

    data.forEach(function(key) {
        var row = $("<tr>");
        var keyCell = $("<td>").text(key.key_value);
        var statoCell = $("<td>").text(key.acquired === '0' ? 'Disponibile' : 'Acquistata');
        var azioniCell = $("<td>");

        var eliminaButton = $("<button>")
            .text("Elimina")
            .addClass("btn btn-danger")
            .attr("data-key-id", key.id);

        eliminaButton.on("click", function () {
            var keyId = $(this).attr("data-key-id");

            deleteKey(keyId);

        });

        azioniCell.append(eliminaButton);
        row.append(keyCell, statoCell, azioniCell);
        tableBody.append(row);
    });
}


function displayInfoGame(data) {
    var container = $("#gameDataContainer");

    var gameDiv = $("<div>").addClass("row justify-content-center");
    var gameInfoDiv = $("<div>").addClass("col-md-8 text-center");

    var image = "../img/" + data.cover;
    var img = $("<img>").attr("src", image).attr("alt", "Immagine del Gioco").addClass("game-image");
    var h2 = $("<h2>").text(data.title);
    var price = $("<p>").text("Prezzo: €" + data.price); // Modifica il testo per visualizzare "€" al posto di "$"
    var genre = $("<p>").text("Genere: " + data.genre);

    gameInfoDiv.append(img, h2, price, genre);
    gameDiv.append(gameInfoDiv);
    container.html(gameDiv);
}


function getGameinfo(id){
    $.ajax({
        type: "GET",
        url: "../controllers/admin_key.php",
        data: {
            action: 'get_info_game',
            id: id // Sostituisci con l'ID del gioco desiderato
        },
        dataType:'json',
        success: function(response) {

            console.log(response);
            displayInfoGame(response.GameInfo);
            renderKeysTable(response.Keys);
        },
        error: function(error) {
            console.log(error);
        }
    });
}

$(document).ready(function() {
    getGameinfo(id);
    $("#aggiungiChiaveForm").submit(function(event) {
        event.preventDefault(); // Evita la ricarica della pagina

        // Recupera il valore della chiave da input
        var chiaveValue = $("#chiave").val();

        // Esegui un'azione come l'aggiunta della chiave


        addKey(id,chiaveValue);
        $("#chiave").val("");
    });
});
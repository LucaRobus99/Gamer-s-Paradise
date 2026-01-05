const Url_gameId = new URLSearchParams(window.location.search);
const id = Url_gameId.get('id');
$(document).ready(function() {

    getGameInfo('../controllers/game_profile.php', id);

});

function getGameInfo(endpointUrl, gameId) {
    $.ajax({
        url: endpointUrl,
        method: 'GET',
        dataType: 'json',
        data: { gameId: gameId },
        success: function(response) {
            if (response.success) {
                console.log(response);
                updateGameInfo(response.gameInfo);
                $('#add-to-cart-btn').click(function() {
                    addToCart();
                });

            } else {
                handleGameError(response.message);
            }
        },
        error: function() {
            window.location.href = '../';
        }
    });
}

function handleGameError(errorMessage) {
    // Crea un elemento <h1> con il testo dell'errorMessage
    var errorElement = $('<h1>').text(errorMessage);

    // Sostituisci il contenuto dell'elemento con id "game-info" con il nuovo elemento <h1>
    $('#game-info .row').html(errorElement);
}


function updateGameInfo(gameInfo) {
    var gameHtml = `
        <div class="col-md-6">
            <img src="../img/${gameInfo.cover}" class="img-fluid" alt="Immagine del Gioco">
            <h2>Trama</h2>
            <p>${gameInfo.description}</p>
        </div>
        <div class="col-md-5">
            <h1>${gameInfo.title}</h1>
            <p>Piattaforma:${gameInfo.platform}</p>
            <p id='price'> ${gameInfo.price}&euro;</p>
         
    `;

    // Verifica se il campo "quantity" è maggiore di 0
    if (gameInfo.quantity > 0) {
        gameHtml += `
            <button id="add-to-cart-btn" class="btn btn-primary">Aggiungi al Carrello</button>
        `;
    } else {
        gameHtml += `
            <p id='nokey'>Key al momento non disponibili</p>
        `;
    }
    gameHtml += `
        </div>
    `;

    $('#game-info .row').html(gameHtml);
}

function addToCart() {
     // Imposta l'ID del gioco
    $.ajax({
        url: '../controllers/game_profile.php',
        method: 'POST',
        dataType: 'json',
        data: {gameId: id},
        success: function (response) {
            if (response.success) {

                var message=response.message;
                Swal.fire('Aggiunto al Carrello', message, 'success');
                updateCartItemCount(response.cartItemCount);
            } else {
                var error=response.message;
                if(error === 'Utente non accesso,reindirizzamento verso la pagina di login'){
                    window.location.href = 'login_view.php';
                }else{
                    Swal.fire('Non puoi aggiungere questo prodotto al carello', error, 'error');
                }

            }

        },
        error: function (error) {
           console.log('error');
        }
    });
}
function updateCartItemCount(count) {
    var currentCount = parseInt($('#cart-items-count').text());


    var ris = 1 + currentCount;


    $('#cart-items-count').text(ris);
}
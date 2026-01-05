// Funzione per aggiornare la quantità nel carrello tramite una chiamata AJAX



function updateCart(response) {
    const gamesList = $('.games-list');
    const cartSummary = $('.cart-summary');
    let subtotal = 0;

    if (response.length === 0) {
        const emptyCartHtml = `
                    <div class="empty-cart">
                        <h2>Carrello Vuoto :(</h2>
                        <p>Corri subito ad aggiungere qualcosa al tuo carrello!</p>
                    </div>
                `;
        gamesList.html(emptyCartHtml);
        cartSummary.hide();
    } else {
        let gamesHtml = '', riepilogoHtml = '';

        response.forEach(function (game) {
            gamesHtml += `
                       <div class="game-card">
                       <h3>${game.title}</h3>
                            <img src="../img/${game.cover}" alt="${game.title}">
                            
                            <p>Prezzo:  ${game.price}&euro;</p>
                            <p>Quantità: 
                               <button class="quantity-decrease" data-id="${game.id}">-</button>
                                <span class="quantity-value">${game.quantity}</span>
                               <button class="quantity-increase" data-id="${game.id}">+</button>
                            </p>

                            <button class="remove-button" data-id="${game.id}">Rimuovi</button>
                        </div>
                    `;

            riepilogoHtml += `
                <p>${game.title} x ${game.quantity} ${(game.quantity * game.price).toFixed(2)}&euro;</p>`;

            subtotal += parseFloat(game.price) * game.quantity;
        });
        const cartSummaryHtml = generateCartSummaryHtml(subtotal, riepilogoHtml);


        gamesList.html(gamesHtml);
        cartSummary.html(cartSummaryHtml);
        cartSummary.show(); // Mostra il riepilogo del carrello
    }


}
function generateCartSummaryHtml(subtotal, riepilogoHtml) {
    return `
        <h2>Riepilogo Carrello</h2>
        ${riepilogoHtml}
        <p>Totale: ${subtotal.toFixed(2)}&euro;</p>
        
        <a href="../view/payment_view.php" class="checkout-button">Procedi al pagamento</a>
    `;
}
function increaseQuantity() {
    const gameId = $(this).data('id');

    $.ajax({
        url: '../controllers/update_cart.php',
        data: { gameId: gameId, action: 'increase' },
        type: 'POST',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                window.location.reload();

            } else  {
                Swal.fire('Hai raggiunto il limite!', response.message, 'error');
            }
        },
        error: function () {
            Swal.fire('Errore', 'Si è verificato un errore durante la richiesta.', 'error');
        }
    });

}

function decreaseQuantity() {
    const gameId = $(this).data('id');


        $.ajax({
            url: '../controllers/update_cart.php',
            data: { gameId: gameId, action: 'decrease' },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    window.location.reload();

                } else  {
                    Swal.fire('Hai raggiunto il limite!', response.message, 'error');
                }
            },
            error: function () {
                Swal.fire('Errore', 'Si è verificato un errore durante la richiesta.', 'error');
            }
        });

}

function removeGame() {
    const gameId = $(this).data('id');

    $.ajax({
        url: '../controllers/update_cart.php', // Assumi che ci sia un file remove_from_cart.php per gestire la rimozione dal carrello
        data: { gameId: gameId ,action:'delete_game_from_cart'},
        type: 'POST',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                window.location.reload();
            } else  {
                Swal.fire('Hai raggiunto il limite!', response.message, 'error');
            }
        },
        error: function () {
            Swal.fire('Errore', 'Si è verificato un errore durante la richiesta.', 'error');
        }
    });
}

$(document).ready(function () {
    // Esempio di chiamata alla funzione
    $.ajax({
        url: '../controllers/cart.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            const data = JSON.parse(response.data);
            updateCart(data);
            $('.games-list').on('click', '.quantity-increase', increaseQuantity);
            $('.games-list').on('click', '.quantity-decrease', decreaseQuantity);
            $('.games-list').on('click', '.remove-button', removeGame);
        },
        error: function () {
            window.location.href = '../.';
        }
    });
});
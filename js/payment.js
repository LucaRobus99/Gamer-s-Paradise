$(document).ready(function () {
    let selectedRadioButtonId = '';
    const backButton = $('#backButton');
    const nextButton = $('#nextButton');
    const stepContainers = $('.step-container');
    const steps = $('.step');
    const progressLines = $('.progress-line');
    const homeButton = $('#homeButton'); // Add the ID for the home button



    let currentStep = 0;

    updateStep(currentStep);

    backButton.on('click', function () {
        if (currentStep > 0) {
            currentStep--;
            updateStep(currentStep);
        }
    });

    nextButton.on('click', function () {
        if (currentStep < stepContainers.length - 1) {
            currentStep++;
            updateStep(currentStep);
        }
    });

    homeButton.on('click', function () {
        // Redirect to the home page
        window.location.href = '../.';
    });

    function updateStep(stepIndex) {
        stepContainers.each(function (index, container) {
            $(container).css('display', index === stepIndex ? 'block' : 'none');
        });

        steps.each(function (index, step) {
            if (index === stepIndex) {
                $(step).addClass('active').removeClass('completed');
            } else if (index < stepIndex) {
                $(step).removeClass('active').addClass('completed');
            } else {
                $(step).removeClass('active completed');
            }
        });
        if (stepIndex === 2) {
            $(steps[2]).removeClass('active').addClass('completed');
        }
        progressLines.each(function (index, line) {
            if (index < stepIndex) {
                $(line).addClass('active');
            } else {
                $(line).removeClass('active');
            }
        });

        // Show/hide buttons and handle home button on step 3
        if (currentStep === 0) {
            backButton.prop('disabled', true);
            nextButton.prop('disabled', true);
            homeButton.hide();
        } else if (currentStep === stepContainers.length - 1) {
            backButton.hide()
            nextButton.hide()
            homeButton.show();
        } else {
            backButton.prop('disabled', false);
            nextButton.prop('disabled', false);
            homeButton.hide();
        }

        if (stepIndex === 0) {
            // AJAX GET request for Step 1
            $.ajax({
                url: '../controllers/buy.php',
                type: 'GET',
                dataType: 'json',

                success: function (response) {
                    if(response.success){
                        renderCardDetailsArray(response.cards);
                        $('input[type="radio"]').on('change', function() {
                            if ($('input[type="radio"]:checked').length > 0) {
                                selectedRadioButtonId = $(this).val(); // Assegna l'ID del radiobutton selezionato
                                nextButton.prop('disabled', false);
                            } else {
                                nextButton.prop('disabled', true);
                            }
                        });
                    }else {
                        window.location.href='cart_view.php'
                    }


                },
                error: function (error) {
                    console.error('Error fetching step content:', error);
                }
            });
        } else {

            $.ajax({
                url: '../controllers/buy.php',
                type: 'POST',
                data: {
                    step: stepIndex + 1,
                    radioButtonId: selectedRadioButtonId // Utilizza l'ID del radiobutton selezionato
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success){
                        if(stepIndex===1){
                            generateSummary(response.videogames,response.user,response.cards);

                        }else if(stepIndex===2){
                            generateAcquiredKeysDetailsHtml(response.acquired_keys);
                            updateCartItemCount();
                            console.log(response.acquired_keys);
                        }
                    }else {
                        window.location.href='cart_view.php'
                    }


                    // Puoi aggiornare il tuo front-end con i dati ricevuti dalla risposta
                },
                error: function (error) {
                    console.error('Error fetching step content:', error);
                }
            });
        }
    }
});

function renderCardDetailsArray(cards) {
    var cardHtml = '';
    var title = '<h1>Seleziona una carta</h1>';
    $.each(cards, function(index, card) {
        
        cardHtml += '<div class="card-details">';
      
        cardHtml += '<p><strong>Nome:</strong> ' + card.card_holder_name + '</p>';
        cardHtml += '<p><strong>Cognome:</strong> ' + card.card_holder_lastname + '</p>';
        cardHtml += '<p><strong>Numero carta:</strong> ' + card.card_number + '</p>';
        cardHtml += '<p><strong>CVV:</strong> ' + card.cvv + '</p>';
        cardHtml += '<p><strong>Data di scadenza:</strong> ' + card.expiring_date + '</p>';
        cardHtml += '<input type="radio" name="selectedCard" value="' + card.id + '">'; // Radio button for card selection
        cardHtml += '</div>';
    });
    $('#stepContainer1').html(title+cardHtml);


}



function generateSummary(videogames, user, card) {
    var summaryHtml = '<h2>Riepilogo</h2>';
    summaryHtml += '<div class="user-info">';
    summaryHtml += '<h3>Informazioni utente</h3>';

    if (user && user.first_name && user.last_name && user.email) {
        summaryHtml += '<p>Nome e cognome: ' + user.first_name + ' ' + user.last_name + '</p>';
        summaryHtml += '<p>Email: ' + user.email + '</p>';
    } else {
        summaryHtml += '<p>Dati utente non disponibili</p>';
    }

    summaryHtml += '</div>';
    
    summaryHtml += '<div class="cart-info">';
    if (videogames && videogames.length > 0) {
        summaryHtml += '<h3>Giochi nel carrello</h3>';
        var total = 0;

        for (var i = 0; i < videogames.length; i++) {
            var game = videogames[i];
            summaryHtml += '<p>Titolo: ' + game.title + '</p>';
            summaryHtml += '<p>Piattaforma: ' + game.platform + '</p>';
            summaryHtml += '<p>Quantità: ' + game.quantity + '</p>';

            var gamePrice = parseFloat(game.price);
            var gameQuantity = parseInt(game.quantity);
            var gameTotal = gamePrice * gameQuantity;

            summaryHtml += '<p>Prezzo totale: $' + gameTotal + '</p>';

            total += gameTotal;
        }

        summaryHtml += '<p>Totale: $' + total + '</p>';
    } else {
        summaryHtml += '<p>Nessun gioco nel carrello</p>';
    }

    summaryHtml += '</div>';

    summaryHtml += '<div class="payment-info">';
    if (card && card.card_holder_name && card.card_number && card.expiring_date) {
        summaryHtml += '<h3>Informazioni di pagamento</h3>';
        summaryHtml += '<p>Proprietario card: ' + card.card_holder_name + ' ' + card.card_holder_lastname + '</p>';
        summaryHtml += '<p>Numero della carta: **** **** **** ' + card.card_number.slice(-4) + '</p>';
        summaryHtml += '<p>Data di scadenza: ' + card.expiring_date + '</p>';
    } else {
        summaryHtml += '<p>Informazioni di pagamento non disponibili</p>';
    }
    summaryHtml += '</div>';

    $('#stepContainer2').html(summaryHtml); // Utilizza jQuery per
}


function generateAcquiredKeysDetailsHtml(acquiredKeys) {
    var acquiredKeysHtml = '<h2>Chiavi acquisite</h2>';
    acquiredKeysHtml += '<ul>';

    for (var i = 0; i < acquiredKeys.length; i++) {
        var key = acquiredKeys[i];
        acquiredKeysHtml += '<li>';
        acquiredKeysHtml += '<strong>Titolo del gioco:</strong> ' + key.game_title + '<br>';
        acquiredKeysHtml += '<strong>Codice chiave:</strong> ' + key.key_code + '<br>';
        acquiredKeysHtml += '</li>';
    }

    acquiredKeysHtml += '</ul>';
    $('#stepContainer3').html(acquiredKeysHtml);
}
function updateCartItemCount() {

// Update the element with the new total
    $('#cart-items-count').text(0);
}
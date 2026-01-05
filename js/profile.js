function setProfile(name,surname,email,password){
    const requestData = {
        Name: name,
        Surname: surname,
        Email: email,
        Password:password,
        action: 'update_profile'
    };

    $.ajax({
        url: '../controllers/update_profile.php', // Sostituisci con il percorso del tuo endpoint di aggiunta carta di credito
        type: 'POST', // Metodo HTTP POST per inviare i dati al server
        data: requestData, // Dati da inviare
        dataType: 'json', // Tipo di dati attesi nella risposta

        success: function (response) {
            if (response.success) {


                // Puoi fare qualcosa con i dati restituiti, ad esempio, mostrare un messaggio di successo
                Swal.fire({
                    icon: 'success',
                    title: 'Successo',
                    text: response.message,
                });


            } else {
                // Gestisci il caso in cui la risposta indica un errore
                Swal.fire({
                    icon: 'error',
                    title: 'Errore',
                    text: response.message,
                });
            }
        },
        error: function (error) {
            // Gestisci l'errore della richiesta AJAX
            console.error('Errore durante la richiesta AJAX:', error);
        }
    });
}



function aggiungiAcquisto(data, nomeGioco, piattaforma,key) {
    if (data && nomeGioco && piattaforma) {
        const purchaseItem = `
        <tr>
            <td>${data}</td>
            <td>${nomeGioco}</td>
            <td>${piattaforma}</td>
            <td>${key}</td>
        </tr>`;
        $('#acquisti tbody').append(purchaseItem);
    }
}

function getAcquisti(){
    $.ajax({
        url: '../controllers/update_profile.php', // Replace with the path to your PHP file
        type: 'GET',
        dataType: 'json',

        success: function(response) {
            if (response.success) {
                const purchasesWithDetails = response.purchasesWithDetails;

                // Now you can use the 'purchasesWithDetails' array in your JavaScript code


                // Loop through the purchase details and do something with them
                purchasesWithDetails.forEach(function(purchase) {
                    const date = purchase.date;
                    const title = purchase.title;
                    const platform = purchase.platform;
                    const key_value=purchase.key_value;

                    aggiungiAcquisto(date,title,platform,key_value);

                    // You can append the data to your HTML elements or use it as needed
                });
            } else {
                console.error('Error:', response.message);
            }
        },
        error: function(error) {
            console.error('Error:', error);
        }
    });


}
// Funzione per aggiungere una carta di credito
function aggiungiCartaDiCredito(idCarta,numeroCarta, scadenzaMese, scadenzaAnno) {
    if (numeroCarta && scadenzaMese && scadenzaAnno) {
        const listItem = `
            <li class="list-group-item">
                Numero: ${numeroCarta}<br>
                Scadenza: ${scadenzaMese}/${scadenzaAnno}
                <button id=${idCarta} class="btn btn-danger btn-sm float-right" onclick="eliminaCartaDiCredito(this)">Elimina</button>
            </li>`;
        $('#carteDiCredito').append(listItem);
    }
}

function addCardRequest(numCard, expireMonth, expireYear, HolderName, HolderSurname, CVV) {
    // Dati da inviare al server
    const requestData = {
        numCard: numCard,
        expireMonth: expireMonth,
        expireYear: expireYear,
        HolderName: HolderName,
        HolderSurname: HolderSurname,
        CVV: CVV,
        action: 'add_card' // Assumi che 'add_card' sia l'azione per aggiungere una carta di credito
    };

    $.ajax({
        url: '../controllers/update_profile.php',
        type: 'POST', // Metodo HTTP POST per inviare i dati al server
        data: requestData, // Dati da inviare
        dataType: 'json', // Tipo di dati attesi nella risposta

        success: function (response) {
            if (response.success) {
                // Dati restituiti con successo
                var dataCard = response.dataCard;
                var message = response.message;
                var numCard = dataCard.numCard; // Valore restituito dalla funzione 'maskCardNumber'
                var expireMonth = dataCard.expireMonth; // Valore da $_POST['expireMonth']
                var expireYear = dataCard.expireYear; // Valore da $_POST['expireYear']
                var idCard = dataCard.idCard; // Valore $id
                // Puoi fare qualcosa con i dati restituiti, ad esempio, mostrare un messaggio di successo
                Swal.fire({
                    icon: 'success',
                    title: 'Successo',
                    text: message,
                });
                aggiungiCartaDiCredito(idCard,numCard,expireMonth,expireYear);

            } else {
                // Gestisci il caso in cui la risposta indica un errore
                Swal.fire({
                    icon: 'error',
                    title: 'Errore',
                    text: response.message,
                });
            }
        },
        error: function (error) {
            // Gestisci l'errore della richiesta AJAX
            console.error('Errore durante la richiesta AJAX:', error);
        }
    });
}


function getCards(){
    $.ajax({
        url: '../controllers/update_profile.php',
        data:{action:'get_cards'},
        type: 'GET',
        dataType: 'json',

        success: function (response) {
            if (response.success) {

                const cards = response.cards; // Assuming cards is an array of card objects

                // Iterate through the array of cards
                cards.forEach(function(card){
                    const cardid = card.id;
                    const cardNumber = card.card_number;
                    const expiringDate = card.expiring_date;
                    const [expiringMonth, expiringYear] = expiringDate.split('/');
                    aggiungiCartaDiCredito(cardid,cardNumber, expiringMonth, expiringYear);
                    $('#formCartaDiCredito')[0].reset();
                }); // Missing closing parenthesis for forEach

            } // Missing closing brace for if

        },
        error: function (error) {
            console.error('Error fetching step content:', error);
        }
    });
}


// Funzione per eliminare una carta di credito
function eliminaCartaDiCredito(button) {
    const idCarta = $(button).attr('id');
    $.ajax({
        url: '../controllers/update_profile.php',

        data:{cardId:idCarta,
            action:'delete_card'
        },
        type: 'POST',
        dataType: 'json',

        success: function (response) {

            if(response.success){
                $(button).parent().remove();
            }else {
                Swal.fire({
                    icon: 'error',
                    title: 'Errore',
                    text: 'Impossibile cancellare la carta di credito. Riprova più tardi.',
                });
            }
        },
        error: function (error) {
            console.error('Error fetching step content:', error);
        }
    });

}


// Gestisci la presentazione iniziale
$(document).ready(function() {
    getCards();
    getAcquisti();


});

// Gestisci l'invio del form per aggiungere una carta di credito
$('#formCartaDiCredito').submit(function(e) {
    e.preventDefault();
    const numeroCarta = $('#numeroCarta').val();
    const scadenzaMese = $('#scadenzaMese').val();
    const scadenzaAnno = $('#scadenzaAnno').val();
    const nomeProp=$('#nomeProprietario').val();
    const cognProp=$('#cognomeProprietario').val();
    const cvv=    $('#cvv').val();
    addCardRequest(numeroCarta,scadenzaMese,scadenzaAnno,nomeProp,cognProp,cvv);

});

// Gestisci l'invio del form per modificare le informazioni personali
$('#formInformazioniPersonali').submit(function(e) {
    e.preventDefault();
    const nome = $('#nome').val();
    const cognome = $('#cognome').val();
    const email = $('#email').val();
    const password = $('#password').val();

    setProfile(nome,cognome,email,password);
});
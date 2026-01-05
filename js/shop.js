function validatePlatformInput(platform) {
    var platformRegex = /^[A-Za-z0-9\s]+$/; // Regex che accetta lettere, numeri e spazi
    return platformRegex.test(platform);
}

function displayError(message) {
    var errorMessage;
    switch (message){
        case 'Piattaforma non trovata':
            errorMessage = '<h1>Errore 404: Non trovato</h1><br><br>' +
                '<p>La piattaforma richiesta non è stata trovata o non ' +
                'abbiamo ancora dei videogiochi per questa piattaforma.</p>';
            break;
        case 'Errore dati mancanti':
            errorMessage = '<h1>Errore: Dati Mancanti</h1><br><br>' +
                '<p>I dati necessari per questa operazione non sono stati forniti. ' +
                'Per favore, assicurati di inserire tutti i dati richiesti.</p>';
            break;
        case 'Videogames non trovati per questi filtri':
            errorMessage = '<h1>Nessun gioco trovato</h1><br><br>' +
                '<p>Non abbiamo trovato quello che cercavi, ci dispiace tanto :(</p>';
            break;
    }
    var mainContent = document.getElementById('gamesContainer');
    mainContent.innerHTML = errorMessage;

}

function createGameCard(game) {
    var card = document.createElement('div');
    card.classList.add('card');

    card.innerHTML = `
        <a href="../view/gameProfile_view.php?id=${game.id}" class="card-link">
            <img src="../img/${game.cover}" alt="Game Cover">
            <h5 class="card-title">${game.title}</h5>
            <p class="card-text">Prezzo: ${game.price}&euro;</p>
        </a>
    `;
    return card;
}

function displayGames(gamesList) {
    var gamesContainer = document.getElementById('gamesContainer');
    while (gamesContainer.firstChild) {
        gamesContainer.removeChild(gamesContainer.firstChild);
    }
    for (var j = 0; j < gamesList.length; j++) {
        var game = gamesList[j];
        var card = createGameCard(game);
        gamesContainer.appendChild(card);
    }
}

function setFilter(platform,genre) {
  
    
      
    var form = $('<form>');
    $('#filters').append(form);
    $.each(platform, function (index, elemento) {
        var checkbox = $('<input type="checkbox">');
        checkbox.attr('id', 'Console' + index);
        checkbox.attr('name', elemento.platform);
        checkbox.attr('checked', true); // Puoi impostare il valore iniziale qui se necessario
        checkbox.addClass('required-checkbox');

        var label = $('<label>').attr('for', 'Console' + index).text(elemento.platform);

        $('form').append(label);
        $('form').append(checkbox);
      
        
    });
    
    var wrapper = $('<div>');
    wrapper.addClass('select-wrapper');
    wrapper.attr('id', 'wrapper0');
    $('form').append(wrapper);

    var select = $('<select>');
    select.attr('id', 'menuGen');
    $('#wrapper0').append(select);
    var defaultOption = $('<option>').attr('value', '').attr('disabled', true).attr('selected', true).attr('hidden', true).text('Genere:');
    select.append(defaultOption);
    $.each(genre, function (index,elemento) {
        var option = $('<option>').val(elemento.genre).text(elemento.genre);
       
        $('#menuGen').append(option);
       
      
        
    });
    var wrapper = $('<div>');
    wrapper.addClass('select-wrapper');
    wrapper.attr('id', 'wrapper1');
    $('form').append(wrapper);
    var select = $('<select>');
    select.attr('id', 'menuSort');
    wrapper.append(select);
    var defaultOption = $('<option>').attr('value', '').attr('disabled', true).attr('selected', true).attr('hidden', true).text('Ordina Per:');
    select.append(defaultOption);
    var opzioni = [
        { value: 'Alfabetico', text: 'Alfabetico' },
        { value: 'Crescente', text: 'Prezzo Cresc.' },
        { value: 'Decrescente', text: 'Prezzo Decresc.' }
    ]; 
    
    $.each(opzioni, function (index, opzione) {
        var option = $('<option>').attr('value', opzione.value).text(opzione.text);
        select.append(option);
    });

    // Aggiungi il select al DOM, ad esempio a un elemento con un ID specifico
    
            
    
     
                 //Controllo checkbox che almeno una sia selezionata
                $('#Console0').click(function () {
                    if (!$('#Console0').prop('checked') && !$('#Console1').prop('checked')) {
                        $('#Console0').prop('checked', true); // Mantenere selezionata la checkbox 4
                    }
                });

                $('#Console0').click(function () {
                    if (!$('#Console1').prop('checked') && !$('#Console0').prop('checked')) {
                        $('#Console1').prop('checked', true); // Mantenere selezionata la checkbox 5
                    }
                });

                $(window).scroll(function () {
                    var scroll = $(window).scrollTop();
                    if (scroll > 0) {
                        $('#filters').addClass('scrolled');
                    } else {
                        $('#filters').removeClass('scrolled');
                    }
                });

          
                $(".select-wrapper").each(function() {
                    const selectWrapper = $(this);
                    const select = selectWrapper.find("select");
            
                    select.change(function() {
                        const selectedOption = $(this).find(":selected").val();
            
                        // Rimuovi l'icona se è già presente
                        const existingCloseIcon = selectWrapper.find(".close-icon");
                        if (existingCloseIcon.length > 0) {
                            existingCloseIcon.remove();
                        }
            
                        if (selectedOption) {
                            const closeIcon = $("<div class='close-icon'>&#10006;</div>");
                            selectWrapper.append(closeIcon);
            
                            // Gestisci il clic sull'icona "x"
                            closeIcon.click(function() {
                                select.prop("selectedIndex", 0); // Ripristina la select allo stato iniziale
                                closeIcon.remove(); // Rimuovi l'icona "x"
                                updateGamesList();
                            });
                        }
                    });
                });

            }

function updateGamesList() {
    const selectedPlarforms = [];
    $('input[type="checkbox"]:checked').each(function () {
        selectedPlarforms.push($(this).attr('name'));
    });

    const selectedSort = $('#menuSort').val();
    const selectedGen = $('#menuGen').val();



    $.ajax({
        url: '../controllers/shop.php',
        method: 'GET',
        dataType: 'json',
        data: {
            action: 'filtring',
            platforms: selectedPlarforms,
            order: selectedSort,
            genre: selectedGen
        },
        success: function (response) {
            console.log('Ajax response:', response);

            if (response.success) {
                displayGames(response.games);
            } else  {
                displayError(response.message);
            }
        },
        error: function (error) {
            console.log('Errore durante la richiesta AJAX.');
        }
    });
}






$(document).ready(function () {

    $('#filters').on('change','input[type="checkbox"], #menuSort, #menuGen,#wrapper', updateGamesList);
    // Ottieni la piattaforma dai parametri dell'URL
    const Url_platform = new URLSearchParams(window.location.search);
    const platform = Url_platform.get('platform');

    // Esegui l'azione iniziale al caricamento della pagina
    $.ajax({
        url: '../controllers/shop.php',
        method: 'GET',
        dataType: 'json',
        data: { action:'showGame',
            platform: platform },
        success: function (response) {


            if (response.success) {
                setFilter(response.platform,response.genre);
                displayGames(response.games);
            } else  {
                displayError(response.message);
            }
        },
        error: function (error) {
            console.log('Errore durante la richiesta AJAX.');
        }
    });
   


});

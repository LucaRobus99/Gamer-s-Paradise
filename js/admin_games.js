

function populateGameslist(games){
    var gameList = $('#gameList');

    // Svuota la lista dei giochi esistente
    gameList.empty();

    // Itera attraverso l'array dei giochi e crea un elemento <li> per ciascun gioco
    $.each(games, function(index, game) {
        var listItem = $('<li>').addClass('list-group-item').attr('id', 'game_' + game.id); // Aggiungi l'attributo id con il valore del game ID
        var gameContent = `

            <h4>${game.title}</h4>
            <p><strong>Genere:</strong> ${game.genre}</p>
            <p><strong>Platform:</strong> ${game.platform}</p>
            <p><strong>Prezzo:</strong> &euro;${game.price}</p>
            <button class="btn btn-danger" onclick="eliminaVideogioco(${game.id})">Elimina</button>
            <button class="btn btn-primary" onclick="Managekeys(${game.id})">Gestisci Key</button>
        `;

        // Aggiungi il contenuto HTML all'elemento <li>
        listItem.html(gameContent);

        // Aggiungi l'elemento <li> all'elemento #gameList
        gameList.append(listItem);
    });

}



function getGames(){
    $.ajax({
        url: '../controllers/admin_games.php', // Replace with the correct path
        type: 'GET',
        dataType:'json',
        data: { action: 'get_games' }, // Send a request to fetch platforms
        success: function (response) {
            populateGameslist(response.games);

        },
        error: function (error) {
            console.error('Error fetching games: ' + error);
        }
    });

}


function populateGenre(GenreOptions){
    $.each(GenreOptions, function(index, genre) {
        $('#gameGenre').append($('<option>', {
            value: genre.genre, // Use the platform name as the value
            text: genre.genre // Display the platform name as the option text
        }));

    });
}
function getGenre(){
    $.ajax({
        url: '../controllers/admin_games.php', // Replace with the correct path
        type: 'GET',
        dataType:'json',
        data: { action: 'get_genre' }, // Send a request to fetch platforms
        success: function (response) {
            var genres = response.genres;
            populateGenre(genres);


        },
        error: function (error) {
            console.error('Error fetching genre: ' + error);
        }
    });

}

function populatePlatform(platformOptions){
    $.each(platformOptions, function(index, platform) {
        $('#gamePlatform').append($('<option>', {
            value: platform.platform, // Use the platform name as the value
            text: platform.platform // Display the platform name as the option text
        }));

    });
}

function getPlatform(){
    $.ajax({
        url: '../controllers/admin_games.php', // Replace with the correct path
        type: 'GET',
        dataType:'json',
        data: { action: 'get_platforms' }, // Send a request to fetch platforms
        success: function (response) {
            var platforms = response.platforms;
            populatePlatform(platforms);

        },
        error: function (error) {
            console.error('Error fetching platforms: ' + error);
        }
    });
}




$(document).ready(function () {
    // Function to handle game genre input
    // Function to handle game genre input
    getPlatform();
    getGenre();
    getGames();





    // Function to handle game search
    $('#gameSearch').on('input', function () {
        var searchTerm = $(this).val().toLowerCase();
        $('#gameList li').each(function () {
            var gameTitle = $(this).text().toLowerCase();
            if (gameTitle.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Function to add a new video game
    $('#addGameForm').submit(function (event) {
        event.preventDefault();
        var title = $('#gameTitle').val();
        var genre = $('#gameGenre').val();
        var platform = $('#gamePlatform').val();
        var  description= $('#gameDescription').val(); // Get description
        var price = $('#gamePrice').val(); // Get price
        var image = $('#gameImage')[0].files[0];
        addGame(title,genre,platform,description,price,image);
    });

    // You should have an additional function here to load the list of games from the database when the page loads.
});

function addGame(title, genre, platform, description, price, image) {
    var formData = new FormData();
    formData.append('gameTitle', title);
    formData.append('gameGenre', genre);
    formData.append('gamePlatform', platform);
    formData.append('gameDescription', description);
    formData.append('gamePrice', price);
    formData.append('gameImage', image);
    formData.append('action','add_game');

    $.ajax({
        url: '../controllers/admin_games.php', // Replace with the correct path
        type: 'POST',
        dataType: 'json',
        data: formData, // Pass formData as the data
        processData: false, // Ensure that jQuery doesn't process the data
        contentType: false, // Set content type to false for file uploads
        success: function (response) {
            addGameToList(response.lastgame)
            Swal.fire({
                icon: 'success',
                title: 'Successo',
                text: response.message,
            });
        },
        error: function (error) {
            console.error('Error adding game: ' + error);
        }
    });
}



function Managekeys(gameId) {
    // Ora puoi utilizzare l'ID del gioco (gameId) come necessario
    // Naviga alla nuova pagina
    window.location.href = "admin_key_view.php?id=" + gameId;
    // Esegui altre operazioni in base all'ID del gioco
}

function eliminaVideogioco(gameId) {
    $.ajax({
        url: '../controllers/admin_games.php',
        data: {
            id_game: gameId,
            action: 'delete_game'
        },
        type: 'POST',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                // Utilizza gameId per trovare l'elemento corrispondente e rimuoverlo
                $('#game_' + gameId).remove();
                Swal.fire({
                    icon: 'success',
                    title: 'Successo',
                    text: 'Videogioco eliminato con successo.',
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Errore',
                    text: 'Impossibile cancellare il videogioco. Riprova più tardi.',
                });
            }
        },
        error: function (error) {
            console.error('Error fetching step content:', error);
        }
    });
}
function addGameToList(game) {
    var listItem = $('<li>').addClass('list-group-item').attr('id', 'game_' + game.id); // Aggiungi l'attributo id con il valore del game ID
    var gameContent = `
        <h4>${game.title}</h4>
        <p><strong>Genere:</strong> ${game.genre}</p>
        <p><strong>Platform:</strong> ${game.platform}</p>
        <p><strong>Prezzo:</strong> &euro;${game.price}</p>
        <button class="btn btn-danger" onclick="eliminaVideogioco(${game.id})">Elimina</button>
        <button class="btn btn-primary" onclick="Managekeys(${game.id})">Gestisci Key</button>
    `;

    listItem.html(gameContent);
    $('#gameList').append(listItem); // Assuming #gameList is the ID of your game list container
}
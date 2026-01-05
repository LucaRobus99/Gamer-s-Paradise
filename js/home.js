function getLastgamesadded(){
    $.ajax({
        type: "GET",
        url: "controllers/home.php",
        dataType: "json",
        data:{action:'get_lastgames_added'},
        success: function (response) {
            $("#recentPurchases .col-md-4").remove();

            // Loop attraverso i giochi più recenti e crea le card dinamicamente
            $.each(response.latestGames, function (index, game) {
                var cardHtml = `
                <div class="col-md-4 mb-4" >
                    <div class="card" onclick="goToPageofGame(${game.id})">
                        <div class="card-body" >
                            <h3 class="card-title">${game.title}</h3>
                            <p class="card-text"> ${game.platform}</p>
                            <p class="card-text"> &euro;${game.price}</p>                            
                        </div>
                        <img src="img/${game.cover}" class="card-img-top" alt="${game.title}">
                    </div>
                </div>
                `;

                // Aggiungi la card appena creata alla sezione "Ultime aggiunte"
                $("#recentPurchases").append(cardHtml);
            });
        },
        error: function (error) {
            console.log(error);
        },
    });

}


function getTrendGames() {
    $.ajax({
        type: "GET",
        url: "controllers/home.php",
        dataType: "json",
        data:{action:'get_trend_games'},
        success: function (response) {
            // Rimuovi tutti gli elementi precedenti nello slider con dissolvenza in uscita
            $(".slider .game").fadeOut(300, function () {
                $(this).remove();
            });

            // Loop attraverso i giochi e aggiungi ciascun gioco allo slider con dissolvenza in entrata
            $.each(response.trendGames, function (index, game) {
                var gameHtml = `
                <div class="game" onclick="goToPageofGame(${game.id})">
                    <img src="img/${game.cover}" alt="${game.title}" class="img-fluid">
                    <h3>${game.title}</h3>
                    <p>Piattaforma: ${game.platform}</p>
                </div>
                `;
                $(".slider").slick("slickAdd", gameHtml).find(".game").fadeIn(300);
            });
        },
        error: function (error) {
            console.log(error);
        },
    });
}


$(document).ready(function () {
    // Inizializza lo slider con le opzioni desiderate
    $(".slider").slick({
        autoplay: true,
        autoplaySpeed: 3000, // Imposta la velocità di scorrimento (in millisecondi)
        arrows: true,
        dots: true,
    });

    getLastgamesadded();
    getTrendGames();

});

function goToPageofGame(gameId){
    window.location.href='view/gameProfile_view.php?id='+gameId;
}
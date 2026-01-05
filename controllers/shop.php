<?php
/*
 *Questo codice gestisce la visualizzazione dei giochi in base alla piattaforma, alle console e ai filtri specificati.
 *
*/


// Includi i modelli necessari
require_once '../model/Game.php';
require_once '../model/Genre.php';
require_once '../model/Platform.php';

// Funzione per ottenere i giochi in base alla piattaforma
function getGamesByPlatform($platform) {
    $g = new Game();
    return $g->getGamesByPlatform($platform);
}

// Funzione per ottenere le console
function getConsole(){
    $pf = new Platform();
    if (isset($_GET['platform'])) {
        $platform = $_GET['platform'];
        return $pf->getAllConsoleByPlatform($platform);
    }
}

// Funzione principale per gestire la richiesta dei giochi
function handleGamesRequest() {
    $gn = new Genre();

    if (!empty($_GET['platform'])) {
        $platform = $_GET['platform'];
        $games_by_platform = getGamesByPlatform($platform);

        if (empty($games_by_platform)) {
            $response = array(
                'success' => false,
                'message' => 'Piattaforma non trovata'
            );
        } else {
            $response = array(
                'success' => true,
                'message' => 'Dati ricevuti correttamente per la piattaforma: ',
                'games' => $games_by_platform,
                'platform' => getConsole(),
                'genre' => $gn->getAllGenre()
            );
        }
    } else {
        $response = array(
            'success' => false,
            'message' => 'Piattaforma non trovata'
        );
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Funzione per ottenere i giochi in base ai filtri
function getGamesByfilters_() {
    $g = new Game();
    $ris = $g->getGamesByFilters($_GET['platforms'], $_GET['order'], $_GET['genre']);

    if ($ris) {
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => true,
            'message' => 'Videogames trovati per questi filtri',
            'games' => $ris
        ));
    } else {
        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => false,
            'message' => 'Videogames non trovati per questi filtri'
        ));
    }
}

if (isset($_GET['action']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'];
    switch ($action) {
        case 'showGame':
            handleGamesRequest();
            break;
        case 'filtring':
            getGamesByfilters_();
            break;
    }
}
?>

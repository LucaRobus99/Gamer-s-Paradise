<?php
/**
 * Questo script PHP gestisce le richieste relative al recupero dei giochi di tendenza (ad esempio, i giochi più venduti)
 * e degli ultimi giochi aggiunti al sito. Le classi necessarie per gestire gli acquisti e i giochi sono incluse
 * e utilizzate per ottenere le informazioni necessarie.
 * Le richieste GET sono gestite in base all'azione specificata nella richiesta, inclusi i casi in cui l'azione non è valida.
 * La risposta JSON contiene i dati richiesti in base all'azione.
 */
// Includi le classi necessarie
require_once '../model/Purchase.php';
require_once '../model/Game.php';

// Inizia la sessione
session_start();

// Crea istanze delle classi Purchase e Game
$purchase = new Purchase();
$games = new Game();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];

        if ($action === 'get_trend_games') {
            // Ottieni i giochi di tendenza (ad esempio, i giochi più venduti)
            $gameDetails = array();
            foreach ($purchase->getMostSoldGames(3) as $item) {
                $game = $games->getGameById($item);
                $gameDetails[] = $game;
            }

            // Prepara la risposta JSON con successo e giochi di tendenza
            $response['success'] = true;
            $response['message'] = 'Giochi di tendenza';
            $response['trendGames'] = $gameDetails;
        } else if ($action === 'get_lastgames_added') {
            // Ottieni gli ultimi giochi aggiunti al sito
            $games_added = $games->getLatestGames(3);

            // Prepara la risposta JSON con successo e gli ultimi giochi aggiunti
            $response['success'] = true;
            $response['message'] = 'Ultimi giochi aggiunti';
            $response['latestGames'] = $games_added;
        } else {
            // Se l'azione non è valida, restituisci una risposta JSON di errore
            $response['success'] = false;
            $response['message'] = 'Azione non valida';
        }
    } else {
        // Se l'azione non è definita, restituisci una risposta JSON di errore
        $response['success'] = false;
        $response['message'] = 'Azione non valida';
    }
} else {
    // Se il metodo di richiesta non è valido, restituisci una risposta JSON di errore
    $response['success'] = false;
    $response['message'] = 'Azione non valida';
}

// Imposta l'header Content-Type su JSON e stampa la risposta JSON
header('Content-Type: application/json');
echo json_encode($response);
?>

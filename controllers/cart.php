<?php

/*x
 * Questo script PHP gestisce la richiesta GET per recuperare i giochi presenti nel carrello dell'utente e restituirli
 * come dati JSON. Le classi necessarie per gestire il carrello e i giochi sono incluse e utilizzate per ottenere le informazioni.
 * L'output della risposta è un oggetto JSON che contiene i giochi nel carrello se il carrello esiste, altrimenti restituisce un messaggio
 * di errore appropriato.
 */
// Includi le classi necessarie
require_once '../model/Cart.php';
require_once '../model/Game.php';

// Funzione per ottenere i giochi nel carrello in formato JSON
function getGamesInCartJSON($cart_items) {
    $game_model = new Game();
    $games_in_cart = array();

    foreach ($cart_items as $cart_item) {
        $game_id = $cart_item['game_id'];
        $game = $game_model->getGameById($game_id);
        if ($game) {
            $quantity = $cart_item['quantity'];
            $game['quantity'] = $quantity;
            $games_in_cart[] = $game;
        }
    }

    return json_encode($games_in_cart);
}

// Inizia la sessione
session_start();

// Gestione della richiesta GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['cart'])) {
        $json_response = array(
            'success' => true,
            'message' => 'Dati recuperati con successo',
            'data' => getGamesInCartJSON($_SESSION['cart'])
        );

        // Restituisci la risposta JSON
    } else {
        $json_response = array(
            'success' => false,
            'message' => 'Il carrello non è stato creato',
        );
    }
} else {
    // Gestisci la richiesta in modo diverso se non è di tipo GET (ad esempio, restituire un errore o una risposta appropriata).
    $json_response = array(
        'success' => false,
        'message' => 'Metodo di richiesta non supportato',
    );

    // Restituisci la risposta JSON
}

// Imposta l'header Content-Type su JSON
header('Content-Type: application/json');
echo json_encode($json_response);
?>

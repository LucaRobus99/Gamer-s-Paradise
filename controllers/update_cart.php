<?php
/**
 * Questo script PHP gestisce le operazioni relative al carrello degli acquisti dell'utente.
 * Include funzioni per aumentare, diminuire o rimuovere un gioco dal carrello.
 * Verifica la disponibilità delle chiavi e assicura che l'utente sia autenticato e che la sessione sia valida.
 * Restituisce risposte JSON con successo o messaggi di errore in base all'esito delle operazioni.
 */
require_once '../model/Cart.php';
require_once '../model/Key.php';

// Inizia la sessione
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $k = new Key();
    $c = new Cart();
    $response = array();

    // Verifica se la sessione contiene il carrello e l'utente è autenticato
    if (isset($_SESSION['cart']) && isset($_SESSION['user'])) {
        if (isset($_POST['gameId']) && isset($_POST['action'])) {
            // Verifica se $_POST['gameId'] non è vuoto
            if (!empty($_POST['gameId'])) {
                $action = $_POST['action'];
                $availableKeys = count($k->getUnacquiredKeysForGame($_POST['gameId']));
                $quantity = 0;
                $exist = false;

                // Verifica se il gioco è già nel carrello
                foreach ($_SESSION['cart'] as $item) {
                    if ($item['game_id'] == $_POST['gameId']) {
                        $quantity = $item['quantity'];
                        $exist = true;
                        break;
                    }
                }

                // Se ci sono chiavi disponibili e il gioco è nel carrello
                if ($availableKeys > 0 && $exist) {
                    if ($action === 'increase') {
                        // Aumenta la quantità del gioco nel carrello
                        $newQuantity = $quantity + 1;

                        // Verifica se la nuova quantità è minore o uguale alle chiavi disponibili
                        if ($newQuantity <= $availableKeys) {
                            $c->setQuantity($_POST['gameId'], $_SESSION['user']['id'], $newQuantity);
                            $_SESSION['cart'] = $c->getCartItems($_SESSION['user']['id']);
                            $response['success'] = true;
                            $response['message'] = 'Quantità aggiornata con successo';
                        } else {
                            $response['success'] = false;
                            $response['message'] = 'Quantità nel carrello superiore alle chiavi disponibili';
                        }
                    } else if ($action === 'decrease') {
                        // Diminuisci la quantità del gioco nel carrello
                        $newQuantity = $quantity - 1;

                        // Verifica se la nuova quantità è maggiore di zero
                        if ($newQuantity > 0) {
                            $c->setQuantity($_POST['gameId'], $_SESSION['user']['id'], $newQuantity);
                            $_SESSION['cart'] = $c->getCartItems($_SESSION['user']['id']);
                            $response['success'] = true;
                            $response['message'] = 'Quantità aggiornata con successo';
                        } else {
                            $response['success'] = false;
                            $response['message'] = "Non puoi più diminuire la quantità di questo prodotto";
                        }
                    } else if ($action === 'delete_game_from_cart') {
                        // Rimuovi il gioco dal carrello
                        $c->removeGameFromCart($_POST['gameId'], $_SESSION['user']['id']);
                        $_SESSION['cart'] = $c->getCartItems($_SESSION['user']['id']);
                        $response['success'] = true;
                        $response['message'] = 'Gioco rimosso dal carrello con successo';
                    } else {
                        $response['success'] = false;
                        $response['message'] = "Azione non consentita";
                    }
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Chiavi non disponibili o gioco non esistente nel tuo carrello';
                }
            } else {
                $response['success'] = false;
                $response['message'] = 'gameId non valido';
            }
        } else {
            $response['success'] = false;
            $response['message'] = 'Parametri mancanti (gameId o action)';
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Sessione non valida (cart o user non definiti)';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Questa pagina richiede una richiesta POST';
}

header('Content-Type: application/json');
echo json_encode($response);
?>

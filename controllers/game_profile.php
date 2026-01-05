<?php


/*
 * Questo script PHP gestisce le richieste relative all'aggiunta di giochi al carrello dell'utente.
 * Le classi necessarie per gestire i giochi, le chiavi e il carrello sono incluse e utilizzate per ottenere
 * informazioni sui giochi, gestire il carrello e verificare l'autenticazione dell'utente.
 * Le richieste GET e POST sono gestite in base al caso, incluso il controllo della disponibilità delle chiavi e
 * la gestione degli errori.
 */

// Includi le classi e le funzioni necessarie
require_once '../model/Game.php';
require_once '../model/Key.php';
require_once '../model/Cart.php';
require_once '../util/alert.php';

// Avvia la sessione, se non è già stata avviata
if (!isset($_SESSION)) {
    session_start();
}

// Crea istanze delle classi Game, Key e Cart
$game = new Game();
$key = new Key();
$cart = new Cart();

// Inizia la gestione delle richieste (GET o POST)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Gestione delle richieste GET
    if (isset($_GET['gameId'])) {
        // Se il parametro 'gameId' è definito nella richiesta GET
        $game_id = $_GET['gameId'];

        // Ottieni le informazioni del gioco con l'ID specificato
        $gameInfo = $game->getGameById($game_id);

        if ($gameInfo) {
            // Se il gioco esiste, controlla il numero di chiavi non ancora acquisite
            $num_of_key = count($key->getUnacquiredKeysForGame($game_id));
            $gameInfo['quantity'] = $num_of_key;

            // Prepara la risposta JSON con successo e informazioni sul gioco
            $response['success'] = true;
            $response['message'] = 'Gioco disponibile';
            $response['gameInfo'] = $gameInfo;
        } else {
            // Se il gioco non esiste, restituisci una risposta JSON di errore
            $response['success'] = false;
            $response['message'] = 'Gioco non esiste ';
        }
    } else {
        // Se il parametro 'gameId' non è definito nella richiesta GET, restituisci una risposta JSON di errore
        $response['success'] = false;
        $response['message'] = 'Dati non validi';
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gestione delle richieste POST
    if (isset($_SESSION['user'])) {
        // Se l'utente è autenticato
        if ($_SESSION['user']['role'] != 1) {
            // Se l'utente non è un amministratore
            if (isset($_POST['gameId'])) {
                // Se il parametro 'gameId' è definito nella richiesta POST
                $game_id = $_POST['gameId'];
                $gameInfo = $game->getGameById($game_id);

                if ($gameInfo) {
                    // Se il gioco esiste
                    $unacquaintedKeys = $key->getUnacquiredKeysForGame($game_id);
                    $cartItemCount = $cart->getQuantity($game_id, $_SESSION['user']['id']);
                    $availableKeys = count($unacquaintedKeys);

                    if ($cartItemCount < $availableKeys) {
                        // Se ci sono chiavi disponibili nel carrello
                        if ($cart->gameExistsInCart($game_id, $_SESSION['user']['id'])) {
                            // Se il gioco è già nel carrello, aumenta la quantità
                            $cart->setQuantity($game_id, $_SESSION['user']['id'], $cartItemCount + 1);
                        } else {
                            // Altrimenti, aggiungi il gioco al carrello
                            $cart->addToCart($game_id, $_SESSION['user']['id']);
                        }

                        // Prepara la risposta JSON con successo e informazioni sul carrello
                        $response['success'] = true;
                        $response['message'] = 'Aggiunto al carrello';
                        $response['cartItemCount'] = $cartItemCount + 1;
                        $_SESSION['cart'] = $cart->getCartItems($_SESSION['user']['id']);
                    } else {
                        // Se il prodotto è esaurito, restituisci una risposta JSON di errore
                        $response['success'] = false;
                        $response['message'] = 'Prodotto esaurito';
                    }
                } else {
                    // Se il gioco non esiste, restituisci una risposta JSON di errore
                    $response['success'] = false;
                    $response['message'] = 'Gioco non esiste ';
                }
            } else {
                // Se il parametro 'gameId' non è definito nella richiesta POST, restituisci una risposta JSON di errore
                $response['success'] = false;
                $response['message'] = 'Dati non validi';
            }
        } else {
            // Se l'utente è un amministratore, restituisci una risposta JSON di errore
            $response['success'] = false;
            $response['message'] = 'Gli amministratori non possono acquistare dal sito';
        }
    } else {
        // Se l'utente non è autenticato, reindirizza verso la pagina di login e invia un messaggio di avviso
        $response['success'] = false;
        $response['message'] = 'Utente non autenticato, reindirizzamento verso la pagina di login';
        sendAlert('danger', 'Devi autenticarti prima di aggiungere qualcosa al tuo carrello!');
    }
} else {
    // Se il metodo di richiesta non è consentito, restituisci una risposta JSON di errore
    $response['success'] = false;
    $response['message'] = 'Azione non consentita';
}

// Imposta l'header Content-Type su JSON e stampa la risposta JSON
header('Content-Type: application/json');
echo json_encode($response);
?>

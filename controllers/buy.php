<?php
/**
 * Questo script PHP gestisce le operazioni relative al carrello dell'utente, inclusa la visualizzazione delle carte,
 * l'acquisizione di giochi e chiavi, nonché l'elaborazione delle transazioni di acquisto (simulate).
 * Le richieste GET e POST sono gestite in base allo step del processo di acquisto.
 */
require_once '../model/Card.php';
require_once '../model/Cart.php';
require_once '../model/Key.php';
require_once '../model/Purchase.php';
require_once '../model/Game.php';

require_once '../util/encrypt_decrypt.php';
session_start();

$card = new Card();
$purchase = new Purchase();
$game = new Game();
$key = new Key();
$cart = new Cart();


// Check if the user session is set (user is logged in)
if (isset($_SESSION['user'])) {
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        handleGetRequest();
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($_POST['step'] == '2') {
            handleStep2PostRequest();
        } elseif ($_POST['step'] == '3') {
            handleStep3PostRequest();
        }
    }
} else {
    echo json_encode(array('success'=>false,'message' => 'Utente non autenticato.'));
}

function handleGetRequest() {
    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
        $card_array = $_SESSION['cards'];
        foreach ($card_array as &$card) {
            $decrypted_cvv = decryptText($card['cvv']);
            $card['cvv'] = maskValue($decrypted_cvv);

            $card_number = $card['card_number'];
            $masked_card_number = maskCardNumber($card_number);
            $card['card_number'] = $masked_card_number;
        }
        echo json_encode(array('success' => true, 'message' => 'Get Carte utente', 'cards' => $card_array));
    } else {
        echo json_encode(array('success'=>false,'message' => 'Il carrello è vuoto.'));
    }
}


function handleStep2PostRequest() {
    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
        $games_in_cart = array();
        foreach ($_SESSION['cart'] as $cart_item) {
            $game_id = $cart_item['game_id'];
            $game = new Game(); // Instantiate a new Game object
            $game_data = $game->getGameById($game_id);
            if ($game_data) {
                $quantity = $cart_item['quantity'];
                $game_data['quantity'] = $quantity;
                $games_in_cart[] = $game_data;
            }
        }
        $cartaEsiste=false;
        foreach ($_SESSION['cards'] as $item) {
            if ($item['id']==$_POST['radioButtonId']) {
                $cartaEsiste = true;
                break; // Possiamo uscire dal ciclo una volta trovata una corrispondenza
            }
        }
        if(!$cartaEsiste){
            echo json_encode(array('success'=>false,'message' => 'la carta non esiste nel tuo portafoglio.'));
        }else{
            echo json_encode(array('success'=>true,
                'videogames' => $games_in_cart,
                'user' => $_SESSION['user'],
                'cards' => getCardById($_POST['radioButtonId'])
            ));
        }

    } else {
        echo json_encode(array('success'=>false,'message' => 'Il carrello è vuoto.'));
    }
}

function handleStep3PostRequest() {
    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
        $acquired_keys_details = simulatePurchase();

        echo json_encode(array('success' => true, 'acquired_keys' => $acquired_keys_details));
    } else {
        echo json_encode(array('success'=>false,'message' => 'Il carrello è vuoto.'));
    }
}


function simulatePurchase() {
    $acquired_keys_details = array(); // Array per i dettagli delle chiavi acquisite
    global $key, $game, $purchase;

    foreach ($_SESSION['cart'] as $cart_item) {
        $game_id = $cart_item['game_id'];
        $quantity = $cart_item['quantity'];

        // Simulazione dell'acquisto della chiave per ciascun gioco nel carrello
        $unacquired_keys = $key->getUnacquiredKeysForGameWithQuantity($game_id, $quantity);

        $game_details = $game->getGameById($game_id);
        $platform = $game_details['platform'];

        // Segna le chiavi come acquisite (simulazione)
        foreach ($unacquired_keys as $key_data) {
            $key_id = $key_data['id'];
            $key->markKeyAsAcquired($key_id);
            $encrypted_key = $key_data['key_value'];

            // Decrittazione della chiave usando la funzione decryptText
            $decrypted_key = decryptText($encrypted_key);

            // Ottenere il titolo del gioco
            $game_details = $game->getGameById($game_id);
            $game_title = $game_details['title'];

            // Aggiungi i dettagli della chiave acquisita all'array
            $acquired_keys_details[] = array(
                'game_title' => $game_title,
                'platform' => $platform, // Aggiungi la piattaforma
                'key_code' => $decrypted_key,
            );

            // Aggiungi i dettagli della chiave acquisita alla tabella purchases
            $purchase_date = date("Y-m-d H:i:s"); // Data simulata di acquisto
            $card_id = $_POST['radioButtonId']; // ID  della carta selezionata
            $purchase->addPurchase($purchase_date, $game_id, $_SESSION['user']['id'], $key_id, $card_id);
        }

    }
    $_SESSION['purchase'] = $purchase->getPurchasesByUserId($_SESSION['user']['id']);

    // Svuota il carrello dopo l'acquisto (simulazione)
    global $cart;
    if ($cart->deleteCartFromUserId($_SESSION['user']['id']))
        $_SESSION['cart'] = $cart->getCartItems($_SESSION['user']['id']);

    return $acquired_keys_details;
}

function maskCardNumber($card_number) {
    $length = strlen($card_number);
    $visible_digits = min(4, $length);
    $masked_part = str_repeat('*', $length - $visible_digits) . substr($card_number, -$visible_digits);
    return $masked_part;
}

function maskValue($value) {
    return str_repeat('*', strlen($value));
}

function getCardById($cardId) {
    global $card;
    return $card->getCardById($cardId);
}


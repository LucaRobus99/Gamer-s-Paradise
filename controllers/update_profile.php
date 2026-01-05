<?php
/**
 * Questo script PHP gestisce le operazioni relative al profilo utente e alle carte di credito registrate.
 * Include funzioni per aggiungere, eliminare e ottenere le carte di credito dell'utente,
 * nonché per aggiornare le informazioni del profilo utente.
 * Verifica l'autenticazione dell'utente e la validità della sessione.
 * Restituisce risposte JSON con successo o messaggi di errore in base all'esito delle operazioni.
 */
session_start();
require_once '../model/Card.php';
require_once '../util/encrypt_decrypt.php';
require_once '../model/Purchase.php';
require_once '../model/User.php';
require_once '../model/Game.php';
require_once '../model/Key.php';
require_once '../util/server_validator.php';

$response = array();
$card = new Card();
$user = new User();
$purchase = new Purchase();
$game = new Game();
$key = new Key();

// Verifica se l'utente ha effettuato l'accesso
if (isset($_SESSION['user'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Assicurati che sia stata fornita un'azione
        if (isset($_POST['action'])) {
            $action = $_POST['action'];

            // Gestione dell'azione "delete_card"
            if ($action === 'delete_card') {
                if (isset($_POST['cardId'])) {
                    $cardId = $_POST['cardId'];
                    if ($card->deleteCard($cardId, $_SESSION['user']['id'])) {
                        $_SESSION['cards'] = $card->getCardsByUserId($_SESSION['user']['id']);
                        $response = array('success' => true);
                    } else {
                        $response = array('success' => false);
                    }
                } else {
                    $response = array('success' => false, 'message' => 'ID della carta mancante.');
                }
            }
            // Gestione dell'azione "add_card"
            else if ($action === 'add_card') {
                if (
                    isset($_POST['numCard']) &&
                    isset($_POST['expireMonth']) &&
                    isset($_POST['expireYear']) &&
                    isset($_POST['HolderName']) &&
                    isset($_POST['HolderSurname']) &&
                    isset($_POST['CVV'])
                ) {
                    // Verifica se i dati non sono vuoti
                    if (
                        !empty($_POST['numCard']) &&
                        !empty($_POST['expireMonth']) &&
                        !empty($_POST['expireYear']) &&
                        !empty($_POST['HolderName']) &&
                        !empty($_POST['HolderSurname']) &&
                        !empty($_POST['CVV'])
                    ) {
                        // Validazione dei campi del modulo
                        $numCard = ValidationHelper::validateCreditCard($_POST['numCard'], $_POST['expireMonth'] . '/' . $_POST['expireYear'], $_POST['CVV']);
                        $HolderName = ValidationHelper::validateName($_POST['HolderName']);
                        $HolderSurname = ValidationHelper::validateName($_POST['HolderSurname']);

                        if ($numCard && $HolderName && $HolderSurname) {
                            $expireDate = $_POST['expireMonth'] . '/' . $_POST['expireYear'];
                            $cartaEsiste = false;

                            // Ciclo attraverso l'array delle carte registrate
                            foreach ($_SESSION['cards'] as $item) {
                                if (
                                    $item['card_number'] === $numCard['cardNumber'] &&
                                    $item['expiring_date'] === $numCard['expirationDate'] &&
                                    $item['card_holder_name'] === $HolderName &&
                                    $item['card_holder_lastname'] === $HolderSurname &&
                                    decryptText($item['cvv']) === $numCard['cvv'] &&
                                    $item['user_id'] === $_SESSION['user']['id']
                                ) {
                                    // La carta di credito esiste nell'array
                                    $cartaEsiste = true;
                                    break;
                                }
                            }

                            if ($cartaEsiste) {
                                $response = array('success' => false, 'message' => 'Questa carta è già registrata nel tuo portafoglio');
                            } else {
                                $cardData = array(
                                    'card_number' => $numCard['cardNumber'],
                                    'card_holder_name' => $HolderName,
                                    'card_holder_lastname' => $HolderSurname,
                                    'cvv' => encryptText($numCard['cvv']),
                                    'expiring_date' => $numCard['expirationDate'],
                                    'user_id' => $_SESSION['user']['id']
                                );
                                $id = $card->createCard($cardData);
                                if ($id) {
                                    $response = array('success' => true, 'message' => 'La carta è stata registrata con successo.', 'dataCard' => array('numCard' => maskCardNumber($numCard['cardNumber']),
                                        'expireMonth' => $_POST['expireMonth'], 'expireYear' => $_POST['expireYear'], 'idCard' => $id));
                                } else {
                                    $response = array('success' => false, 'message' => 'Errore durante la registrazione della carta.');
                                }

                                $_SESSION['cards'] = $card->getCardsByUserId($_SESSION['user']['id']);
                            }
                        } else {
                            $response = array('success' => false, 'message' => 'I dati della carta di credito non sono validi.');
                        }
                    } else {
                        $response = array('success' => false, 'message' => 'I dati della carta di credito sono incompleti o vuoti.');
                    }
                } else {
                    $response = array('success' => false, 'message' => 'Dati POST mancanti.');
                }
            }
            // Gestione dell'azione "update_profile"
            else if ($action === 'update_profile') {
                $updates = '';
                $id_user = $_SESSION['user']['id'];
                if (!empty($_POST['Name'])) {
                    $user->setFirstName($id_user, $_POST['Name']);
                    $updates .= 'nuovo nome aggiornato ';
                }
                if (!empty($_POST['Surname'])) {
                    $user->setLastName($id_user, $_POST['Surname']);
                    $updates .= 'nuovo cognome aggiornato ';
                }
                if (!empty($_POST['Email'])) {
                    $user->setEmail($id_user, $_POST['Email']);
                    $updates .= 'nuova email aggiornata ';
                }
                if (!empty($_POST['Password'])) {
                    $user->setPassword($id_user, $_POST['Password']);
                    $updates .= 'nuova password aggiornata ';
                }
                $_SESSION['user'] = $user->getUserById($_SESSION['user']['id']);
                $response = array('success' => true, 'message' => $updates);
            }
        } else {
            $response = array('success' => false, 'message' => 'Azione mancante.');
        }
    }
    // Gestione della richiesta GET
    else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Azione per ottenere le carte dell'utente
        if (isset($_GET['action']) && $_GET['action'] === 'get_cards') {
            $card_array = $_SESSION['cards'];
            foreach ($card_array as &$card) {
                $decrypted_cvv = decryptText($card['cvv']);
                $card['cvv'] = maskValue($decrypted_cvv);
                $card_number = $card['card_number'];
                $masked_card_number = maskCardNumber($card_number);
                $card['card_number'] = $masked_card_number;
            }
            $response = array('success' => true, 'message' => 'GetCards.', 'cards' => $card_array);
        }
        // Azione per ottenere gli acquisti dell'utente
        else {
            if (isset($_SESSION['user']['id'])) {
                $purchasesWithDetails = [];

                foreach ($_SESSION['purchase'] as $item) {
                    $orderDate = $item["order_date"];
                    $gameId = $item["game_id"];
                    $keyId = $item["key_id"];

                    $key_details = $key->getKeyFromId($keyId);
                    // Ottieni i dettagli del gioco
                    $gameDetails = $game->getGameById($gameId);

                    if ($gameDetails && $key_details) {
                        $title = $gameDetails["title"];
                        $platform = $gameDetails["platform"];
                        $key_value = decryptText($key_details['key_value']);
                        // Crea un nuovo array con i campi desiderati
                        $purchaseDetails = [
                            "date" => $orderDate,
                            "title" => $title,
                            "platform" => $platform,
                            "key_value" => $key_value
                        ];

                        // Aggiungi i dettagli dell'acquisto all'array risultante
                        $purchasesWithDetails[] = $purchaseDetails;
                    }
                }

                $response = array('success' => true, 'message' => 'Acquisti trovati.', 'purchasesWithDetails' => $purchasesWithDetails);
            } else {
                $response = array('success' => false, 'message' => 'Sessione utente non valida.');
            }
        }
    } else {
        $response = array('success' => false, 'message' => 'Richiesta non valida.');
    }
} else {
    $response = array('success' => false, 'message' => 'Utente non autenticato.');
}

// Funzione per mascherare i numeri di carta di credito
function maskCardNumber($card_number) {
    $length = strlen($card_number);
    $visible_digits = min(4, $length);
    return str_repeat('*', $length - $visible_digits) . substr($card_number, -$visible_digits);
}

// Funzione per mascherare un valore
function maskValue($value) {
    return str_repeat('*', strlen($value));
}

// Restituisci la risposta come JSON
header('Content-Type: application/json');
echo json_encode($response);
?>

<?php
/**
 * Questo script PHP gestisce le richieste relative all'amministrazione di giochi e chiavi.
 * Verifica l'autenticazione dell'utente come amministratore e gestisce le azioni GET e POST per recuperare informazioni,
 * aggiungere chiavi e eliminare chiavi per i giochi.
 * Le richieste e le risposte sono gestite in formato JSON.
 */
session_start();

// Inizializza una variabile di risposta
$response = array();

// Verifica se l'utente è autenticato come amministratore
if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 1) {
    // L'utente è loggato come amministratore, procedi con lo script

    // Include le classi necessarie
    require_once '../model/Key.php';
    require_once '../model/Game.php';
    require_once '../util/encrypt_decrypt.php';
    require_once '../util/server_validator.php';

    // Inizializza oggetti per gestire chiavi e giochi
    $key = new Key();
    $game = new Game();

    // Verifica il metodo della richiesta (GET o POST)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['action'])) {
            $action = $_GET['action'];

            // Azione: 'get_info_game' (recupero informazioni su un gioco)
            if ($action === 'get_info_game') {
                if (isset($_GET['id']) && !empty($_GET['id'])) {
                    $response['success'] = true;
                    $response['GameInfo'] = $game->getGameById($_GET['id']);
                    $keys = $key->getAllKeysForGame($_GET['id']);
                    foreach ($keys as &$key_) {
                        $keyValue_decrypted = decryptText($key_['key_value']);
                        $key_['key_value'] = $keyValue_decrypted;
                    }

                    $response['Keys'] = $keys;
                } else {
                    $response['success'] = false;
                    $response['message'] = 'ID non valido';
                }
            } else {
                $response['success'] = false;
                $response['message'] = 'Azione non valida';
            }
        } else {
            $response['success'] = false;
            $response['message'] = 'Azione mancante';
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            $action = $_POST['action'];

            // Azione: 'add_key' (aggiunta di una chiave)
            if ($action === 'add_key') {
                if (isset($_POST['idGame']) && !empty($_POST['idGame']) && isset($_POST['key_value']) && !empty($_POST['key_value'])) {
                    $key_cleaned = ValidationHelper::validateGameKey($_POST['key_value']);
                    if ($key_cleaned && $game->getGameById($_POST['idGame'])) {
                        $key_exist = false;
                        foreach ($key->getAllKeys() as $key_on_db) {
                            if ($key_cleaned === decryptText($key_on_db['key_value'])) {
                                $key_exist = true;
                                break;
                            }
                        }
                        if (!$key_exist) {
                            $last_key_added = $key->insertNewKey(encryptText($key_cleaned), $_POST['idGame']);
                            if ($last_key_added) {
                                $last_key_added['key_value'] = $key_cleaned;
                                $response['success'] = true;
                                $response['message'] = 'Chiave aggiunta con successo';
                                $response['lastkey'] = $last_key_added; // Includi $last_key_added con il campo 'key_value' decriptato nella risposta
                            } else {
                                $response['success'] = false;
                                $response['message'] = 'Chiave non aggiunta';
                            }
                        } else {
                            $response['success'] = false;
                            $response['message'] = "Chiave già esistente";
                        }
                    } else {
                        $response['success'] = false;
                        $response['message'] = 'Chiave non valida o gioco non esistente';
                    }
                } else {
                    $response['success'] = false;
                    $response['message'] = 'ID gioco mancante o chiave non valida';
                }
            } elseif ($action === 'delete_key') {
                // Azione: 'delete_key' (eliminazione di una chiave)
                if (isset($_POST['idKey']) && !empty($_POST['idKey'])) {
                    if ($key->getKeyFromId($_POST['idKey'])) {
                        if ($key->deleteKeyFromId($_POST['idKey'])) {
                            $response['success'] = true;
                            $response['message'] = 'Chiave eliminata con successo';
                        } else {
                            $response['success'] = false;
                            $response['message'] = 'Errore durante l\'eliminazione della chiave';
                        }
                    } else {
                        $response['success'] = false;
                        $response['message'] = 'Chiave non trovata';
                    }
                } else {
                    $response['success'] = false;
                    $response['message'] = 'ID chiave mancante o non valido';
                }
            }
        } else {
            $response['success'] = false;
            $response['message'] = 'Azione mancante';
        }
    }
} else {
    // Accesso non autorizzato
    $response['success'] = false;
    $response['message'] = 'Accesso non autorizzato';
}

// Imposta l'intestazione della risposta come JSON
header('Content-Type: application/json');
// Restituisci la risposta come JSON
echo json_encode($response);
?>

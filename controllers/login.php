<?php
/**
 * Questo script PHP gestisce il processo di login dell'utente. Include classi e funzioni necessarie per
 * la validazione dei dati, l'autenticazione dell'utente e la gestione della sessione.
 * Se l'utente è già autenticato, lo reindirizza alla pagina principale. Altrimenti, gestisce la richiesta di login.
 */

// Includi le classi e le funzioni necessarie
require_once '../util/server_validator.php';
require_once '../util/redirectTO.php';
require_once '../model/User.php';
require_once '../model/Cart.php';
require_once '../model/Purchase.php';
require_once '../model/Card.php';


if(!isset($_SESSION)){
    session_start();
}

// Funzione per il login dell'utente
function loginUser($email, $password) {
    $u = new User();
    $log = $u->login($email, $password);
    return $log;
}

// Funzione per gestire il login
function handleLogin() {
    // Verifica se la richiesta HTTP è di tipo POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verifica se sono presenti dati POST validi (email e password)
        if (!empty($_POST) && isset($_POST['email']) && isset($_POST['password'])) {
            // Esegue la validazione dell'email e della password
            $email = ValidationHelper::validateEmail($_POST['email']);
            $pass = ValidationHelper::validatePassword($_POST['password']);

            // Se l'email e la password sono valide
            if ($pass && $email) {
                // Effettua il login dell'utente
                $log = loginUser($email, $_POST['password']);
                if ($log !== false) {
                    // Crea istanze delle classi Cart, Card e Purchase
                    $cart = new Cart();
                    $card = new Card();
                    $purchase = new Purchase();

                    // Imposta l'utente nella sessione
                    $_SESSION['user'] = $log;

                    // Se l'utente non è un amministratore
                    if ($_SESSION['user']['role'] != 1) {
                        // Ottieni il carrello, le carte e gli acquisti dell'utente
                        $_SESSION['cart'] = $cart->getCartItems($_SESSION['user']['id']);
                        $_SESSION['cards'] = $card->getCardsByUserId($_SESSION['user']['id']);
                        $_SESSION['purchase'] = $purchase->getPurchasesByUserId($_SESSION['user']['id']);
                    }

                    // Reindirizza l'utente alla pagina principale
                    redirectToPage('../.');
                } else {
                    // Mostra un messaggio di errore e reindirizza alla pagina di login in caso di credenziali errate
                    sendAlert('danger', "Email o Password non corretti. Riprova");
                    redirectToPage('../view/login_view.php');
                }
            }

            // Mostra un messaggio di errore e reindirizza alla pagina di login in caso di dati non validi
            sendAlert('danger', "Email o Password non corretti. Riprova");
            redirectToPage('../view/login_view.php');
        }

        // Mostra un messaggio di errore e reindirizza alla pagina di login in caso di dati mancanti
        sendAlert('danger', "Email o Password non corretti. Riprova");
        redirectToPage('../view/login_view.php');
    }

    // Mostra un messaggio di errore e reindirizza alla pagina di login in caso di richiesta non valida
    sendAlert('danger', "Email o Password non corretti. Riprova");
    redirectToPage('../view/login_view.php');
}

// Verifica se l'utente è già autenticato
if (isset($_SESSION['user'])) {
    // Reindirizza l'utente alla pagina principale se è già autenticato
    redirectToPage('../.');
} else {
    // Altrimenti, gestisci il login
    handleLogin();
}

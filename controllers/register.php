<?php
/**
 * Questo script PHP gestisce il processo di registrazione di un nuovo utente. Include funzioni per registrare un nuovo utente,
 * effettuare il login automatico dell'utente appena registrato e gestire la validazione dei dati inseriti. Se l'utente viene registrato
 * con successo, viene automaticamente effettuato il login, e vengono caricate le informazioni del carrello, delle carte e degli acquisti
 * dell'utente nelle variabili di sessione. Se l'utente è già autenticato, viene reindirizzato alla pagina principale. In caso di dati
 * mancanti o non validi, vengono mostrati messaggi di errore appropriati.
 */

// Includi le librerie e i modelli necessari
require_once '../util/server_validator.php';
require_once '../util/redirectTO.php';
require_once '../model/User.php';
require_once '../model/Cart.php';
require_once '../model/Purchase.php';
require_once '../model/Card.php';
require_once '../util/alert.php';

// Inizia una sessione o riprendi quella esistente
if(!isset($_SESSION)){
    session_start();
}

// Funzione per registrare un nuovo utente
function registerUser($email, $first_name, $last_name, $password) {
    $u = new User();
    $registerUser = $u->register($email, $first_name, $last_name, $password);
    return $registerUser;
}

// Funzione per effettuare il login di un utente
function loginUser($email, $password) {
    $u = new User();
    return $u->login($email, $password);
}

// Funzione principale per gestire il processo di registrazione
function handleRegistration() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['first_name']) && isset($_POST['last_name'])) {
            // Validazione dei dati inseriti
            $email = ValidationHelper::validateEmail($_POST['email']);
            $first_name = ValidationHelper::validateName($_POST['first_name']);
            $last_name = ValidationHelper::validateName($_POST['last_name']);
            $password = ValidationHelper::validatePassword($_POST['password']);

            if ($password && $email && $first_name && $last_name) {
                // Registra l'utente nel database
                $registerUser = registerUser($email, $first_name, $last_name, $password);

                if ($registerUser) {
                    // Login automatico dell'utente appena registrato
                    $user = loginUser($email, $_POST['password']);

                    // Ottieni il carrello, le carte e gli acquisti dell'utente e salvali nelle variabili di sessione
                    $cart = new Cart();
                    $card = new Card();
                    $purchase = new Purchase();
                    $_SESSION['user'] = $user;
                    $_SESSION['cart'] = $cart->getCartItems($_SESSION['user']['id']);
                    $_SESSION['cards'] = $card->getCardsByUserId($_SESSION['user']['id']);
                    $_SESSION['purchase'] = $purchase->getPurchasesByUserId($_SESSION['user']['id']);

                    // Reindirizza l'utente alla pagina principale
                    redirectToPage('../');
                } else {
                    // Mostra un messaggio di errore se l'utente è già registrato
                    sendAlert('danger', "Utente già registrato.");
                    redirectToPage('../view/register_view.php');
                }
            } else {
                // Mostra un messaggio di errore se i dati inseriti non sono validi
                sendAlert('danger', "Dati non validi, riprovare.");
                redirectToPage('../view/register_view.php');
            }
        } else {
            // Mostra un messaggio di errore se i dati sono mancanti
            sendAlert('danger', "Inserisci dei dati!");
            redirectToPage('../view/register_view.php');
        }
    } else {
        // Reindirizza l'utente alla pagina principale se la richiesta non è una POST
        redirectToPage('../.');
    }
}

// Verifica se l'utente è già autenticato
if (isset($_SESSION['user'])) {
    // Se l'utente è già autenticato, reindirizzalo alla pagina principale
    redirectToPage('../.');
} else {
    // Se l'utente non è autenticato, gestisci il processo di registrazione
    handleRegistration();
}
?>

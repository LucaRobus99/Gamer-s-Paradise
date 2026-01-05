<?php
/*
 * Questo script PHP gestisce il logout dell'utente. Include la funzione di reindirizzamento per reindirizzare
 * l'utente alla pagina principale dopo il logout. Se l'utente è autenticato, vengono cancellate tutte le variabili di sessione,
 * e l'utente viene scollegato. Se l'utente non è autenticato, viene comunque reindirizzato alla pagina principale.
 */
// Includi la funzione di reindirizzamento
require_once '../util/redirectTO.php';

// Funzione per il logout dell'utente
function logoutUser() {
    // Inizia la sessione
    session_start();

    // Verifica se l'utente è autenticato
    if (isset($_SESSION['user'])) {
        // Cancella tutte le variabili di sessione
        session_unset();
        session_destroy();
        unset($_SESSION['user']);
        unset($_SESSION['cart']);
        unset($_SESSION['cards']);
        unset($_SESSION['purchase']);

        // Reindirizza l'utente alla pagina principale dopo il logout
        redirectToPage('../');
    } else {
        // Reindirizza l'utente alla pagina principale se non è autenticato
        redirectToPage('../');
    }
}

// Chiama la funzione di logout
logoutUser();

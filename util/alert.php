<?php
/**
 * Questo file contiene le funzioni per gestire messaggi di avviso
 * e visualizzarli all'interno delle pagine web.
 *
 * Le funzioni `sendAlert` e `displayAlert` possono essere utilizzate
 * per inviare e visualizzare messaggi di avviso ai visitatori del sito.
 */
function sendAlert($type, $message) {
    unset($_SESSION["error_message"]);

    $_SESSION["error_message"] = "<div class=\"alert alert-$type\">$message</div>";
}

function displayAlert() {
    if (isset($_SESSION["error_message"])) {
        $alert = $_SESSION["error_message"];

        unset($_SESSION["error_message"]);

        return $alert;
    }

}


<?php
/**
 * Questa funzione reindirizza l'utente a una pagina specificata utilizzando l'header "Location".
 *
 *
 */
function redirectToPage($pageName) {
    header("Location: " . $pageName);
    exit();
}
?>

<?php
session_start();
require_once '../config.php';
require_once '../util/redirectTO.php';
include (__DIR__ . '/includes/header.php');


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 1) {
    redirectToPage('../.'); // Specifica la pagina di reindirizzamento corretta
}
?>
<link rel="stylesheet" type="text/css" href="../css/admin.css">
<div class="container mt-5">
    <!-- Visualizzazione delle informazioni del gioco -->
    <div id="gameDataContainer">

    </div>




    <!-- Form per aggiungere una chiave -->
    <div class="row mt-4">
        <div class="col-md-6 mx-auto my-auto">
            <form id="aggiungiChiaveForm">
                <div class="input-group">
                    <input type="text" class="form-control" id="chiave" placeholder="Inserisci la chiave" required pattern="^[a-zA-Z0-9]{12}$" title="Inserisci una chiave di 12 caratteri alfanumerici (lettere maiuscole/minuscole e numeri).">
                    <button type="submit" class="btn btn-primary">Aggiungi Chiave</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<p id="error" class="mt-5 mb-5"></p>
    <!-- Tabella delle chiavi di attivazione -->
    <div class="row mt-4">
        <div class="table">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Chiave</th>
                    <th>Stato</th>
                    <th>Azioni</th>
                </tr>
                </thead>
                <tbody id="chiaviTabella">
                <!-- Qui verranno aggiunte le righe della tabella dinamicamente -->
                </tbody>
            </table>
        </div>
    </div>


<?php
// Includi il footer
include (__DIR__ . '/includes/footer.php');
?>

<script src="../js/admin_key.js"></script>

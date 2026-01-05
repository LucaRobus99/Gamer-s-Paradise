<?php
session_start();
require_once '../config.php';
require_once '../util/redirectTO.php';
include (__DIR__ . '/includes/header.php');
if(!isset($_SESSION['user']) ||$_SESSION['user']['role']!=1){
    redirectToPage('../.');
}
?>

<link rel="stylesheet" type="text/css" href="../css/admin.css">

<div class="container mt-5 mb-5">
    <h1>Admin Panel - Gestione Videogiochi</h1>

    <!-- Form to add a new video game -->
    <div class="card mt-4">
        <div class="card-header">
            Aggiungi un nuovo videogioco
        </div>
        <div class="card-body">
            <form id="addGameForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="gameTitle">Titolo del Videogioco</label>
                    <input type="text" class="form-control" id="gameTitle" required pattern="^[A-Za-z0-9_]+$" title="Perfavore inserisci solo dei caratteri validi">
                </div>
                <div class="form-group">
                    <label for="gameGenre">Genere</label>
                    <select class="form-control" id="gameGenre" required>
                        <option value="">Seleziona genere</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="gamePlatform">Piattaforma</label>
                    <select class="form-control" id="gamePlatform" name="platform" required>
                        <option value="">Seleziona la piattaforma</option>
                        <!-- Options will be dynamically populated here -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="gameDescription">Descrizione del Videogioco</label>
                    <textarea class="form-control" id="gameDescription" rows="4" required pattern="^[A-Za-z0-9\s.,!?()-]+$" title="Inserisci una descrizione valida (solo lettere, numeri, spazi e segni di punteggiatura comuni)"></textarea>
                </div>
                <div class="form-group">
                    <label for="gamePrice">Prezzo del Videogioco</label>
                    <input type="number" class="form-control" id="gamePrice" required pattern="^\d+\.\d{2}$" title="Inserisci un prezzo valido (numerico positivo con esattamente due cifre decimali)">
                </div>
                <div class="form-group">
                    <label for="gameImage">Immagine (JPG)</label>
                    <input type="file" class="form-control-file" id="gameImage" accept=".jpg, .jpeg" required>
                </div>

                <button type="submit" class="btn btn-primary">Aggiungi Videogioco</button>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            Elenco dei Videogiochi
        </div>
        <div class="card-body">
            <div class="input-group mb-3">
                <input type="text" id="gameSearch" class="form-control" placeholder="Cerca un videogioco">
            </div>
            <ul id="gameList" class="list-group">
                <!-- Video games from the database will be displayed here -->
            </ul>
        </div>
    </div>



</div>

<?php
// Includi il footer
include (__DIR__ . '/includes/footer.php');
?>
<script src="../js/admin_games.js"></script>

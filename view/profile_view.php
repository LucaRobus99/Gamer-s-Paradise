<?php
session_start();
require_once '../config.php';
require_once '../util/redirectTO.php';
include (__DIR__ . '/includes/header.php');
if (!isset($_SESSION['user'])) {
    redirectToPage('../view/login_view.php');
}
?>
<link rel="stylesheet" type="text/css" href="../css/profile.css">
<div class="container mt-5">
    <h1>Profilo Utente</h1>

    <!-- Informazioni personali -->
    <div class="card mt-4">
        <div class="card-header">
            Informazioni Personali
        </div>
        <div class="card-body">
            <!-- Form per modificare le informazioni personali -->
            <form id="formInformazioniPersonali">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" class="form-control" id="nome" placeholder="<?=$_SESSION['user']['first_name']?>" >
                </div>
                <div class="form-group">
                    <label for="cognome">Cognome:</label>
                    <input type="text" class="form-control" id="cognome" placeholder="<?=$_SESSION['user']['last_name']?>" >
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" placeholder="<?=$_SESSION['user']['email']?>" >
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" >
                </div>
                <button type="submit" class="btn btn-primary">Salva Modifiche</button>
            </form>
        </div>
    </div>

    <?php
    if ($_SESSION['user']['role'] != 1) {

        ?>
        <!-- Carte di Credito -->
        <div class="card mt-4">
            <div class="card-header">
                Carte di Credito
            </div>
            <div class="card-body">
                <!-- Lista delle carte di credito -->
                <ul id="carteDiCredito" class="list-group">
                    <!-- Le carte di credito saranno aggiunte dinamicamente qui -->
                </ul>

                <!-- Form per aggiungere una nuova carta di credito -->
                <form id="formCartaDiCredito" class="mt-3">
                    <div class="form-group">
                        <label for="nomeProprietario">Nome del Proprietario:</label>
                        <input type="text" class="form-control" id="nomeProprietario" required>
                    </div>
                    <div class="form-group">
                        <label for="cognomeProprietario">Cognome del Proprietario:</label>
                        <input type="text" class="form-control" id="cognomeProprietario" required>
                    </div>
                    <div class="form-group">
                        <label for="numeroCarta">Numero Carta di Credito:</label>
                        <input type="text" class="form-control" id="numeroCarta" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="scadenzaMese">Scadenza Mese:</label>
                            <select class="form-control" id="scadenzaMese" required>
                                <option value="">Seleziona il mese</option>
                                <?php
                                for ($mese = 1; $mese <= 12; $mese++) {
                                    $meseConZero = str_pad($mese, 2, '0', STR_PAD_LEFT); // Aggiunge lo zero se necessario
                                    echo "<option value='$meseConZero'>$meseConZero</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="scadenzaAnno">Scadenza Anno:</label>
                            <select class="form-control" id="scadenzaAnno" required>
                                <option value="">Seleziona l'anno</option>
                                <?php
                                for ($anno = 23; $anno <= 30; $anno++) {
                                    echo "<option value='$anno'>$anno</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV:</label>
                        <input type="text" class="form-control" id="cvv" required>
                    </div>
                    <button type="submit" class="btn btn-success">Aggiungi Carta</button>
                </form>
            </div>
        </div>

        <!-- Lista degli Acquisti -->
        <div class="card mt-4">
            <div class="card-header">
                Lista degli Acquisti
            </div>
            <div class="card-body" id="acquisti">
                <!-- La lista degli acquisti verrà aggiunta dinamicamente qui -->
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Data e Ora</th>
                        <th>Nome del Gioco</th>
                        <th>Piattaforma</th>
                        <th>Chiave</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Gli elementi dell'acquisto verranno aggiunti qui -->
                    </tbody>
                </table>
            </div>
        </div>
    <?php }  ?>

</div>

<?php
// Include the footer
include (__DIR__ . '/includes/footer.php');
?>
<script src="../js/profile.js"></script>

<?php
session_start();
require_once '../config.php';
require_once '../util/redirectTO.php';
include (__DIR__ . '../includes/header.php');
if(!isset($_GET['id'])){
    redirectToPage('../.');
}

?>
<link rel="stylesheet" type="text/css" href="../css/game_profile.css">


<div id="game-info" class="container mt-5">
    <div class="row">
        <!-- Qui andranno le informazioni del videogioco -->
    </div>
</div>




<?php
// Includi il footer
include (__DIR__ . '/includes/footer.php');
?>

<script src="<?= $GLOBALS ['dir']; ?>/js/game_profile.js"></script>

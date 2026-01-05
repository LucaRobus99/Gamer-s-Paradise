<?php
session_start();
require_once '../config.php';
require_once '../util/redirectTO.php';
include (__DIR__ . '/includes/header.php');
if(!isset($_SESSION['user'])&&!isset($_SESSION['cart'])){
    redirectToPage('../view/login_view.php');
}
?>

<link rel="stylesheet" type="text/css" href="../css/cart.css">

<body>
<div class="cart-container">
    <div class="games-list">
        <!-- I giochi nel carrello saranno aggiunti qui tramite JavaScript -->
    </div>
    <div class="cart-summary">
        <!-- Il riepilogo dell'ordine sarà aggiunto qui tramite JavaScript -->
    </div>
</div>

</body>





<?php
// Includi il footer
include (__DIR__ . '/includes/footer.php');
?>
<script src="../js/cart.js"></script>
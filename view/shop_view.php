<?php
session_start();
require_once '../config.php';
 include (__DIR__ . '/includes/header.php');


?>

<link rel="stylesheet" type="text/css" href="../css/shop.css">
<main >

<div id="filters">

</div>
<div class="container mt-5" id="gamesContainer">
</div>

</main>

<?php
// Includi il footer
include (__DIR__ . '/includes/footer.php');
?>
<script src='../js/shop.js'></script>


<?php
session_start();
require_once '../config.php';
require_once '../util/redirectTO.php';
include (__DIR__ . '/includes/header.php');
if(!isset($_SESSION['user'])){
    redirectToPage('../view/login_view.php');
}
?>


<link rel="stylesheet" type="text/css" href="../css/payment.css">
<body>
<div class="progress-container">
    <div class="step active">1</div>
    <div class="progress-line"></div>
    <div class="step">2</div>
    <div class="progress-line"></div>
    <div class="step">3</div>
</div>



<div class="buttons-container">
    <button id="homeButton">Torna alla Home</button>
    <button id="backButton">Indietro</button>
    <button id="nextButton">Avanti</button>
</div>

<div id="stepContainer1" class="step-container">

</div>

<div id="stepContainer2" class="step-container">

</div>

<div id="stepContainer3" class="step-container">

</div>

<?php
// Includi il footer
include (__DIR__ . '/includes/footer.php');
?>



<script src="../js/payment.js"></script>
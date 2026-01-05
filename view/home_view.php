<?php include(__DIR__ . '/includes/header.php');?>


<link rel="stylesheet" type="text/css" href="css/home.css">


<main class="custom-main-container mt-5">
    <div class="row">
        <section class="col-md-6">
            <div class="welcome">
      
                <h1>Benvenuti a Gamer's Paradise</h1>
                <p>Realizza i tuoi sogni da Gamer con Gamer's Paradise: Dove le chiavi aprono Mondi!</p>
                <img  src="<?=  $GLOBALS['dir']?>img/logo.png" alt="Logo">
            </div>
        </section>

        <section class="col-md-6">
            <h2>Giochi più acquistati</h2>
            <div class="slider">
                <!-- Le tue immagini saranno visualizzate qui dinamicamente -->
            </div>
        </section>
    </div>


    <section class="lastadd">
        <h2 >Ultime aggiunte</h2>
        <div id="recentPurchases" class="row">

        </div>
    </section>


</main>

<?php include(__DIR__ . '/includes/footer.php');?>


<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

<script src="js/home.js"></script>

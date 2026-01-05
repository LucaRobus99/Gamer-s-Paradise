<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?=  $GLOBALS['dir']?>img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.2/dist/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <?php
    $currentPage = basename($_SERVER['PHP_SELF']); // Ottieni il nome del file corrente
    if ($currentPage !== 'index.php') {
        echo '<link rel="stylesheet" href="../css/footer.css">';
        echo '<link rel="stylesheet" href="../css/header.css">';
    }
    ?>
    <title>Gamer's Paradise</title>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark  fixed-top">
        <a class="navbar-brand " href="<?=  $GLOBALS['dir']?>"> <img id='logo' src="<?=  $GLOBALS['dir']?>img/logo.png" alt="Logo"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="console collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav mx-auto">

                <li class="nav-item">
                    <a class="nav-link"   href="<?php echo $GLOBALS['dir']?>view/shop_view.php?platform=PC">PC</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $GLOBALS['dir']?>view/shop_view.php?platform=Playstation">Playstation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"href="<?php echo $GLOBALS['dir']?>view/shop_view.php?platform=Xbox">Xbox</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $GLOBALS['dir']?>view/shop_view.php?platform=Nintendo">Nintendo</a>
                </li>
            </ul>
        </div>
        <div class="collapse navbar-collapse mx-auto text-center small-nav" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php
                if (isset($_SESSION['user'])) {
                    echo '
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user"></i>
                                
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href='.$GLOBALS['dir'].'view/profile_view.php>Profilo</a>
                                <a class="dropdown-item" href='.$GLOBALS['dir'].'controllers/logout.php>Logout</a>                              
                            </div>
                        </li>
                    ';
                    if ($_SESSION['user']['role'] == 1) {
                        echo '
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-cog"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="adminDropdown">
                                    <a class="dropdown-item" href='.$GLOBALS['dir'].'view/admin_games_view.php>Gestione Videogiochi</a>
                                </div>
                            </li>
                        ';
                    }else {
                        $totalQuantity = 0;

                        foreach ($_SESSION['cart'] as $item) {
                            $totalQuantity += $item['quantity'];
                        }

                        echo '
                <li class="nav-item">
                    <a class="nav-link" href='.$GLOBALS['dir'].'view/cart_view.php>
                        <i class="fas fa-shopping-cart"></i> 
                        <span id="cart-items-count" class="cart-items-count">'.$totalQuantity.'</span>
                    </a>
                </li>';
                    }
                } else {
                    echo '
                        <li class="nav-item">
                            <a class="nav-link" href='.$GLOBALS['dir'].'view/login_view.php>Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href='.$GLOBALS['dir'].'view/register_view.php>Registrazione</a>
                        </li>
                    ';
                }
                ?>
            </ul>
        </div>
    </nav>
</header>
<div class="mt-5 mb-5">

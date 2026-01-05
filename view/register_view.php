<?php
require_once '../util/redirectTO.php';
require_once '../util/alert.php';
session_start();
require_once '../config.php';

if( isset($_SESSION['user']) ){
    redirectToPage($GLOBALS['dir']);
}
?>




<?php include (__DIR__ . '/includes/header.php');?>


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-center">Registrazione</h2>
                </div>
                <div class="card-body">
                    <form method="post" action="../controllers/register.php">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="first_name">Nome:</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Cognome:</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <button type="submit" class="btn mb-4 btn-primary btn-block">Registrati</button>
                    </form>
                    <?php
                    if (isset($_SESSION["error_message"])) {
                        echo displayAlert();
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include (__DIR__ . '/includes/footer.php');?>



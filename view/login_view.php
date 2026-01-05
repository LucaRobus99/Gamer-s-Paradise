
<?php
require_once '../util/redirectTO.php';
session_start();
require_once '../util/alert.php';
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
             
                </div>
                <div class="card-body">
                    <h2 class="text-center">Login</h2>
                    <form action="../controllers/login.php" method="POST">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" class="form-control" required minlength="8">
                        </div>
                        <button type="submit" class="mb-4 btn  btn-primary btn-block">Login</button>
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


<?php include(__DIR__ . '/includes/footer.php');?>








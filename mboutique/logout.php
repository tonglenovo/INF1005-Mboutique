<?php
session_start();
session_unset(); // unset all session variables
session_destroy(); // destroy the session data
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include 'head.inc.php' ?>
    </head>
    <body>
        <?php include "nav.inc.php"; ?>
        <main class="container">
            <main class='container' id='message'>
                <h4>Logout successful!</h4>
                <a href='index.php' role='button' class='btn btn-success'>Return to Home</a>
            </main>

        </main>
        <?php include "footer.inc.php"; ?>
    </body>
</html>
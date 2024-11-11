<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();

    $success = true;
    $errorMsg = "";

    if (isset($_SESSION['loggedIn'])) {
        if ($_SESSION['loggedIn'] != '') {
            $errorMsg = "You are not allow to view this page";
            $success = false;
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include 'head.inc.php' ?>
    </head>
    <body>
        <?php include 'nav.inc.php' ?>

        <main class="container">
            <h1>Reset New Password</h1>
            <?php if (!$success) { ?>
                <main class='container' id='message'>
                    <h4>Opps!</h4>
                    <h4>The following input errors were detected:</h4>
                    <p><?php echo " " . $errorMsg . " "; ?></p>
                    <a href='index.php' role='button' class='btn btn-danger'>Back to home</a>

                </main>
            <?php } else { ?>
                <form action="process_forgot_password.php" method="post">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input class="form-control" type="email" id="email"
                               name="email" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">Submit</button>
                        <a href="login.php" class="btn btn-danger">Back</a>
                    </div>

                </form>
            <?php } ?>
        </main>

        <?php include'footer.inc.php' ?>
    </body>
</html>

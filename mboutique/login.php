<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'head.inc.php' ?>
    </head>
    <body>
        <?php include "nav.inc.php"; ?>
        <main class="container">
            <h1>Member Login</h1>
            <p>
                Existing members log in here. For new member, please go to the
                <a href="register.php">Sign Up page</a>.
            </p>
            <form action="process_login.php" method="post">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input class="form-control" type="email" id="email"
                           name="email" placeholder="Enter email">
                </div>
                <div class="form-group">
                    <label for="pwd">Password:</label>
                    <input class="form-control" type="password" id="pwd"
                           name="pwd" placeholder="Enter password">
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Submit</button>
                     <a href="forgetPassword.php" class="btn btn-danger">Reset New Password</a>
                </div>
                <div class="form-group">
                   
                </div>
            </form>

        </main>
        <?php include "footer.inc.php"; ?>
    </body>
</html>
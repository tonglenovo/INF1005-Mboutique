<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'head.inc.php' ?>
    </head>
    <body>
        <?php include "nav.inc.php"; ?>
        <main class="container">
            <h1>Member Registration</h1>
            <p>
                For existing members, please go to the
                <a href="login.php">Sign In page</a>.
            </p>
            <form action="process_register.php" method="post">
                <div class="form-group">
                    <label for="fname">First Name:</label>
                    <input class="form-control" type="text" id="fname"
                           name="fname" placeholder="Enter first name">
                </div>
                <div class="form-group">
                    <label for="lname">Last Name:</label>
                    <input class="form-control" type="text" id="lname"
                           name="lname" placeholder="Enter last name">
                </div>
                <div class="form-group">
                    <label for="email">Address Line 1:</label>
                    <input class="form-control" type="text" id="address1"
                           name="address1" placeholder="Enter postal code Eg. 123456">
                </div>
                <div class="form-group">
                    <label for="email">Address Line 2:</label>
                    <input class="form-control" type="text" id="address2"
                           name="address2" placeholder="Enter unit number Eg. #12-345">
                </div>
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
                    <label for="pwd_confirm">Confirm Password:</label>
                    <input class="form-control" type="password" id="pwd_confirm"
                           name="pwd_confirm" placeholder="Confirm password">
                </div>
                <div class="form-check">
                    <label>
                        <input type="checkbox" name="agree" required>
                        Agree to terms and conditions.
                    </label>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>

        </main>
        <?php include "footer.inc.php"; ?>
    </body>
</html>
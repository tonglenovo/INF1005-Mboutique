<?php
$payment_status = "not_yet";
$delivery_status = "Pending";
$cartCount = "";
$orderMemberCount = "";
$orderAdminCount = "";

if (!isset($_SESSION['loggedIn'])) {
    $_SESSION['loggedIn'] = false;
}
if (!isset($_SESSION['member_id'])) {
    $_SESSION['member_id'] = 0;
}
if (isset($_SESSION['member_id'])) {
    $mid = $_SESSION['member_id'];
}
if(isset($_SESSION['role'])){
    if($_SESSION['role'] == 'Member'){
        getOrderMemberCount();
    } else {
        getOrderAdminCount();
    }
}


getCartCount();


function getCartCount() {
    global $mid, $errorMsg, $success, $payment_status, $cartCount;
    include 'dbinfo.php';
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        // Prepare the statement:
        $stmt = $conn->prepare("SELECT sum(c.qty) as 'cartIndex' FROM cart c WHERE member_id = ? AND payment_status = ?");
        //$stmt = $conn->prepare("SELECT sum(c.qty) as 'cartIndex' FROM cart c LEFT JOIN payment p ON c.cart_id = p.cart_id WHERE p.payment_id IS NULL AND c.member_id = ?");
        // Bind & execute the query statement:
        $stmt->bind_param("ss", $mid, $payment_status);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $count = $result->fetch_assoc();
            $cartCount = $count['cartIndex'];
        } else {
            $cartCount = 0;
        }
        $stmt->close();
    }
    $conn->close();
}

function getOrderMemberCount() {
    global $mid, $errorMsg, $success, $delivery_status, $orderMemberCount;
    include 'dbinfo.php';
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        // Prepare the statement:
//        $stmt = $conn->prepare("SELECT count(*) as orderIndex FROM payment WHERE member_id = ? AND delivery_status = ? ");
         $stmt = $conn->prepare("SELECT count(*) as orderIndex FROM payment WHERE member_id = ?");
        //$stmt = $conn->prepare("SELECT sum(c.qty) as 'cartIndex' FROM cart c LEFT JOIN payment p ON c.cart_id = p.cart_id WHERE p.payment_id IS NULL AND c.member_id = ?");
        // Bind & execute the query statement:
        $stmt->bind_param("s", $mid);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $counter = $result->fetch_assoc();
            $orderMemberCount = $counter['orderIndex'];
        } else {
            $orderMemberCount = 0;
        }
        $stmt->close();
    }
    $conn->close();
}
function getOrderAdminCount() {
    global $errorMsg, $success, $delivery_status, $orderMemberCount;
    include 'dbinfo.php';
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        // Prepare the statement:
       // $stmt = $conn->prepare("SELECT count(*) as orderIndex FROM payment WHERE delivery_status = ? ");
         $stmt = $conn->prepare("SELECT count(*) as orderIndex FROM payment");
        //$stmt = $conn->prepare("SELECT sum(c.qty) as 'cartIndex' FROM cart c LEFT JOIN payment p ON c.cart_id = p.cart_id WHERE p.payment_id IS NULL AND c.member_id = ?");
        // Bind & execute the query statement:
//        $stmt->bind_param("s", $delivery_status);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $counter = $result->fetch_assoc();
            $orderMemberCount = $counter['orderIndex'];
        } else {
            $orderMemberCount = 0;
        }
        $stmt->close();
    }
    $conn->close();
}
?>


<nav class="navbar sticky-top navbar-expand-md navbar-light" style="background-color: #CAD1DE;"> 


    <!-- Brand -->
    <!--  <a class="navbar-brand" href="#">Navbar</a>-->
    <a href="index.php"><img src="images/logo3.png" alt="LOGO" style="width: 50px; height: 50px; border: 0;"></a>

    <!-- Toggler/collapsibe Button -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar links -->
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link nav_link_a" href="index.php">Home</a>
            </li>
            <!--            <li class="nav-item">
                            <a class="nav-link" href="viewClothing.php">Men</a>
                        </li>-->
            <!-- Dropdown -->
<!--            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                    Men's
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="viewClothing.php?type=Shirt">Top: Shirt</a>
                    <a class="dropdown-item" href="viewClothing.php?type=T-shirt">Top: T-shirt</a>
                    <a class="dropdown-item" href="viewClothing.php?type=Outerwear">Top: Outerwear</a>
                    <a class="dropdown-item" href="viewClothing.php?type=Short">Bottom: Shorts</a>
                    <a class="dropdown-item" href="viewClothing.php?type=Long">Bottom: Long Pants</a>
                                        <a class="dropdown-item" href="#">Link 3</a>
                </div>
            </li>-->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle nav_link_a" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Men's
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Top</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="viewClothing.php?type=Shirt">Shirt</a></li>
                            <li><a class="dropdown-item" href="viewClothing.php?type=T-shirt">T-shirt</a></li>
                            <li><a class="dropdown-item" href="viewClothing.php?type=Outerwear">Outerwear</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Bottom</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="viewClothing.php?type=Short">Shorts</a></li>
                            <li><a class="dropdown-item" href="viewClothing.php?type=Long_Pants">Long Pants</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <?php
            if (isset($_SESSION['role'])) {
                if ($_SESSION['role'] == 'admin') {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">Admin</a>
                    </li>
                    <!--                    <li class="nav-item">
                                            <a class="nav-link" href="admin_member.php">Admin_Member</a>
                                        </li>-->
                    <?php
                }
            }
            ?>
            <li class="nav-item">
                <a class="nav-link" href="aboutUs.php">About Us</a>
            </li>

    
            <?php if(isset($_SESSION['role'])) { ?>
            <li class="nav-item">
                <a class="nav-link" href="order.php">
                    View Order status

                    <?php if ($orderMemberCount > 9) { ?>
                        <span class="badge badge-pill badge-danger">9+</span>
                    <?php } else { ?>
                        <span class="badge badge-pill badge-danger"><?php echo $orderMemberCount; ?></span>
                    <?php } ?>

                </a>

            </li>
            <?php } ?>


        </ul>

        <?php if (isset($_SESSION['loggedIn'])) { ?>
            <?php if (($_SESSION['loggedIn']) == true) { ?>
                <?php if (($_SESSION['role']) == 'Member') { ?>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">
                             View Cart
                                <?php if ($cartCount > 9) { ?>
                                    <span class="badge badge-pill badge-danger">9+</span>
                                <?php } else { ?>
                                    <span class="badge badge-pill badge-danger"><?php echo $cartCount; ?></span>
                                <?php } ?>

                            </a>

                        </li>
                        <li class="nav-item">
            <!--                            <span class="navbar-brand mb-0 h4"><?php echo $_SESSION['member_name'] ?></span>-->
                            <a href="viewMemberPage.php" class="nav-link"><?php echo $_SESSION['member_name'] ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-danger"  role='button' href="logout.php">Logout</a>
                        </li>
                    </ul>
                <?php } else if (($_SESSION['role']) == 'admin') { ?>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">


                        </li>
                        <li class="nav-item">
                            <span class="navbar-brand mb-0 h4"><?php echo $_SESSION['member_name'] ?></span>
                                                   <!--<a href="#" class="nav-link"><?php echo $_SESSION['member_name'] ?></a>-->
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-danger"  role='button' href="logout.php">Logout</a>
                        </li>
                    </ul>
                <?php
                }
            } else {
                ?>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
    
                <?php
            }
        }
        ?>

    </div>
</nav>
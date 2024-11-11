<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    $role = "member";
    include 'dbinfo.php';
    $conn = new mysqli($config['servername'], $config['username'],
            $config['password'], $config['dbname']);
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
//    $success = false;
    } else {
        $stmt = $conn->prepare("SELECT * FROM member ");
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $member[] = $row;
            }
        }
        $stmt->close();
    }
    $conn->close();
    print_r($member);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include 'head.inc.php' ?>
    </head>
    <body>
        <?php include 'nav.inc.php' ?>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Member Name: </th>
                        <th>Member Email: </th>
                        <th>Member Role: </th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < count($member); $i++) { ?>
                        <tr>

                            <th><?php echo $member[$i]['fname'].' '.$member[$i]['lname'] ?></th>
                            <th><?php echo $member[$i]['email'] ?></th>
                            <th><?php echo $member[$i]['role']?></th>
                            <th><button class="btn btn-primary btn-permission" id="<?php echo $member[$i]['member_id'];?>" data-role-id="<?php echo $member[$i]['role']?>">Change</button></th>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php include'footer.inc.php' ?>
    </body>
</html>

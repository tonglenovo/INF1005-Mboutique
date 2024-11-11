<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$clothings = [];
include 'dbinfo.php';
$conn = new mysqli($config['servername'], $config['username'],
        $config['password'], $config['dbname']);

if ($conn->connect_error) {
    $errorMsg = "Connection failed: " . $conn->connect_error;
//    $success = false;
} else {
    // Prepare the statement: 
    $stmt = $conn->prepare("SELECT * FROM clothing");
    // Bind & execute the query statement: 
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $clothings[] = $row;
        }
    }
    $stmt->close();
    $stmt1 = $conn->prepare("SELECT * FROM size");
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    if ($result1->num_rows > 0) {
        while ($row1 = $result1->fetch_assoc()) {
            $sizes[] = $row1;
        }
    }
    $stmt1->close();
    $stmt2 = $conn->prepare("SELECT * FROM color");
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    if ($result2->num_rows > 0) {
        while ($row2 = $result2->fetch_assoc()) {
            $colors[] = $row2;
        }
    }


    $stmt2->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'head.inc.php' ?>
    </head>
    <body>
        <?php include 'nav.inc.php' ?>
        <main>
            <?php if (isset($_SESSION['member_name'])) { ?>
                <div class="container text-center">
                    <h1>Welcome, <?php echo 'Administrator ' . $_SESSION['member_name'] ?></h1>
                    <h2>Admin Page</h2>
                </div>

                <div class="container mb-3">
                    <!-- Button trigger modal -->
                    <button class="btn btn-success mr-3" data-toggle="modal" data-target="#createModel">
                        <span class="fas fa-plus"></span> Create new Clothing
                    </button>
                    <button class="btn btn-success mr-3" data-toggle="modal" data-target="#createColorModel">
                        <span class="fas fa-plus"></span> Create new Color
                    </button>
                    <button class="btn btn-primary btn-viewColor" data-toggle="modal" data-target="#viewColorModel">
                        <span class="fa fa-eye"></span> View Color
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Clothing Name</th>
                                <th>Clothing Picture</th>
                                <th>Clothing Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < count($clothings); $i++) { ?>
                                <tr>
                                    <th><?php echo $clothings[$i]['clothing_title']; ?></th>
                                    <th><img src="images/<?php echo $clothings[$i]['clothing_image'] ?>" alt="<?php echo $clothings[$i]['clothing_image'] ?>" style="width: 25%;"></th>
                                    <th>$<?php echo number_format((float) $clothings[$i]['clothing_price'], 2, '.', '') ?></th>
                                    <th><button class="btn btn-primary btn-edit" data-clothing-id="<?php echo $clothings[$i]['clothing_id'] ?>" data-toggle="modal" data-target="#editModel"><span class="fas fa-pencil-alt"></span>Edit</button>  <button class="btn btn-danger btn-delete" id="<?php echo $clothings[$i]['clothing_id'] ?>" data-toggle="modal" data-target="#deleteModel"><span class="fas fa-trash-alt"></span>Delete</button></th>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="createModel" tabindex="-1" role="dialog" aria-labelledby="createClothingModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="createClothingModalLabel">Create new Clothing</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="c_name">Clothes Type:</label><br>
                                    <!--<input class="form-control" type="text" id="c_name" name="c_name" placeholder="Clothes Name">-->
                                    <select id="c_type" class="form-control" required>
                                        <option value="">Select Clothing Type</option>
                                        <option value="Shirt">Shirt</option>
                                        <option value="T-shirt">T-shirt</option>
                                        <option value="Outerwear">Outerwear</option>
                                        <option value="Short">Short</option>
                                        <option value="Long">Long</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="c_name">Clothes Name:</label>
                                    <input class="form-control" type="text" id="c_name" name="c_name" placeholder="Clothes Name">
                                </div>
                                <div class="form-group">
                                    <label for="c_desc">Clothes Description:</label>
                                    <input class="form-control" type="text" id="c_desc" name="c_desc" placeholder="Clothes Description">
                                </div>
                                <!--                            <div class="form-group">
                                                                <label for="c_size">Clothes Size:</label>
                                                                <input class="form-control" type="text" id="c_size" name="c_size" placeholder="Clothes Size">
                                                            </div>-->
                                <div class="form-group">
                                    <div id="checkbox-container-size">
                                        <label>Clothes Size:</label><br>
                                        <?php for ($i = 0; $i < count($sizes); $i++) { ?>
                                            <input type="checkbox" name="c_size[]" id="size_<?php echo $sizes[$i]['size_id'] ?>" value="<?php echo $sizes[$i]['size_id'] ?>">
                                            <label for="size_<?php echo $sizes[$i]['size_id'] ?>" style="display: inline-block; margin-right: 10px; vertical-align: middle;"><?php echo $sizes[$i]['size_name'] ?></label>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div id="checkbox-container-color">
                                        <label>Clothes Color:</label><br>
                                        <?php for ($i = 0; $i < count($colors); $i++) { ?>
                                            <input type="checkbox" name="c_color[]" id="color_<?php echo $colors[$i]['color_id'] ?>" value="<?php echo $colors[$i]['color_id'] ?>">
                                            <label for="color_<?php echo $colors[$i]['color_id'] ?>" style="display: inline-block; margin-right: 10px; vertical-align: middle;"><?php echo $colors[$i]['color_name'] ?></label>
                                        <?php } ?>
                                    </div>
                                </div>


                                <!--                                <div class="form-group">
                                                                    <label for="c_color">Clothes Color:</label>
                                                                    <input class="form-control" type="text" id="c_color" name="c_color" placeholder="red,green,blue">
                                                                </div>-->
                                <div class="form-group">
                                    <label for="c_price">Clothes Price:</label>
                                    <input class="form-control" type="text" id="c_price" name="c_price" placeholder="Clothes Price">
                                </div>
                                <div class="form-group">
                                    <label for="c_image">Clothes Image:</label>
                                    <input class="form-control" type="file" id="c_image" name="c_image">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-success" id="add_clothes_btn"><span class="fas fa-plus"></span>Add new clothes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="editModel" tabindex="-1" role="dialog" aria-labelledby="edidModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="edidModalLabel">Edit Clothing</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type ="hidden" id="e_id" >
                                <input type ="hidden" id="e_image_hidden" >
                                <label for="c_name">Clothes Type:</label><br>
                                    <!--<input class="form-control" type="text" id="c_name" name="c_name" placeholder="Clothes Name">-->
                                <select id="e_type" class="form-control">
                                    <option>Select Clothing Type</option>
                                    <option value="Shirt">Shirt</option>
                                    <option value="T-shirt">T-shirt</option>
                                    <option value="Outerwear">Outerwear</option>
                                    <option value="Short">Short</option>
                                    <option value="Long">Long</option>
                                </select>
                                <div class="form-group">
                                    <label for="e_name">Clothes Name:</label>
                                    <input class="form-control" type="text" id="e_name" 
                                           name="c_name" placeholder="Clothes Name" >
                                </div>
                                <div class="form-group">
                                    <label for="e_desc">Clothes Description:</label>
                                    <input class="form-control" type="text" id="e_desc" name="e_desc" placeholder="Clothes Description">
                                </div>
                                <!--                                <div class="form-group">
                                                                    <label for="e_size">Clothes Size:</label>
                                                                    <input class="form-control" type="text" id="e_size" name="e_size" placeholder="Clothes Size">
                                                                </div>-->

                                <div class="form-group">
                                    <label>Clothes Size:</label>
                                    <div id="edit_size_div">
                                        <?php for ($i = 0; $i < count($sizes); $i++) { ?>
                                            <input type="checkbox" name="e_size[]" id="e_size_<?php echo $sizes[$i]['size_id'] ?>" value="<?php echo $sizes[$i]['size_id'] ?>">
                                            <label for="e_size_<?php echo $sizes[$i]['size_id'] ?>" style="display: inline-block; margin-right: 10px; vertical-align: middle;"><?php echo $sizes[$i]['size_name'] ?></label>
                                        <?php } ?>
                                    </div>
                                    <div id="hidden-inputs-container">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Clothes Color:</label>
                                    <div id="edit_color_div">
                                        <?php for ($i = 0; $i < count($colors); $i++) { ?>
                                            <input type="checkbox" name="e_color[]" id="e_color_<?php echo $colors[$i]['color_id'] ?>" value="<?php echo $colors[$i]['color_id'] ?>">
                                            <label for="e_color_<?php echo $colors[$i]['color_id'] ?>" style="display: inline-block; margin-right: 10px; vertical-align: middle;"><?php echo $colors[$i]['color_name'] ?></label>
                                        <?php } ?>
                                    </div>
                                    <div id="hidden-color-container">

                                    </div>
                                </div>
                                <!--                                <div class="form-group">
                                                                    <label for="e_color">Clothes Color:</label>
                                                                    <input class="form-control" type="text" id="e_color" name="e_color" placeholder="red,green,blue">
                                                                </div>-->
                                <div class="form-group">
                                    <label for="e_price">Clothes Price:</label>
                                    <input class="form-control" type="text" id="e_price" name="e_price" placeholder="Clothes Price">
                                </div>
                                <div class="form-group">
                                    <label for="e_image">Clothes Image:</label>
                                    <input class="form-control" type="file" id="e_image" name="e_image">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="edit_clothes_btn"><span class="fas fa-pencil-alt"></span>Edit clothes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="deleteModel" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Delete Clothing</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type ="hidden" id="d_id" >
                                <input type ="hidden" id="d_img" >
                                <input type ="hidden" id="d_size" >
                                <p>Are you sure you want to delete?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-danger" id="delete_clothes_btn"><span class="fas fa-trash-alt"></span> Yes, Delete it</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal -->
                <div class="modal fade" id="createColorModel" tabindex="-1" role="dialog" aria-labelledby="createColorModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="createColorModalLabel">Create Color</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="g_color">Color:</label>
                                        <input class="form-control" type="text" id="g_color" name="g_color" placeholder="Color">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-success" id="add_color_btn"><span class="fas fa-plus"></span>Add new color</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="viewColorModel" tabindex="-1" role="dialog" aria-labelledby="viewColorModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewColorModalLabel">View Color</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="form-group">
                                        <div id="getColor">
                                            <!--                                            <div class="mb-3">Red <button class="btn btn-danger">Delete</button></div>
                                                                                        <div class="mb-3">Blue <button class="btn btn-danger">Delete</button></div>
                                                                                        <div class="mb-3">Green <button class="btn btn-danger">Delete</button></div>
                                                                                        <div class="mb-3">Red <button class="btn btn-danger">Delete</button></div>-->
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="container text-center">
                    <h1>You are not authorize to this page </h1>
                    <h2><a class="btn btn-danger" href="login.php">Back to login</a></h2>
                </div>
            <?php } ?>


        </main>
        <?php include'footer.inc.php' ?>
    </body>
</html>
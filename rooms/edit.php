<?php
session_start();
require_once "../config/db.php";

$id = $_GET['id'];
$room = $conn->query("SELECT * FROM rooms WHERE id = $id")->fetch_assoc();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $room_number = trim($_POST['room_number']);
    $room_type   = trim($_POST['room_type']);
    $price       = trim($_POST['price']);

    // 1. Không cho để trống
    if ($room_number == "" || $room_type == "" || $price == "") {
        $error = "Please fill in all fields.";
    } else {
        // 2. Không cho trùng số phòng (trừ chính nó)
        $check = $conn->query(
            "SELECT id FROM rooms 
             WHERE room_number = '$room_number' AND id != $id"
        );

        if ($check->num_rows > 0) {
            $error = "Room number already exists.";
        } else {
            // 3. Update KHÔNG ĐỘNG status
            $conn->query(
                "UPDATE rooms SET
                    room_number = '$room_number',
                    room_type   = '$room_type',
                    price       = '$price'
                 WHERE id = $id"
            );
            header("Location: list.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Room</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
    <h2 class="page-title">Edit Room</h2>

    <?php if ($error != "") { ?>
        <p style="color:red"><?= $error ?></p>
    <?php } ?>

    <form method="post">
        <div class="form-group">
            <label>Room Number</label>
            <input class="form-control" name="room_number"
                   value="<?= $room['room_number'] ?>" required>
        </div>

        <div class="form-group">
            <label>Room Type</label>
            <input class="form-control" name="room_type"
                   value="<?= $room['room_type'] ?>" required>
        </div>

        <div class="form-group">
            <label>Price</label>
            <input class="form-control" type="number" name="price"
                   value="<?= $room['price'] ?>" required>
        </div>

        <button class="btn btn-primary">Update</button>
    </form>
</div>
</body>
</html>

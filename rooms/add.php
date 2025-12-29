<?php
session_start();
require_once "../config/db.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $room_number = trim($_POST['room_number']);
    $room_type   = trim($_POST['room_type']);
    $price       = trim($_POST['price']);

    // 1. Không cho để trống
    if ($room_number == "" || $room_type == "" || $price == "") {
        $error = "Please fill in all fields.";
    } else {
        // 2. Không cho trùng số phòng
        $check = $conn->query(
            "SELECT id FROM rooms WHERE room_number = '$room_number'"
        );

        if ($check->num_rows > 0) {
            $error = "Room number already exists.";
        } else {
            // 3. Thêm phòng, status mặc định available
            $conn->query(
                "INSERT INTO rooms (room_number, room_type, price, status)
                 VALUES ('$room_number', '$room_type', '$price', 'available')"
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
    <title>Add Room</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
    <h2 class="page-title">Add Room</h2>

    <?php if ($error != "") { ?>
        <p style="color:red"><?= $error ?></p>
    <?php } ?>

    <form method="post">
        <div class="form-group">
            <label>Room Number</label>
            <input class="form-control" name="room_number" required>
        </div>

        <div class="form-group">
            <label>Room Type</label>
            <input class="form-control" name="room_type" required>
        </div>

        <div class="form-group">
            <label>Price</label>
            <input class="form-control" type="number" name="price" required>
        </div>

        <button class="btn btn-primary">Save</button>
    </form>
</div>
</body>
</html>

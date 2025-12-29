<?php
session_start();
require_once "../config/db.php";

$keyword = "";

if (isset($_GET['keyword'])) {
    $keyword = $_GET['keyword'];
    $sql = "SELECT * FROM rooms
            WHERE room_number LIKE '%$keyword%'
               OR room_type   LIKE '%$keyword%'";
} else {
    $sql = "SELECT * FROM rooms";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Room List</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container">
    <h2 class="page-title">Room List</h2>

    <form method="get">
        <div class="form-group">
            <input class="form-control"
                   name="keyword"
                   placeholder="Search by room number or type"
                   value="<?= $keyword ?>">
        </div>
        <button class="btn btn-primary">Search</button>
        <a href="list.php" class="btn">Reset</a>
    </form>

    <a href="add.php" class="btn btn-primary">Add Room</a>

    <table class="table">
        <tr>
            <th>ID</th>
            <th>Room Number</th>
            <th>Room Type</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php if ($result->num_rows > 0) { ?>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['room_number'] ?></td>
                    <td><?= $row['room_type'] ?></td>
                    <td><?= $row['price'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>
                        <a class="btn btn-primary"
                           href="edit.php?id=<?= $row['id'] ?>">Edit</a>

                        <a class="btn btn-danger"
                           href="delete.php?id=<?= $row['id'] ?>"
                           onclick="return confirm('Delete this room?')">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="6">No rooms found</td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>

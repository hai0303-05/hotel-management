<?php
require_once "../config/db.php";

$sql = "SELECT * FROM customers";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Customer List</title>
</head>
<body>

<h2>Customer List</h2>
<a href="add.php">Thêm khách hàng</a>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>ID Card</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['phone']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['id_card']; ?></td>
            <td>
                <a href="edit.php?id=<?php echo $row['id']; ?>">Sửa</a> |
                <a href="delete.php?id=<?php echo $row['id']; ?>" 
                   onclick="return confirm('Bạn chắc chắn muốn xóa không ?')">Xóa</a>
            </td>
        </tr>
    <?php } ?>

</table>

</body>
</html>

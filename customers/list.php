<?php
require_once "../config/db.php";

$sql = "SELECT id, name, phone, email, id_card FROM customers";
$result = $conn->query($sql); // <- CHU Y: $conn
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Danh sach khach hang</title>
</head>
<body>

<h2>Danh sach khach hang</h2>
<a href="add.php">Them khach hang</a>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Ten</th>
        <th>Phone</th>
        <th>Email</th>
        <th>CCCD</th>
        <th>Hanh dong</th>
    </tr>

<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row['id']."</td>";
        echo "<td>".$row['name']."</td>";
        echo "<td>".$row['phone']."</td>";
        echo "<td>".$row['email']."</td>";
        echo "<td>".$row['id_card']."</td>";
        echo "<td>
                <a href='edit.php?id=".$row['id']."'>Sua</a> |
                <a href='delete.php?id=".$row['id']."'>Xoa</a>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>Khong co du lieu</td></tr>";
}
?>

</table>

</body>
</html>

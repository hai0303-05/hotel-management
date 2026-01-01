<?php
require_once "../config/db.php";
$sql = "SELECT id, name, phone, email, id_card FROM customers";
$result = $conn->query($sql); // <- CHU Y: $conn
?>

<!DOCTYPE html>
<html>

$sql = "
SELECT 
    c.*,
    CASE
        WHEN EXISTS (
            SELECT 1 
            FROM bookings b
            WHERE b.customer_id = c.id
              AND b.check_in <= CURDATE()
              AND (b.check_out IS NULL OR b.check_out >= CURDATE())
        ) THEN 'Dang o'

        WHEN EXISTS (
            SELECT 1 
            FROM bookings b
            WHERE b.customer_id = c.id
              AND b.check_in > CURDATE()
        ) THEN 'Dat truoc'

        WHEN EXISTS (
            SELECT 1 
            FROM bookings b
            WHERE b.customer_id = c.id
              AND b.check_out < CURDATE()
        ) THEN 'Tung o'

        ELSE 'Chua tung dat'
    END AS trang_thai
FROM customers c
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sach khach hang</title>
</head>
<body>

<h2>Danh sach khach hang</h2>
<a href="add.php">Them khach hang</a>
<table border="1">

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Ten</th>
        <th>Phone</th>
        <th>Email</th>
        <th>CCCD</th>

        <th>Trang thai</th>

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

?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['phone']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td><?php echo $row['id_card']; ?></td>
        <td><?php echo $row['trang_thai']; ?></td>
        <td>
            <a href="edit.php?id=<?php echo $row['id']; ?>">Sua</a> |
            <a href="delete.php?id=<?php echo $row['id']; ?>"
               onclick="return confirm('Ban co chac muon xoa khach hang nay khong?');">
               Xoa
            </a>
        </td>
    </tr>
<?php
    }
} else {
    echo "<tr><td colspan='7'>Khong co du lieu</td></tr>";
}
?>
</table>

</body>
</html>

<?php
require_once "../config/db.php";

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

<?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['phone'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['id_card'] ?></td>
            <td><?= $row['trang_thai'] ?></td>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>">Sua</a> |
                <a href="delete.php?id=<?= $row['id'] ?>"
                   onclick="return confirm('Xoa khach hang nay?')">Xoa</a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="7">Khong co du lieu</td></tr>
<?php endif; ?>

</table>

</body>
</html>

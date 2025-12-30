<?php
require_once __DIR__ . '/../config/db.php';

/*
 LUU Y:
 - KHONG SUA config/db.php
 - DUNG DUNG TEN BIEN KET NOI (o day la $connection)
*/

$sql = "SELECT id, name, phone, email, id_card FROM customers ORDER BY id DESC";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sach khach hang</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 8px; text-align: center; }
    </style>
</head>
<body>

<h2>Danh sach khach hang</h2>
<a href="add.php">+ Them khach hang</a>

<table>
    <tr>
        <th>ID</th>
        <th>Ten</th>
        <th>Phone</th>
        <th>Email</th>
        <th>CCCD</th>
        <th>Hanh dong</th>
    </tr>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['id_card']) ?></td>
                <td>
                    <a href="edit.php?id=<?= $row['id'] ?>">Sua</a> |
                    <a href="delete.php?id=<?= $row['id'] ?>" 
                       onclick="return confirm('Xoa khach hang nay?')">Xoa</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">Khong co du lieu</td>
        </tr>
    <?php endif; ?>

</table>

</body>
</html>

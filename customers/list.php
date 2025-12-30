<?php
require_once "../config/db.php";

$sql = "SELECT id, name, phone, email, id_card, status FROM customers ORDER BY id DESC";
$result = $conn->query($sql);

function hienTrangThai($status) {
    switch ($status) {
        case 'dat_truoc': return 'Đặt trước';
        case 'dang_dat':  return 'Đang đặt';
        case 'tung_dat':  return 'Từng đặt';
        default: return 'Không xác định';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sach khach hang</title>
</head>
<body>

<h2>Danh sach khach hang</h2>
<a href="add.php">+ Them khach hang</a>

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
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['id_card']; ?></td>
                <td><?php echo hienTrangThai($row['status']); ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $row['id']; ?>">Sua</a> |
                    <a href="delete.php?id=<?php echo $row['id']; ?>"
                       onclick="return confirm('Xoa khach hang nay?');">Xoa</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="7">Chua co du lieu</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>

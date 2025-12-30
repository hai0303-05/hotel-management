<?php
// 1. Tự động dò tìm file db.php trong toàn bộ thư mục dự án
function find_db_file($start_dir) {
    $current_dir = $start_dir;
    // Lùi tối đa 3 cấp thư mục để tìm file config/db.php
    for ($i = 0; $i < 3; $i++) {
        $test_path = $current_dir . '/config/db.php';
        if (file_exists($test_path)) {
            return $test_path;
        }
        $current_dir = dirname($current_dir);
    }
    return null;
}

$db_path = find_db_file(__DIR__);

if ($db_path) {
    require_once $db_path;
} else {
    die("Lỗi: Không thể tìm thấy file config/db.php ở bất cứ đâu xung quanh thư mục customers.");
}

// 2. Kiểm tra biến kết nối từ file của nhóm trưởng
if (!isset($conn)) {
    die("Lỗi: File db.php đã tìm thấy nhưng biến kết nối không phải là \$conn.");
}

// 3. Logic lấy Trạng thái (Đặt trước, Đang đặt, Từng đặt)
$sql = "SELECT c.*, 
        CASE 
            WHEN b.id IS NULL THEN 'Đặt trước'
            WHEN b.check_in IS NOT NULL AND b.check_out IS NULL THEN 'Đang đặt'
            WHEN b.check_in IS NOT NULL AND b.check_out IS NOT NULL THEN 'Từng đặt'
            ELSE 'Đặt trước'
        END as trang_thai
        FROM customers c
        LEFT JOIN (
            /* Lấy trạng thái mới nhất từ bảng bookings của người khác */
            SELECT customer_id, check_in, check_out, id
            FROM bookings 
            WHERE id IN (SELECT MAX(id) FROM bookings GROUP BY customer_id)
        ) b ON c.id = b.customer_id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách khách hàng</title>
    <style>
        table { border-collapse: collapse; width: 100%; font-family: sans-serif; }
        th, td { border: 1px solid #ddd; padding: 10px; }
        .badge { padding: 5px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .bg-pre { background: #e3f2fd; color: #0d47a1; } /* Đặt trước */
        .bg-now { background: #e8f5e9; color: #1b5e20; } /* Đang đặt */
        .bg-old { background: #f5f5f5; color: #616161; } /* Từng đặt */
    </style>
</head>
<body>

<h2>Quản lý khách hàng</h2>
<p><i>Hệ thống tự động cập nhật trạng thái từ Booking</i></p>
<a href="add.php" style="text-decoration:none; background:#007bff; color:white; padding:8px 15px; border-radius:4px;">+ Thêm khách hàng mới</a>
<br><br>

<table>
    <tr style="background:#f4f4f4;">
        <th>ID</th>
        <th>Tên khách hàng</th>
        <th>Số điện thoại</th>
        <th>Trạng thái</th>
        <th>Thao tác</th>
    </tr>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><strong><?= $row['name'] ?></strong><br><small><?= $row['email'] ?></small></td>
                <td><?= $row['phone'] ?></td>
                <td>
                    <?php 
                        $class = 'bg-pre';
                        if ($row['trang_thai'] == 'Đang đặt') $class = 'bg-now';
                        if ($row['trang_thai'] == 'Từng đặt') $class = 'bg-old';
                    ?>
                    <span class="badge <?= $class ?>"><?= $row['trang_thai'] ?></span>
                </td>
                <td>
                    <a href="edit.php?id=<?= $row['id'] ?>">Sửa</a> | 
                    <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Xóa khách này?')">Xóa</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5" align="center">Chưa có khách hàng nào.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
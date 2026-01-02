<?php
require_once "../config/db.php";

/* ===== LAY TU KHOA TIM KIEM (XOÁ KHOANG TRANG THUA) ===== */
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

/* ===== SQL LAY DANH SACH KHACH + TRANG THAI ===== */
$sql = "
SELECT 
    c.id,
    c.name,
    c.phone,
    c.email,
    c.id_card,

    CASE
        -- Dang luu tru: da check-in, chua check-out
        WHEN EXISTS (
            SELECT 1 FROM bookings b
            WHERE b.customer_id = c.id
              AND b.check_in IS NOT NULL
              AND b.check_out IS NULL
        ) THEN 'Dang luu tru'

        -- Dat phong: co booking nhung chua check-in
        WHEN EXISTS (
            SELECT 1 FROM bookings b
            WHERE b.customer_id = c.id
              AND b.check_in IS NULL
        ) THEN 'Dat phong (chua check-in)'

        -- Da tung luu tru: da check-out, hien tai khong luu tru
        WHEN EXISTS (
            SELECT 1 FROM bookings b
            WHERE b.customer_id = c.id
              AND b.check_out IS NOT NULL
        ) THEN 'Da tung luu tru'

        -- Khach moi them tu customer
        ELSE 'Dat phong (chua check-in)'
    END AS trang_thai

FROM customers c
WHERE 1
";

/* ===== TIM KIEM TU DO (GO BAO NHIEU CUNG TIM DUOC) ===== */
if ($keyword !== '') {
    $keyword = $conn->real_escape_string($keyword);
    $sql .= "
        AND (
            c.name LIKE '%$keyword%'
            OR c.phone LIKE '%$keyword%'
            OR c.email LIKE '%$keyword%'
            OR c.id_card LIKE '%$keyword%'
        )
    ";
}

$sql .= " ORDER BY c.id DESC";

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

<form method="get">
    <input
        type="text"
        name="q"
        placeholder="Tim ten / SDT / email / CCCD"
        value="<?= htmlspecialchars($keyword) ?>"
    >
    <button type="submit">Tim</button>
</form>

<br>
<a href="add.php">Them khach hang</a>
<br><br>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Ten</th>
        <th>SDT</th>
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
            <a href="edit.php?id=<?= $row['id'] ?>">Sua</a>
            <?php if ($row['trang_thai'] === 'Dang luu tru'): ?>
                | <span>Khong the xoa</span>
            <?php else: ?>
                | <a href="delete.php?id=<?= $row['id'] ?>"
                     onclick="return confirm('Ban co chac muon xoa khach hang nay?')">
                     Xoa
                  </a>
            <?php endif; ?>
        </td>
    </tr>
<?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="7">Khong co du lieu</td>
    </tr>
<?php endif; ?>

</table>

</body>
</html>

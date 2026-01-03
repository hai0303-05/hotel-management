<?php
if (!defined('IN_INDEX')) die('Access denied');

require_once 'config/db.php';

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$delete_error = '';

/* ===== XOA KHACH HANG ===== */
if (
    isset($_GET['action']) &&
    $_GET['action'] === 'delete' &&
    isset($_GET['id'])
) {
    $id = (int)$_GET['id'];

    $check = $conn->query("
        SELECT COUNT(*) AS total
        FROM bookings
        WHERE customer_id = $id
    ");
    $rowCheck = $check->fetch_assoc();

    if ($rowCheck['total'] > 0) {
        $delete_error = "Không thể xóa khách hàng vì vẫn còn booking";
    } else {
        $conn->query("DELETE FROM customers WHERE id = $id");
        header("Location: index.php?page=customers");
        exit;
    }
}

/* ===== SQL ===== */
$sql = "
SELECT 
    c.id,
    c.name,
    c.phone,
    c.email,
    c.id_card,

    (
        SELECT COUNT(*) 
        FROM bookings b 
        WHERE b.customer_id = c.id
    ) AS booking_count,

    (
        CASE
            WHEN EXISTS (
                SELECT 1
                FROM bookings b
                WHERE b.customer_id = c.id
                  AND b.check_out IS NULL
            ) THEN 'Đang ở'
            ELSE 'Đã từng ở'
        END
    ) AS trang_thai

FROM customers c
WHERE 1
";

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

<div class="customers-page">

<h2>Danh sách khách hàng</h2>

<?php if ($delete_error): ?>
    <p style="color:red; font-weight:bold">
        <?php echo $delete_error; ?>
    </p>
<?php endif; ?>

<form method="get">
    <input type="hidden" name="page" value="customers">
    <input
        type="text"
        name="q"
        placeholder="Tên / SĐT / Email / CCCD"
        value="<?php echo htmlspecialchars($keyword); ?>"
    >
    <button type="submit">Tìm</button>
</form>

<a href="index.php?page=customers_add">+ Thêm khách hàng</a>

<br><br>

<table width="100%">
    <tr>
        <th>STT</th>
        <th>Tên</th>
        <th>SĐT</th>
        <th>Email</th>
        <th>CCCD</th>
        <th>Trạng thái</th>
        <th>Hành động</th>
    </tr>

<?php
$stt = 1;
if ($result && $result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
?>
<tr>
    <td><?php echo $stt++; ?></td>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['phone']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td><?php echo $row['id_card']; ?></td>

    <!-- TRANG THAI -->
    <td>
        <?php
        $statusClass = 'status-old';
        if ($row['trang_thai'] === 'Đang ở') {
            $statusClass = 'status-stay';
        }
        ?>
        <span class="status <?php echo $statusClass; ?>">
            <?php echo $row['trang_thai']; ?>
        </span>
    </td>

    <!-- HANH DONG -->
    <td class="action">
        <a
            href="index.php?page=customers_edit&id=<?php echo $row['id']; ?>"
            class="edit"
        >
            Sửa
        </a>

        <?php if ($row['booking_count'] > 0): ?>
            <span>Không thể xóa</span>
        <?php else: ?>
            <a
                href="index.php?page=customers&action=delete&id=<?php echo $row['id']; ?>"
                class="delete"
                onclick="return confirm('Bạn có chắc muốn xóa khách hàng này?')"
            >
                Xóa
            </a>
        <?php endif; ?>
    </td>
</tr>

<?php
    endwhile;
else:
?>
<tr>
    <td colspan="7">Không có dữ liệu</td>
</tr>
<?php endif; ?>
</table>

</div>

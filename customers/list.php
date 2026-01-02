<?php
if (!defined('IN_INDEX')) die('Access denied');

require_once 'config/db.php';

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

$delete_error = '';

if (
    isset($_GET['action']) &&
    $_GET['action'] === 'delete' &&
    isset($_GET['id'])
) {
    $id = (int)$_GET['id'];

    // Check con booking hay khong
    $check = $conn->query("
        SELECT COUNT(*) AS total
        FROM bookings
        WHERE customer_id = $id
    ");
    $rowCheck = $check->fetch_assoc();

    if ($rowCheck['total'] > 0) {
        $delete_error = "Khong the xoa khach hang vi van con booking";
    } else {
        $conn->query("DELETE FROM customers WHERE id = $id");
        header("Location: index.php?page=customers");
        exit;
    }
}

/* ===== SQL LAY DANH SACH KHACH + TRANG THAI TU BOOKING ===== */
$sql = "
SELECT
    c.id,
    c.name,
    c.phone,
    c.email,
    c.id_card,

    CASE
        -- Dang o: hom nay nam trong khoang check-in -> check-out
        WHEN EXISTS (
            SELECT 1 FROM bookings b
            WHERE b.customer_id = c.id
              AND b.check_in <= CURRENT_DATE
              AND b.check_out >= CURRENT_DATE
        ) THEN 'Dang o'

        -- Dat truoc: check-in trong tuong lai
        WHEN EXISTS (
            SELECT 1 FROM bookings b
            WHERE b.customer_id = c.id
              AND b.check_in > CURRENT_DATE
        ) THEN 'Dat truoc'

        -- Da tung o: da check-out truoc hom nay
        WHEN EXISTS (
            SELECT 1 FROM bookings b
            WHERE b.customer_id = c.id
              AND b.check_out < CURRENT_DATE
        ) THEN 'Da tung o'

        -- Khong co booking
        ELSE 'Dat truoc'
    END AS trang_thai,

    -- Dem so booking (de chan xoa)
    (
        SELECT COUNT(*)
        FROM bookings b
        WHERE b.customer_id = c.id
    ) AS booking_count

FROM customers c
WHERE 1
";

/* ===== THEM DIEU KIEN TIM KIEM ===== */
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

<h2>Danh sach khach hang</h2>

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
        placeholder="Tim ten / SDT / email / CCCD"
        value="<?php echo htmlspecialchars($keyword); ?>"
    >
    <button type="submit">Tim</button>
</form>

<br>

<a href="index.php?page=customers_add">+ Them khach hang</a>

<br><br>

<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <tr>
        <th>STT</th>
        <th>Ten</th>
        <th>SDT</th>
        <th>Email</th>
        <th>CCCD</th>
        <th>Trang thai</th>
        <th>Hanh dong</th>
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
    <td><?php echo $row['trang_thai']; ?></td>
    <td>
        <a href="index.php?page=customers_edit&id=<?php echo $row['id']; ?>">Sua</a>
        |
        <?php if ($row['booking_count'] > 0): ?>
            <span>Khong the xoa</span>
        <?php else: ?>
            <a
                href="index.php?page=customers&action=delete&id=<?php echo $row['id']; ?>"
                onclick="return confirm('Ban co chac muon xoa khach hang nay?')"
            >Xoa</a>
        <?php endif; ?>
    </td>
</tr>
<?php
    endwhile;
else:
?>
<tr>
    <td colspan="7">Khong co du lieu</td>
</tr>
<?php endif; ?>
</table>

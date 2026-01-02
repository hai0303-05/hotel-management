<?php
if (!defined('IN_INDEX')) die('Access denied');
require_once 'config/db.php';

/*
|===========================================================
| 1. TÌM KHÁCH THEO SĐT (PHP THUẦN – SUBMIT FORM)
|===========================================================
*/
$phone = $_GET['phone'] ?? '';
$customers = [];

if (strlen($phone) >= 3) {
    $stmt = $conn->prepare("
        SELECT id, name, phone, id_card
        FROM customers
        WHERE phone LIKE ?
        LIMIT 5
    ");
    $like = "%$phone%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $customers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/*
|===========================================================
| 2. LẤY BOOKING KHI CHỌN KHÁCH
|===========================================================
*/
$booking = null;

if (isset($_GET['customer_id'])) {
    $customer_id = (int)$_GET['customer_id'];

    $stmt = $conn->prepare("
        SELECT
            b.id AS booking_id,
            c.name,
            c.id_card,
            r.room_number,
            r.room_type,
            r.id AS room_id,
            b.check_in,
            COALESCE(b.check_out, CURDATE()) AS check_out,
            GREATEST(
                DATEDIFF(COALESCE(b.check_out, CURDATE()), b.check_in),
                1
            ) AS days,
            GREATEST(
                DATEDIFF(COALESCE(b.check_out, CURDATE()), b.check_in),
                1
            ) * r.price AS total_price
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        JOIN customers c ON b.customer_id = c.id
        WHERE b.customer_id = ?
          AND r.status = 'booked'
        LIMIT 1
    ");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
}
?>

<h2>TRẢ PHÒNG</h2>

<!-- ================= FORM TÌM KHÁCH ================= -->
<form method="get">
    <input type="hidden" name="page" value="bookings_checkout">

    <label>Số điện thoại</label><br>
    <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>">
    <button type="submit">Tìm</button>
</form>

<!-- ================= DANH SÁCH KHÁCH ================= -->
<?php if (!empty($customers)): ?>
    <ul>
        <?php foreach ($customers as $c): ?>
            <li>
                <a href="index.php?page=bookings_checkout&phone=<?= urlencode($phone) ?>&customer_id=<?= $c['id'] ?>">
                    <?= htmlspecialchars($c['phone']) ?> - <?= htmlspecialchars($c['name']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<hr>

<!-- ================= HIỂN THỊ BOOKING ================= -->
<?php if ($booking): ?>
    <p><b>Khách hàng:</b> <?= htmlspecialchars($booking['name']) ?></p>
    <p><b>CCCD:</b> <?= htmlspecialchars($booking['id_card']) ?></p>
    <p><b>Phòng:</b> <?= $booking['room_number'] ?> - <?= $booking['room_type'] ?></p>
    <p><b>Ngày nhận:</b> <?= $booking['check_in'] ?></p>
    <p><b>Ngày trả:</b> <?= $booking['check_out'] ?></p>
    <p><b>Số ngày:</b> <?= $booking['days'] ?></p>
    <p><b>Tổng tiền:</b> <?= number_format($booking['total_price']) ?> VND</p>

    <!-- ===== CHUYỂN SANG HÓA ĐƠN ===== -->
    <form method="get" action="index.php">
        <input type="hidden" name="page" value="bookings">
        <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
        <button type="submit">XÁC NHẬN TRẢ PHÒNG</button>
    </form>

<?php elseif (isset($_GET['customer_id'])): ?>
    <p>Khách này không có phòng đang ở.</p>
<?php endif; ?>

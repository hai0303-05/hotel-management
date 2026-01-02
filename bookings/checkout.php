<?php
if (!defined('IN_INDEX')) die('Access denied');
require_once 'config/db.php';

/*
|===========================================================
| 1. TÌM KHÁCH THEO SĐT
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
| 2. LẤY BOOKING
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
            GREATEST(DATEDIFF(COALESCE(b.check_out, CURDATE()), b.check_in),1) AS days,
            GREATEST(DATEDIFF(COALESCE(b.check_out, CURDATE()), b.check_in),1) * r.price AS total_price
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

<!-- ================= GIAO DIỆN ================= -->
<div class="booking-wrapper">

    <h1 class="booking-title">TRẢ PHÒNG</h1>

    <!-- ================= TÌM KHÁCH ================= -->
    <form method="get" class="checkout-search">
        <input type="hidden" name="page" value="bookings_checkout">

        <div class="search-input">
            <input
                type="text"
                name="phone"
                placeholder="Nhập số điện thoại (≥ 3 số)"
                value="<?= htmlspecialchars($phone) ?>"
            >
        </div>

        <button type="submit" class="booking-btn primary">
            Tìm
        </button>
    </form>

    <!-- ================= DANH SÁCH KHÁCH ================= -->
    <?php if (!empty($customers)): ?>
        <ul class="search-results">
            <?php foreach ($customers as $c): ?>
                <li>
                    <a href="index.php?page=bookings_checkout&phone=<?= urlencode($phone) ?>&customer_id=<?= $c['id'] ?>">
                        <?= htmlspecialchars($c['phone']) ?> – <?= htmlspecialchars($c['name']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- ================= HÓA ĐƠN ================= -->
    <?php if ($booking): ?>

        <div class="invoice-box">

            <p><strong>Khách hàng:</strong> <?= htmlspecialchars($booking['name']) ?></p>
            <p><strong>CCCD:</strong> <?= htmlspecialchars($booking['id_card']) ?></p>

            <p><strong>Phòng:</strong> <?= $booking['room_number'] ?> – <?= $booking['room_type'] ?></p>
            <p><strong>Ngày nhận:</strong> <?= $booking['check_in'] ?></p>
            <p><strong>Ngày trả:</strong> <?= $booking['check_out'] ?></p>
            <p><strong>Số ngày:</strong> <?= $booking['days'] ?></p>

            <h3 class="total-price">
                Tổng tiền: <?= number_format($booking['total_price']) ?> VND
            </h3>

            <form method="get" action="index.php">
                <input type="hidden" name="page" value="bookings">
                <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                <button type="submit" class="booking-btn danger">
                    XÁC NHẬN TRẢ PHÒNG
                </button>
            </form>

        </div>

    <?php elseif (isset($_GET['customer_id'])): ?>
        <p class="empty-text">Khách này không có phòng đang ở.</p>
    <?php endif; ?>

</div>

<?php
require_once "../config/db.php";

/* =========================
   XỬ LÝ XÁC NHẬN CHECK OUT (POST)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $booking_id = (int)($_POST['booking_id'] ?? 0);
    $room_id    = (int)($_POST['room_id'] ?? 0);

    if ($booking_id <= 0 || $room_id <= 0) {
        die("Thiếu dữ liệu check out");
    }

    $conn->begin_transaction();

    try {
        // 1. XÓA BOOKING
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();

        // 2. TRẢ PHÒNG
        $stmt = $conn->prepare("
            UPDATE rooms 
            SET status = 'available'
            WHERE id = ?
        ");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();

        $conn->commit();

        echo "<script>
            alert('Trả phòng thành công');
            window.location.href = 'checkout.php';
        </script>";
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        die('Lỗi check out: ' . $e->getMessage());
    }
}

/* =========================
   HIỂN THỊ HÓA ĐƠN (GET)
========================= */
$booking_id = (int)($_GET['booking_id'] ?? 0);
if ($booking_id <= 0) {
    die("Truy cập không hợp lệ");
}

$stmt = $conn->prepare("
    SELECT
        b.id AS booking_id,
        r.id AS room_id,
        c.name,
        c.phone,
        c.id_card,
        r.room_number,
        r.room_type,
        r.price,
        b.check_in,
        COALESCE(b.check_out, CURDATE()) AS check_out,
        GREATEST(DATEDIFF(COALESCE(b.check_out, CURDATE()), b.check_in), 1) AS days,
        b.total_price
    FROM bookings b
    JOIN customers c ON b.customer_id = c.id
    JOIN rooms r ON b.room_id = r.id
    WHERE b.id = ?
");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Không tìm thấy hóa đơn");
}

/* QR DEMO */
$qr_content = "Thanh toan phong {$data['room_number']} - {$data['total_price']} VND";
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qr_content);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Hóa đơn thanh toán</title>
</head>
<body>

<h2>HÓA ĐƠN THANH TOÁN</h2>
<hr>

<p><strong>Khách hàng:</strong> <?= htmlspecialchars($data['name']) ?></p>
<p><strong>SĐT:</strong> <?= htmlspecialchars($data['phone']) ?></p>
<p><strong>CCCD:</strong> <?= htmlspecialchars($data['id_card']) ?></p>

<hr>

<p><strong>Phòng:</strong> <?= $data['room_number'] ?> - <?= $data['room_type'] ?></p>
<p><strong>Ngày nhận:</strong> <?= $data['check_in'] ?></p>
<p><strong>Ngày trả:</strong> <?= $data['check_out'] ?></p>
<p><strong>Số ngày:</strong> <?= $data['days'] ?></p>

<hr>

<h3>TỔNG TIỀN: <?= number_format($data['total_price']) ?> VND</h3>

<hr>

<h4>Quét mã QR để thanh toán</h4>
<img src="<?= $qr_url ?>" alt="QR thanh toán">

<hr>

<form method="post">
    <input type="hidden" name="booking_id" value="<?= $data['booking_id'] ?>">
    <input type="hidden" name="room_id" value="<?= $data['room_id'] ?>">
    <button type="submit">XÁC NHẬN THANH TOÁN & CHECK OUT</button>
</form>

<br>
<button onclick="window.print()">In hóa đơn</button>

</body>
</html>

<?php
if (!defined('IN_INDEX')) die('Access denied');

require_once 'config/db.php';

/* =========================
   XỬ LÝ ĐẶT PHÒNG
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name      = trim($_POST['name']);
    $phone     = trim($_POST['phone']);
    $id_card   = trim($_POST['id_card']);
    $room_id   = (int)$_POST['room_id'];
    $check_in  = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    /* 1. Thêm khách hàng */
    $stmt = $conn->prepare("
        INSERT INTO customers (name, phone, id_card)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("sss", $name, $phone, $id_card);
    $stmt->execute();
    $customer_id = $conn->insert_id;

    /* 2. Lấy giá phòng */
    $stmt = $conn->prepare("SELECT price FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $room = $stmt->get_result()->fetch_assoc();

    if (!$room) {
        die('Phòng không tồn tại');
    }

    $price = $room['price'];

    /* 3. Tính số ngày */
    $days = (strtotime($check_out) - strtotime($check_in)) / 86400;
    if ($days < 1) $days = 1;

    $total_price = $days * $price;

    /* 4. Thêm booking */
    $stmt = $conn->prepare("
        INSERT INTO bookings (room_id, customer_id, check_in, check_out, total_price)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iissd", $room_id, $customer_id, $check_in, $check_out, $total_price);
    $stmt->execute();

    /* 5. Cập nhật trạng thái phòng */
    $stmt = $conn->prepare("
        UPDATE rooms
        SET status = 'booked'
        WHERE id = ?
    ");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();

    /* 6. Quay về form */
    echo "<script>
        alert('Đặt phòng thành công');
        window.location.href = 'index.php?page=bookings';
    </script>";
    exit;
}

/* =========================
   LẤY PHÒNG TRỐNG
========================= */
$rooms = $conn->query("
    SELECT id, room_number, room_type
    FROM rooms
    WHERE status = 'available'
");
?>

<!-- =========================
     GIAO DIỆN (CÓ CLASS CSS)
========================= -->

<div class="booking-wrapper">

    <h1 class="booking-title">ĐẶT PHÒNG</h1>

    <form method="post" class="booking-form">

        <div class="form-group">
            <label>Tên khách hàng</label>
            <input type="text" name="name" required>
        </div>

        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="text" name="phone" required>
        </div>

        <div class="form-group">
            <label>Căn cước công dân</label>
            <input type="text" name="id_card">
        </div>

        <div class="form-group">
            <label>Chọn phòng</label>
            <select name="room_id" required>
                <option value="">-- Chọn phòng --</option>
                <?php while ($r = $rooms->fetch_assoc()): ?>
                    <option value="<?= $r['id'] ?>">
                        <?= $r['room_number'] ?> - <?= $r['room_type'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Ngày nhận phòng</label>
            <input type="date" name="check_in" required>
        </div>

        <div class="form-group">
            <label>Ngày trả phòng</label>
            <input type="date" name="check_out" required>
        </div>

        <button type="submit" class="booking-btn">Đặt phòng</button>

    </form>
</div>

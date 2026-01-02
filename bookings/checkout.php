<?php
require_once "../config/db.php";

/* =========================
   SEARCH KHÁCH THEO SĐT
========================= */
if (isset($_GET['ajax']) && $_GET['ajax'] === 'search') {
    $phone = $_GET['phone'] ?? '';

    $stmt = $conn->prepare("
        SELECT id, name, phone, id_card
        FROM customers
        WHERE phone LIKE ?
        LIMIT 5
    ");
    $like = "%$phone%";
    $stmt->bind_param("s", $like);
    $stmt->execute();

    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    exit;
}

/* =========================
   LẤY BOOKING ĐANG Ở (KHÔNG ÂM NGÀY)
========================= */
if (isset($_GET['ajax']) && $_GET['ajax'] === 'detail') {
    $customer_id = (int)$_GET['customer_id'];

    $stmt = $conn->prepare("
        SELECT 
            b.id AS booking_id,
            r.id AS room_id,
            r.room_number,
            r.room_type,
            r.price,
            b.check_in,
            COALESCE(b.check_out, CURDATE()) AS check_out,

            /* SỐ NGÀY TỐI THIỂU = 1 */
            GREATEST(
                DATEDIFF(COALESCE(b.check_out, CURDATE()), b.check_in),
                1
            ) AS days,

            /* TỔNG TIỀN KHÔNG ÂM */
            GREATEST(
                DATEDIFF(COALESCE(b.check_out, CURDATE()), b.check_in),
                1
            ) * r.price AS total_price

        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        WHERE b.customer_id = ?
          AND r.status = 'booked'
        ORDER BY b.check_in DESC
        LIMIT 1
    ");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if (!$data) {
        echo json_encode(["error" => "Khách này không có phòng đang ở"]);
    } else {
        echo json_encode($data);
    }
    exit;
}

/* =========================
   CHECK OUT → CHUYỂN SANG HÓA ĐƠN (list.php)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = (int)$_POST['booking_id'];

    if ($booking_id <= 0) {
        die("Booking không hợp lệ");
    }

    /* CHỈ CHUYỂN TRANG – KHÔNG XÓA BOOKING */
    header("Location: list.php?booking_id=$booking_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Check out</title>
</head>
<body>

<h2>Check out</h2>

<label>Số điện thoại</label><br>
<input type="text" id="phone" onkeyup="searchCustomer()" autocomplete="off">
<div id="list"></div>

<hr>

<form method="post">
    <input type="hidden" name="booking_id" id="booking_id">

    <p>Khách hàng: <span id="name"></span></p>
    <p>CCCD: <span id="id_card"></span></p>
    <p>Phòng: <span id="room"></span></p>
    <p>Ngày nhận: <span id="check_in"></span></p>
    <p>Ngày trả: <span id="check_out"></span></p>
    <p>Số ngày: <span id="days"></span></p>
    <p>Tổng tiền: <strong id="total_price"></strong> VND</p>

    <button type="submit">Check out</button>
</form>

<script>
function searchCustomer() {
    let phone = document.getElementById('phone').value;
    if (phone.length < 3) {
        document.getElementById('list').innerHTML = '';
        return;
    }

    fetch("checkout.php?ajax=search&phone=" + phone)
        .then(r => r.json())
        .then(data => {
            let html = '';
            data.forEach(c => {
                html += `<div onclick="selectCustomer(${c.id}, '${c.name}', '${c.id_card ?? ''}')">
                            ${c.phone} - ${c.name}
                         </div>`;
            });
            document.getElementById('list').innerHTML = html;
        });
}

function selectCustomer(id, name, id_card) {
    document.getElementById('name').innerText = name;
    document.getElementById('id_card').innerText = id_card;
    document.getElementById('list').innerHTML = '';

    fetch("checkout.php?ajax=detail&customer_id=" + id)
        .then(r => r.json())
        .then(d => {
            if (d.error) {
                alert(d.error);
                return;
            }

            document.getElementById('booking_id').value = d.booking_id;

            document.getElementById('room').innerText =
                d.room_number + " - " + d.room_type;

            document.getElementById('check_in').innerText = d.check_in;
            document.getElementById('check_out').innerText = d.check_out;
            document.getElementById('days').innerText = d.days;
            document.getElementById('total_price').innerText = d.total_price;
        });
}
</script>

</body>
</html>

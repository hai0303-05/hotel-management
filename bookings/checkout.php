<?php
// checkout.php
require_once "../config/db.php";

/*
BẢNG LIÊN QUAN
- customers(id, name, phone, email, id_card)
- rooms(id, room_number, room_type, price, status)
- bookings(id, room_id, customer_id, check_in, check_out, total_price)
*/

/* =========================
   API SEARCH KHÁCH THEO SĐT
   ========================= */
if (isset($_GET['ajax']) && $_GET['ajax'] === 'search') {
    $phone = $_GET['phone'] ?? '';

    $sql = "
        SELECT c.id, c.name, c.phone, c.id_card
        FROM customers c
        WHERE c.phone LIKE ?
        LIMIT 5
    ";
    $stmt = $conn->prepare($sql);
    $like = "%$phone%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $rs = $stmt->get_result();

    $data = [];
    while ($row = $rs->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

/* =========================
   API LẤY THÔNG TIN CHECKOUT
   ========================= */
if (isset($_GET['ajax']) && $_GET['ajax'] === 'detail') {
    $customer_id = (int)$_GET['customer_id'];

    $sql = "
        SELECT 
            b.id AS booking_id,
            r.room_number,
            r.room_type,
            r.price,
            b.check_in,
            b.check_out,
            DATEDIFF(COALESCE(b.check_out, CURDATE()), b.check_in) AS days,
            (DATEDIFF(COALESCE(b.check_out, CURDATE()), b.check_in) * r.price) AS total_price
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        WHERE b.customer_id = ?
          AND r.status = 'booked'
        ORDER BY b.check_in DESC
        LIMIT 1
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $rs = $stmt->get_result();

    echo json_encode($rs->fetch_assoc());
    exit;
}

/* =========================
   XỬ LÝ CHECK OUT
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = (int)$_POST['booking_id'];
    $room_number = $_POST['room_number'];

    // cập nhật ngày trả phòng
    $conn->query("UPDATE bookings SET check_out = CURDATE() WHERE id = $booking_id");

    // trả phòng
    $stmt = $conn->prepare("UPDATE rooms SET status='available' WHERE room_number=?");
    $stmt->bind_param("s", $room_number);
    $stmt->execute();

    echo "Check out thành công";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Check out</title>
</head>
<body>

<h2>Check out</h2>

<label>Số điện thoại (3 số đầu)</label><br>
<input type="text" id="phone" onkeyup="searchCustomer()" autocomplete="off">
<div id="list"></div>

<hr>

<form method="post">
    <input type="hidden" name="booking_id" id="booking_id">

    <p>Khách hàng: <span id="name"></span></p>
    <p>CCCD: <span id="id_card"></span></p>
    <p>Phòng: <span id="room_number_text"></span></p>
    <input type="hidden" name="room_number" id="room_number">

    <p>Ngày nhận: <span id="check_in"></span></p>
    <p>Ngày trả: <span id="check_out"></span></p>
    <p>Số ngày: <span id="days"></span></p>
    <p>Tổng tiền: <strong id="total_price"></strong></p>

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
        .then(res => res.json())
        .then(data => {
            let html = '';
            data.forEach(c => {
                html += `<div onclick="selectCustomer(${c.id}, '${c.name}', '${c.phone}', '${c.id_card}')">
                            ${c.phone} - ${c.name}
                         </div>`;
            });
            document.getElementById('list').innerHTML = html;
        });
}

function selectCustomer(id, name, phone, id_card) {
    document.getElementById('list').innerHTML = '';
    document.getElementById('name').innerText = name;
    document.getElementById('id_card').innerText = id_card;

    fetch("checkout.php?ajax=detail&customer_id=" + id)
        .then(res => res.json())
        .then(d => {
            document.getElementById('booking_id').value = d.booking_id;
            document.getElementById('room_number').value = d.room_number;
            document.getElementById('room_number_text').innerText = d.room_number + " - " + d.room_type;
            document.getElementById('check_in').innerText = d.check_in;
            document.getElementById('check_out').innerText = d.check_out ?? 'Hôm nay';
            document.getElementById('days').innerText = d.days;
            document.getElementById('total_price').innerText = d.total_price;
        });
}
</script>

</body>
</html>

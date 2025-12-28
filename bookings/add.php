<?php
require_once "../config/db.php";

/* Lấy phòng trống */
$rooms = $conn->query("SELECT id, room_number, room_type FROM rooms WHERE status='available'");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Đặt phòng</title>
</head>
<body>

<h1>Đặt phòng</h1>

<button onclick="showForm('new')">Khách mới</button>
<button onclick="showForm('old')">Khách đặt trước</button>

<hr>

<form id="bookingForm" method="post" style="display:none">

    <label>Số điện thoại</label><br>
    <input type="text" id="phone" name="phone" onkeyup="searchCustomer()">
    <div id="list"></div><br>

    <label>Tên khách hàng</label><br>
    <input type="text" id="name" name="name"><br><br>

    <label>Căn cước công dân</label><br>
    <input type="text" id="id_card" name="id_card"><br><br>

    <label>Chọn phòng trống</label><br>
    <select name="room_id">
        <option value="">-- Chọn phòng --</option>
        <?php while ($r = $rooms->fetch_assoc()) { ?>
            <option value="<?= $r['id'] ?>">
                <?= $r['room_number'] ?> - <?= $r['room_type'] ?>
            </option>
        <?php } ?>
    </select><br><br>

    <label>Ngày nhận phòng</label><br>
    <input type="date" name="check_in"><br><br>

    <label>Ngày trả phòng</label><br>
    <input type="date" name="check_out"><br><br>

    <button type="submit">Đặt phòng</button>
    <button type="button" onclick="location.reload()">Quay lại</button>

</form>

<script>
let mode = '';

function showForm(type) {
    mode = type;
    document.getElementById('bookingForm').style.display = 'block';
    document.getElementById('list').innerHTML = '';

    if (type === 'new') {
        document.getElementById('phone').value = '';
        document.getElementById('name').value = '';
        document.getElementById('id_card').value = '';
    }
}

function searchCustomer() {
    if (mode !== 'old') return;

    let phone = document.getElementById('phone').value;
    if (phone.length < 3) {
        document.getElementById('list').innerHTML = '';
        return;
    }

    fetch("list.php?phone=" + phone)
        .then(res => res.json())
        .then(data => {
            let html = '';
            data.forEach(c => {
                html += `<div onclick="fillCustomer('${c.name}','${c.phone}','${c.id_card ?? ''}')">
                            ${c.phone} - ${c.name}
                         </div>`;
            });
            document.getElementById('list').innerHTML = html;
        });
}

function fillCustomer(name, phone, id_card) {
    document.getElementById('name').value = name;
    document.getElementById('phone').value = phone;
    document.getElementById('id_card').value = id_card;
    document.getElementById('list').innerHTML = '';
}
</script>

</body>
</html>

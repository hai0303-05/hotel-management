<?php
require_once __DIR__ . '/../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $id_card = $conn->real_escape_string(trim($_POST['id_card']));

    if ($name == '' || $phone == '') {
        $error = 'Tên và số điện thoại bắt buộc';
    } else {
        $sql = "INSERT INTO customers (name, phone, email, id_card)
                VALUES ('$name', '$phone', NULLIF('$email',''), NULLIF('$id_card',''))";
        if ($conn->query($sql)) {
            header('Location: list.php');
            exit();
        } else {
            $error = 'Lỗi: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Thêm khách hàng</title></head>
<body>
    <h2>Thêm khách hàng (Đặt trước)</h2>
    <?php if ($error): ?><p style="color:red"><?= $error ?></p><?php endif; ?>
    <form method="post">
        Tên khách hàng *<br><input type="text" name="name" required><br><br>
        Số điện thoại *<br><input type="text" name="phone" required><br><br>
        Email<br><input type="email" name="email"><br><br>
        CCCD<br><input type="text" name="id_card"><br><br>
        <button type="submit">Lưu</button>
        <a href="list.php">Quay lại</a>
    </form>
</body>
</html>
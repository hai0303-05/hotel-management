<?php
require_once __DIR__ . '/../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $id_card = trim($_POST['id_card']);

    if ($name === '' || $phone === '') {
        $error = 'Ten va so dien thoai la bat buoc';
    } else {
        $stmt = $connection->prepare(
            "INSERT INTO customers (name, phone, email, id_card) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $name, $phone, $email, $id_card);
        $stmt->execute();
        header('Location: list.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Them khach hang</title>
</head>
<body>

<h2>Them khach hang</h2>

<?php if ($error): ?>
    <p style="color:red"><?= $error ?></p>
<?php endif; ?>

<form method="post">
    Ten khach hang *<br>
    <input type="text" name="name"><br><br>

    So dien thoai *<br>
    <input type="text" name="phone"><br><br>

    Email (co the de trong)<br>
    <input type="email" name="email"><br><br>

    CCCD (co the de trong)<br>
    <input type="text" name="id_card"><br><br>

    <button type="submit">Luu</button>
    <a href="list.php">Quay lai</a>
</form>

</body>
</html>

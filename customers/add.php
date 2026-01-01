<?php
require_once "../config/db.php";

$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$id_card = $_POST['id_card'] ?? '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($name === '') $errors[] = "Ten bat buoc";
    if ($phone === '') $errors[] = "SDT bat buoc";

    if (!$errors) {
        $stmt = $conn->prepare("
            INSERT INTO customers (name, phone, email, id_card)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssss", $name, $phone, $email, $id_card);
        $stmt->execute();

        header("Location: list.php");
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

<form method="post">
    Ten*: <input type="text" name="name" value="<?= htmlspecialchars($name) ?>"><br><br>
    SDT*: <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>"><br><br>
    Email: <input type="text" name="email" value="<?= htmlspecialchars($email) ?>"><br><br>
    CCCD: <input type="text" name="id_card" value="<?= htmlspecialchars($id_card) ?>"><br><br>

    <button type="submit">Luu</button>
</form>

<?php foreach ($errors as $e): ?>
    <p style="color:red"><?= $e ?></p>
<?php endforeach; ?>

<br>
<a href="list.php">Quay lai danh sach</a>

</body>
</html>

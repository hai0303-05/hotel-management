<?php
session_start();
require_once '../config/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users 
            WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Luu session dang nhap
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        header("Location: ../index.php");
        exit();
    } else {
        $error = "Sai ten dang nhap hoac mat khau!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dang nhap</title>
</head>
<body>

<h2>Dang nhap he thong</h2>

<?php if ($error != "") { ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php } ?>

<form method="post">
    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Dang nhap</button>
</form>

</body>
</html>

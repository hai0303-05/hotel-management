<?php
require_once '../auth/check_login.php';
checkRole('admin');
require_once '../config/db.php';

$error = '';

/* ===== THEM TAI KHOAN ===== */
if (isset($_POST['add'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    if ($username == '' || $password == '') {
        $error = "Khong duoc de trong username hoac password";
    } else {
        // Kiem tra trung username
        $check = mysqli_query($conn,
            "SELECT id FROM users WHERE username='$username'"
        );

        if (mysqli_num_rows($check) > 0) {
            $error = "Username da ton tai";
        } else {
            $password = md5($password);
            mysqli_query($conn,
                "INSERT INTO users(username, password, role)
                 VALUES('$username', '$password', '$role')"
            );
            header("Location: users.php");
            exit;
        }
    }
}

/* ===== XOA TAI KHOAN ===== */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: users.php");
    exit;
}

/* ===== SUA TAI KHOAN (KEM DOI MAT KHAU) ===== */
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $newpass = trim($_POST['new_password']);

    if ($username == '') {
        $error = "Username khong duoc de trong";
    } else {
        // Neu co nhap mat khau moi
        if ($newpass != '') {
            $newpass = md5($newpass);
            mysqli_query($conn,
                "UPDATE users 
                 SET username='$username', role='$role', password='$newpass'
                 WHERE id=$id"
            );
        } else {
            // Khong doi mat khau
            mysqli_query($conn,
                "UPDATE users 
                 SET username='$username', role='$role'
                 WHERE id=$id"
            );
        }
        header("Location: users.php");
        exit;
    }
}

/* ===== LAY DANH SACH ===== */
$result = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quan ly tai khoan</title>
</head>
<body>

<h2>Quan ly tai khoan nhan vien</h2>

<p>Xin chao Admin: <b><?php echo $_SESSION['username']; ?></b></p>

<?php if ($error != '') { ?>
<p style="color:red;"><?php echo $error; ?></p>
<?php } ?>

<!-- ===== FORM THEM ===== -->
<h3>Them tai khoan</h3>
<form method="post">
    Username:
    <input type="text" name="username">
    Password:
    <input type="password" name="password">
    Role:
    <select name="role">
        <option value="staff">Nhan vien</option>
        <option value="admin">Admin</option>
    </select>
    <button type="submit" name="add">Them</button>
</form>

<hr>

<!-- ===== DANH SACH ===== -->
<h3>Danh sach tai khoan</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
        <th>Mat khau moi</th>
        <th>Hanh dong</th>
    </tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
<form method="post">
    <td><?php echo $row['id']; ?></td>

    <td>
        <input type="text" name="username"
               value="<?php echo $row['username']; ?>">
    </td>

    <td>
        <select name="role">
            <option value="admin"
                <?php if ($row['role']=='admin') echo 'selected'; ?>>
                Admin
            </option>
            <option value="staff"
                <?php if ($row['role']=='staff') echo 'selected'; ?>>
                Nhan vien
            </option>
        </select>
    </td>

    <td>
        <input type="password" name="new_password"
               placeholder="Bo trong neu khong doi">
    </td>

    <td>
        <input type="hidden" name="id"
               value="<?php echo $row['id']; ?>">
        <button type="submit" name="update">Sua</button>

        <?php if ($row['id'] != $_SESSION['user_id']) { ?>
            | <a href="users.php?delete=<?php echo $row['id']; ?>"
                 onclick="return confirm('Xoa tai khoan nay?')">
                 Xoa
              </a>
        <?php } else { ?>
            | <i>(Tai khoan dang dang nhap)</i>
        <?php } ?>
    </td>
</form>
</tr>
<?php } ?>

</table>

<p>
    
    <a href="../index.php">Trang chu</a> |
    
</p>

</body>
</html>

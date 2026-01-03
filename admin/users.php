<?php
if (!defined('IN_INDEX')) die('Access denied');
require_once 'config/db.php';

$error = '';

/* ===== THEM ===== */
if (isset($_POST['add'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    if ($username == '' || $password == '') {
        $error = "Khong duoc de trong username hoac password";
    } else {
        $check = mysqli_query($conn,
            "SELECT id FROM users WHERE username='$username'"
        );

        if (mysqli_num_rows($check) > 0) {
            $error = "Username da ton tai";
        } else {
            $password = md5($password);
            mysqli_query($conn,
                "INSERT INTO users(username,password,role)
                 VALUES('$username','$password','$role')"
            );
            header("Location: index.php?page=admin_users");
            exit;
        }
    }
}

/* ===== XOA ===== */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Khong cho xoa tai khoan dang dang nhap
    if ($id != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    }

    header("Location: index.php?page=admin_users");
    exit;
}

/* ===== SUA ===== */
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $new_password = trim($_POST['new_password']);

    if ($username == '') {
        $error = "Username khong duoc de trong";
    } else {

        // NEU LA TAI KHOAN DANG DANG NHAP -> KHONG DOI ROLE
        if ($id == $_SESSION['user_id']) {

            if ($new_password != '') {
                $new_password = md5($new_password);
                mysqli_query($conn,
                    "UPDATE users
                     SET username='$username', password='$new_password'
                     WHERE id=$id"
                );
            } else {
                mysqli_query($conn,
                    "UPDATE users
                     SET username='$username'
                     WHERE id=$id"
                );
            }

        } else {
            // Tai khoan khac -> doi ca role
            if ($new_password != '') {
                $new_password = md5($new_password);
                mysqli_query($conn,
                    "UPDATE users
                     SET username='$username', role='$role', password='$new_password'
                     WHERE id=$id"
                );
            } else {
                mysqli_query($conn,
                    "UPDATE users
                     SET username='$username', role='$role'
                     WHERE id=$id"
                );
            }
        }

        header("Location: index.php?page=admin_users");
        exit;
    }
}

$result = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quan ly tai khoan</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>

<div class="container">
    <h2 class="page-title">Quản lý tài khoản</h2>

    <p>Xin chào: <b><?php echo $_SESSION['username']; ?></b></p>

    <?php if ($error != '') { ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <h3>Thêm tài khoản</h3>
    <form method="post">
        <div class="form-group">
            <input type="text" name="username" class="form-control" placeholder="Username">
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control" placeholder="Password">
        </div>
        <div class="form-group">
            <select name="role" class="form-control">
                <option value="staff">Nhân viên</option>
                <option value="admin">Quản lý</option>
            </select>
        </div>
        <button type="submit" name="add" class="btn btn-primary">THÊM</button>
    </form>

    <hr>

    <table class="table">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Mật khẩu mới</th>
            <th>Hành động</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
        <form method="post">
            <td><?php echo $row['id']; ?></td>

            <td>
                <input type="text" name="username" class="form-control"
                       value="<?php echo $row['username']; ?>">
            </td>

            <td>
                <?php if ($row['id'] == $_SESSION['user_id']) { ?>
                    <!-- Khong cho doi role tai khoan hien tai -->
                    <input type="text" class="form-control"
                           value="<?php echo $row['role']; ?>" disabled>
                <?php } else { ?>
                    <select name="role" class="form-control">
                        <option value="admin" <?php if ($row['role']=='admin') echo 'selected'; ?>>Quản lý </option>
                        <option value="staff" <?php if ($row['role']=='staff') echo 'selected'; ?>>Nhân viên</option>
                    </select>
                <?php } ?>
            </td>

            <td>
                <input type="password" name="new_password" class="form-control">
            </td>

            <td>
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <button type="submit" name="update" class="btn btn-primary">Sửa</button>

                <?php if ($row['id'] != $_SESSION['user_id']) { ?>
                    <a href="index.php?page=admin_users&delete=<?php echo $row['id']; ?>"
                       class="btn btn-danger"
                       onclick="return confirm('Xóa tài khỏan này?')">
                        Xóa
                    </a>

                <?php } ?>
            </td>
        </form>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>

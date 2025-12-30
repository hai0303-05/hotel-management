<?php
require_once '../auth/check_login.php';
checkRole('admin');
require_once '../config/db.php';

/* ===== THONG KE CO BAN ===== */
$room_count = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM rooms")
)['total'];

$customer_count = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM customers")
)['total'];

$booking_count = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings")
)['total'];

$revenue = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(total_price) AS total FROM bookings")
)['total'];

if ($revenue == null) {
    $revenue = 0;
}

/* ===== LOC DOANH THU THEO THANG NAM ===== */
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year  = isset($_GET['year'])  ? $_GET['year']  : date('Y');

$monthly_revenue = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT SUM(total_price) AS total 
         FROM bookings 
         WHERE MONTH(check_in) = $month 
           AND YEAR(check_in) = $year"
    )
)['total'];

if ($monthly_revenue == null) {
    $monthly_revenue = 0;
}

/* ===== THONG KE DOANH THU TUNG THANG ===== */
$revenue_by_month = mysqli_query(
    $conn,
    "SELECT 
        MONTH(check_in) AS month,
        YEAR(check_in) AS year,
        SUM(total_price) AS total
     FROM bookings
     GROUP BY YEAR(check_in), MONTH(check_in)
     ORDER BY year DESC, month DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Thong ke he thong</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container">
    <h2 class="page-title">Thong ke he thong</h2>

    <p>Xin chao Admin: <b><?php echo $_SESSION['username']; ?></b></p>

    <!-- ===== THONG KE TONG ===== -->
    <table class="table">
        <tr>
            <th>Noi dung</th>
            <th>So lieu</th>
        </tr>
        <tr>
            <td>Tong so phong</td>
            <td><?php echo $room_count; ?></td>
        </tr>
        <tr>
            <td>Tong khach hang</td>
            <td><?php echo $customer_count; ?></td>
        </tr>
        <tr>
            <td>Tong don dat phong</td>
            <td><?php echo $booking_count; ?></td>
        </tr>
        <tr>
            <td><b>Tong doanh thu</b></td>
            <td><b><?php echo number_format($revenue); ?> VND</b></td>
        </tr>
    </table>

    <br>

    <!-- ===== LOC DOANH THU THEO THANG ===== -->
    <h3>Doanh thu theo thang</h3>

    <form method="get">
        Thang:
        <input type="number" name="month" min="1" max="12"
               value="<?php echo $month; ?>" class="form-control">

        Nam:
        <input type="number" name="year"
               value="<?php echo $year; ?>" class="form-control">

        <button type="submit" class="btn btn-primary">Loc</button>
    </form>

    <p>
        <b>Doanh thu thang <?php echo $month . '/' . $year; ?>:</b>
        <?php echo number_format($monthly_revenue); ?> VND
    </p>

    <br>

    <!-- ===== BANG DOANH THU TUNG THANG ===== -->
    <h3>Thong ke doanh thu theo tung thang</h3>

    <table class="table">
        <tr>
            <th>Thang</th>
            <th>Nam</th>
            <th>Doanh thu (VND)</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($revenue_by_month)) { ?>
        <tr>
            <td><?php echo $row['month']; ?></td>
            <td><?php echo $row['year']; ?></td>
            <td><?php echo number_format($row['total']); ?></td>
        </tr>
        <?php } ?>
    </table>

    <p>
        <a href="users.php">Quan ly tai khoan</a> |
        <a href="../index.php">Trang chu</a> |
        <a href="../auth/logout.php">Dang xuat</a>
    </p>
</div>

</body>
</html>

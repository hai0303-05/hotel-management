<?php
if (!defined('IN_INDEX')) die('Access denied');
require_once 'config/db.php';

/* ===== TỔNG QUAN ===== */
$room_count = $conn->query("SELECT COUNT(*) AS total FROM rooms")->fetch_assoc()['total'];
$customer_count = $conn->query("SELECT COUNT(*) AS total FROM customers")->fetch_assoc()['total'];
$booking_count = $conn->query("SELECT COUNT(*) AS total FROM bookings")->fetch_assoc()['total'];

$revenue = $conn->query("SELECT SUM(total_price) AS total FROM bookings")
                ->fetch_assoc()['total'] ?? 0;

/* ===== DOANH THU THEO THÁNG ===== */
$month = $_GET['month'] ?? date('m');
$year  = $_GET['year'] ?? date('Y');

$stmt = $conn->prepare("
    SELECT SUM(total_price) AS total
    FROM bookings
    WHERE MONTH(check_in) = ?
      AND YEAR(check_in) = ?
");
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$monthly_revenue = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

/* ===== DOANH THU THEO THÁNG (BẢNG) ===== */
$revenue_by_month = $conn->query("
    SELECT
        YEAR(check_in) AS year,
        MONTH(check_in) AS month,
        SUM(total_price) AS total
    FROM bookings
    GROUP BY YEAR(check_in), MONTH(check_in)
    ORDER BY year DESC, month DESC
");
?>

<div class="admin-container">

    <h2 class="admin-title">Thống kê hệ thống</h2>

    <!-- ===== DASHBOARD ===== -->
    <div class="admin-dashboard">
        <div class="admin-card">
            <span>Phòng</span>
            <strong><?= $room_count ?></strong>
        </div>
        <div class="admin-card">
            <span>Khách hàng</span>
            <strong><?= $customer_count ?></strong>
        </div>
        <div class="admin-card">
            <span>Đơn đặt phòng</span>
            <strong><?= $booking_count ?></strong>
        </div>
        <div class="admin-card highlight">
            <span>Tổng doanh thu</span>
            <strong><?= number_format($revenue) ?> VND</strong>
        </div>
    </div>

    <!-- ===== FILTER ===== -->
    <form method="get" class="admin-filter">
        <input type="hidden" name="page" value="admin_stats">

        <input type="number" name="month" min="1" max="12"
               value="<?= $month ?>" class="form-control">

        <input type="number" name="year"
               value="<?= $year ?>" class="form-control">

        <button class="btn btn-primary">Lọc</button>
    </form>

    <p class="admin-monthly">
        Doanh thu tháng <b><?= $month ?>/<?= $year ?></b>:
        <b><?= number_format($monthly_revenue) ?> VND</b>
    </p>

    <!-- ===== TABLE ===== -->
    <table class="table admin-table">
        <tr>
            <th>Tháng</th>
            <th>Năm</th>
            <th>Doanh thu (VND)</th>
        </tr>

        <?php while ($row = $revenue_by_month->fetch_assoc()): ?>
        <tr>
            <td><?= $row['month'] ?></td>
            <td><?= $row['year'] ?></td>
            <td><?= number_format($row['total']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>

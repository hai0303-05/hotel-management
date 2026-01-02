<?php
if (!defined('IN_INDEX')) die('Access denied');
require_once 'config/db.php';

/* ===== TỔNG QUAN ===== */
$total_rooms = $conn->query("SELECT COUNT(*) total FROM rooms")->fetch_assoc()['total'];
$total_customers = $conn->query("SELECT COUNT(*) total FROM customers")->fetch_assoc()['total'];
$total_bookings = $conn->query("SELECT COUNT(*) total FROM bookings")->fetch_assoc()['total'];
$total_revenue = $conn->query("SELECT SUM(total_price) total FROM bookings")->fetch_assoc()['total'] ?? 0;

/* ===== THÁNG HIỆN TẠI ===== */
$month = $_GET['month'] ?? date('m');
$year  = $_GET['year']  ?? date('Y');

/* ===== DOANH THU THÁNG ===== */
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) booking_count,
        SUM(total_price) revenue
    FROM bookings
    WHERE MONTH(check_in)=? AND YEAR(check_in)=?
");
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();

$current_revenue = $current['revenue'] ?? 0;
$current_bookings = $current['booking_count'] ?? 0;

/* ===== THÁNG TRƯỚC ===== */
$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month == 0) {
    $prev_month = 12;
    $prev_year--;
}

$stmt->bind_param("ii", $prev_month, $prev_year);
$stmt->execute();
$prev = $stmt->get_result()->fetch_assoc();
$prev_revenue = $prev['revenue'] ?? 0;

/* ===== SO SÁNH ===== */
$growth = $prev_revenue > 0
    ? (($current_revenue - $prev_revenue) / $prev_revenue) * 100
    : 0;

/* ===== BÁO CÁO LỊCH SỬ ===== */
$history = $conn->query("
    SELECT 
        YEAR(check_in) year,
        MONTH(check_in) month,
        COUNT(*) bookings,
        SUM(total_price) revenue
    FROM bookings
    GROUP BY year, month
    ORDER BY year DESC, month DESC
");
?>

<div class="admin-container">

    <h2 class="admin-title">Báo cáo doanh thu</h2>

    <!-- KPI -->
    <div class="admin-dashboard">
        <div class="admin-card">
            <span>Tổng phòng</span>
            <strong><?= $total_rooms ?></strong>
        </div>
        <div class="admin-card">
            <span>Đơn đặt phòng</span>
            <strong><?= $total_bookings ?></strong>
        </div>
        <div class="admin-card highlight">
            <span>Tổng doanh thu</span>
            <strong><?= number_format($total_revenue) ?> VND</strong>
        </div>
        <div class="admin-card <?= $growth>=0?'up':'down' ?>">
            <span>So với tháng trước</span>
            <strong><?= number_format($growth,1) ?>%</strong>
        </div>
    </div>

    <!-- FILTER -->
    <form class="admin-filter" method="get">
        <input type="hidden" name="page" value="admin_stats">
        <input type="number" name="month" min="1" max="12" value="<?= $month ?>">
        <input type="number" name="year" value="<?= $year ?>">
        <button class="btn btn-primary">Lọc</button>
    </form>

    <p class="admin-monthly">
        Doanh thu tháng <b><?= $month ?>/<?= $year ?></b>:
        <b><?= number_format($current_revenue) ?> VND</b>
        — <?= $current_bookings ?> đơn
    </p>

    <!-- TABLE -->
    <table class="table admin-table">
        <tr>
            <th>Tháng</th>
            <th>Năm</th>
            <th>Số đơn</th>
            <th>Doanh thu (VND)</th>
        </tr>
        <?php while ($row = $history->fetch_assoc()): ?>
        <tr>
            <td><?= $row['month'] ?></td>
            <td><?= $row['year'] ?></td>
            <td><?= $row['bookings'] ?></td>
            <td><?= number_format($row['revenue']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>

<?php
if (!defined('IN_INDEX')) die('Access denied');
require_once 'config/db.php';

/* ===== TỔNG QUAN ===== */
$total_rooms = $conn->query("SELECT COUNT(*) total FROM rooms")->fetch_assoc()['total'];

$summary = $conn->query("
    SELECT 
        COUNT(*) total_bookings,
        SUM(total_price) total_revenue
    FROM bookings
    WHERE check_out IS NOT NULL
")->fetch_assoc();

$total_bookings = $summary['total_bookings'] ?? 0;
$total_revenue  = $summary['total_revenue'] ?? 0;

/* ===== THÁNG / NĂM ===== */
$month = $_GET['month'] ?? date('m');
$year  = $_GET['year']  ?? date('Y');

/* ===== DOANH THU THÁNG ===== */
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) booking_count,
        SUM(total_price) revenue
    FROM bookings
    WHERE check_out IS NOT NULL
      AND MONTH(check_out) = ?
      AND YEAR(check_out) = ?
");
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();

$current_bookings = $current['booking_count'] ?? 0;
$current_revenue  = $current['revenue'] ?? 0;

/* ===== LỊCH SỬ ===== */
$history = $conn->query("
    SELECT 
        YEAR(check_out) year,
        MONTH(check_out) month,
        COUNT(*) bookings,
        SUM(total_price) revenue
    FROM bookings
    WHERE check_out IS NOT NULL
    GROUP BY year, month
    ORDER BY year DESC, month DESC
");
?>

<div class="admin-container">

    <h2 class="admin-title">Báo cáo doanh thu</h2>

    <!-- KPI -->
    <div class="admin-dashboard">
        <div class="admin-card">
            <span>Tổng số phòng</span>
            <strong><?= $total_rooms ?></strong>
        </div>
        <div class="admin-card">
            <span>Đơn đã hoàn tất</span>
            <strong><?= $total_bookings ?></strong>
        </div>
        <div class="admin-card highlight">
            <span>Tổng doanh thu</span>
            <strong><?= number_format($total_revenue) ?> VND</strong>
        </div>
    </div>

    <!-- FILTER -->
    <form class="admin-filter" method="get">
        <input type="hidden" name="page" value="admin_stats">
        <input type="number" name="month" min="1" max="12" value="<?= $month ?>">
        <input type="number" name="year" value="<?= $year ?>">
        <button class="btn btn-primary">Xem</button>
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

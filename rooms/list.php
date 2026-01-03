<?php
if (!defined('IN_INDEX')) die('Access denied');
require_once "config/db.php";

/* ===== FILTER ===== */
$status     = $_GET['status'] ?? '';
$price_from = $_GET['price_from'] ?? '';
$price_to   = $_GET['price_to'] ?? '';

$sql = "
SELECT r.*,
       b.check_out
FROM rooms r
LEFT JOIN bookings b
       ON r.id = b.room_id
       AND b.check_out IS NOT NULL
WHERE 1
";

if ($status !== '') {
    $sql .= " AND r.status = '$status'";
}
if ($price_from !== '') {
    $sql .= " AND r.price >= $price_from";
}
if ($price_to !== '') {
    $sql .= " AND r.price <= $price_to";
}

$result = $conn->query($sql);
?>

<div class="rooms-container">
    <h2 class="page-title">Quản lý phòng</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="rooms-alert">
            <?= $_SESSION['error'] ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- FILTER + SEARCH -->
    <form method="get" class="rooms-filter">
        <input type="hidden" name="page" value="rooms">

        <select name="status" class="form-control">
            <option value="">-- Trạng thái --</option>
            <option value="available" <?= $status === 'available' ? 'selected' : '' ?>>Available</option>
            <option value="booked" <?= $status === 'booked' ? 'selected' : '' ?>>Booked</option>
        </select>

        <input type="number" name="price_from" class="form-control" placeholder="Giá từ" value="<?= $price_from ?>">
        <input type="number" name="price_to" class="form-control" placeholder="Giá đến" value="<?= $price_to ?>">

        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text"
                   id="roomSearch"
                   class="form-control"
                   placeholder="Tìm số phòng / loại phòng">
        </div>

        <button class="btn btn-primary">Lọc</button>
    </form>

    <!-- ACTION -->
    <div class="rooms-actions">
        <a href="index.php?page=rooms_add" class="btn btn-primary">Thêm phòng</a>
    </div>

    <!-- TABLE -->
    <table class="rooms-table">
        <thead>
        <tr>
            <th>Số phòng</th>
            <th>Loại phòng</th>
            <th>Giá</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['room_number'] ?></td>
                    <td><?= $row['room_type'] ?></td>
                    <td><?= number_format($row['price']) ?></td>
                    <td>
                        <?php if ($row['status'] === 'available'): ?>
                            <span class="room-status-available">Available</span>
                        <?php else: ?>
                            <span class="room-status-booked">Booked</span>
                            <?php
                            if (!empty($row['check_out'])) {
                                $daysLeft = (strtotime($row['check_out']) - time()) / 86400;
                                if ($daysLeft <= 2) {
                                    echo '<span class="room-warning">Sắp trống</span>';
                                }
                            }
                            ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="btn btn-primary"
                           href="index.php?page=rooms_edit&id=<?= $row['id'] ?>">Sửa</a>

                        <?php if ($row['status'] === 'available'): ?>
                            <a class="btn btn-danger"
                               href="index.php?page=rooms_delete&id=<?= $row['id'] ?>"
                               onclick="return confirm('Xóa phòng này?')">Xóa</a>
                        <?php else: ?>
                            <span class="btn-lock" title="Phòng đang có khách">🔒</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="rooms-empty">Không có phòng</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- REALTIME SEARCH -->
<script>
document.getElementById('roomSearch').addEventListener('keyup', function () {
    const keyword = this.value.toLowerCase();
    document.querySelectorAll('.rooms-table tbody tr').forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(keyword) ? '' : 'none';
    });
});
</script>

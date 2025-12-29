<?php
// customers/list.php
// LUU Y: KHONG SUA FILE config/db.php

require_once __DIR__ . '/../config/db.php';

/*
    Nguyen tac:
    - Neu chua co $connection (db chua code) -> KHONG query
    - Trang van phai hien thi binh thuong
*/

$sql = "
    SELECT
        c.id,
        c.name,
        c.phone,
        c.email,
        c.id_card,
        b.check_in,
        b.check_out
    FROM customers c
    LEFT JOIN bookings b
        ON c.id = b.customer_id
        AND b.id = (
            SELECT b2.id
            FROM bookings b2
            WHERE b2.customer_id = c.id
            ORDER BY b2.check_in DESC
            LIMIT 1
        )
    ORDER BY c.id DESC
";

// mac dinh
$result = null;

// CHI QUERY KHI DB DA TON TAI
if (isset($connection) && $connection) {
    $result = $connection->query($sql);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sach khach hang</title>
    <style>
        body {
            font-family: Arial;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }
        th {
            background: #eee;
        }
        .status-booking {
            color: green;
            font-weight: bold;
        }
        .status-reserve {
            color: orange;
            font-weight: bold;
        }
        .status-none {
            color: gray;
        }
    </style>
</head>
<body>

<h2>Danh sach khach hang</h2>

<p>
    <a href="add.php">+ Them khach hang</a>
</p>

<table>
    <tr>
        <th>ID</th>
        <th>Ten</th>
        <th>Phone</th>
        <th>Email</th>
        <th>CCCD</th>
        <th>Trang thai</th>
        <th>Check in</th>
        <th>Check out</th>
        <th>Hanh dong</th>
    </tr>

<?php
// TRUONG HOP DB CHUA SAN SANG
if (!isset($connection) || !$connection) {
    echo '<tr>
            <td colspan="9">Chua co ket noi CSDL (cho nhom truong hoan thien db)</td>
          </tr>';
}
// DB CO NHUNG CHUA CO DU LIEU
elseif (!$result || $result->num_rows == 0) {
    echo '<tr>
            <td colspan="9">Chua co du lieu khach hang</td>
          </tr>';
}
// CO DU LIEU
else {
    while ($row = $result->fetch_assoc()) {

        // XAC DINH TRANG THAI
        $status = 'Chua dat phong';
        $statusClass = 'status-none';

        if (!empty($row['check_in']) && empty($row['check_out'])) {
            $status = 'Dang o';
            $statusClass = 'status-booking';
        } elseif (!empty($row['check_in']) && !empty($row['check_out'])) {
            $status = 'Da tung o';
            $statusClass = 'status-reserve';
        }

        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['phone']) . '</td>';
        echo '<td>' . htmlspecialchars($row['email']) . '</td>';
        echo '<td>' . ($row['id_card'] ?: 'Chua co') . '</td>';
        echo '<td class="' . $statusClass . '">' . $status . '</td>';
        echo '<td>' . ($row['check_in'] ?: '-') . '</td>';
        echo '<td>' . ($row['check_out'] ?: '-') . '</td>';
        echo '<td>
                <a href="edit.php?id=' . $row['id'] . '">Sua</a> |
                <a href="delete.php?id=' . $row['id'] . '" onclick="return confirm(\'Xoa khach hang?\')">Xoa</a>
              </td>';
        echo '</tr>';
    }
}
?>

</table>

</body>
</html>

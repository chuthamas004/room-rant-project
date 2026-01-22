<?php
session_start();
require_once 'config/db.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = '';
$error = '';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $booking_id = intval($_POST['booking_id']);
    $action = $_POST['action'];

    if ($action == 'approve') {
        // Start transaction
        mysqli_begin_transaction($conn);
        try {
            // Check if room is already occupied by another confirmed booking (Optional safety check)
            // For now, we trust the flow.

            // Update booking status
            $sql_booking = "UPDATE bookings SET status = 'confirmed' WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql_booking);
            mysqli_stmt_bind_param($stmt, "i", $booking_id);
            mysqli_stmt_execute($stmt);

            // Get room_id for this booking
            $sql_get_room = "SELECT room_id FROM bookings WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql_get_room);
            mysqli_stmt_bind_param($stmt, "i", $booking_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $booking_data = mysqli_fetch_assoc($res);
            $room_id = $booking_data['room_id'];

            // Update room status to occupied
            $sql_room = "UPDATE rooms SET status = 'occupied' WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql_room);
            mysqli_stmt_bind_param($stmt, "i", $room_id);
            mysqli_stmt_execute($stmt);

            mysqli_commit($conn);
            $_SESSION['success_message'] = "อนุมัติการจองเรียบร้อยแล้ว ห้องพักถูกปรับสถานะเป็น 'ไม่ว่าง' (จะไม่แสดงในหน้ารายการห้องพัก)";
            header("Location: index.php#rooms");
            exit();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    } elseif ($action == 'reject') {
        // Start transaction
        mysqli_begin_transaction($conn);
        try {
            // Get room_id for this booking
            $sql_get_room = "SELECT room_id FROM bookings WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql_get_room);
            mysqli_stmt_bind_param($stmt, "i", $booking_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $booking_data = mysqli_fetch_assoc($res);
            $room_id = $booking_data['room_id'];

            // Update booking status to cancelled
            $sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $booking_id);
            mysqli_stmt_execute($stmt);

            // Update room status to available
            $sql_room = "UPDATE rooms SET status = 'available' WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql_room);
            mysqli_stmt_bind_param($stmt, "i", $room_id);
            mysqli_stmt_execute($stmt);

            mysqli_commit($conn);
            $_SESSION['success_message'] = "ยกเลิก/ปฏิเสธการจองเรียบร้อยแล้ว ห้องพักกลับมาว่างพร้อมให้จอง";
            header("Location: index.php#rooms");
            exit();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    } elseif ($action == 'delete') {
        // Start transaction
        mysqli_begin_transaction($conn);
        try {
            // Check current status and room
            $sql_check = "SELECT status, room_id FROM bookings WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql_check);
            mysqli_stmt_bind_param($stmt, "i", $booking_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $booking_data = mysqli_fetch_assoc($res);

            // If booking was holding the room (pending/confirmed), free the room first
            if ($booking_data['status'] == 'pending' || $booking_data['status'] == 'confirmed') {
                $status_update = "UPDATE rooms SET status = 'available' WHERE id = ?";
                $stmt_room = mysqli_prepare($conn, $status_update);
                mysqli_stmt_bind_param($stmt_room, "i", $booking_data['room_id']);
                mysqli_stmt_execute($stmt_room);
            }

            // Delete the booking
            $sql = "DELETE FROM bookings WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $booking_id);
            mysqli_stmt_execute($stmt);

            mysqli_commit($conn);
            $message = "ลบข้อมูลการจองเรียบร้อยแล้ว";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการการจอง - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FFFFFF',
                        secondary: '#1E40AF',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">
    <nav class="bg-secondary text-primary shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="admin_dashboard.php" class="text-xl font-bold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                </svg>
                กลับหน้าหลัก Admin
            </a>
            <span class="mr-4 text-sm font-medium opacity-90">Admin:
                <?php echo $_SESSION['username']; ?>
            </span>
            <a href="change_password.php" class="text-white hover:text-blue-200 transition text-sm mr-4"
                title="เปลี่ยนรหัสผ่าน">เปลี่ยนรหัส</a>
            <a href="logout.php"
                class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white transition text-xs font-bold">ออกจากระบบ</a>
        </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 border-l-4 border-secondary pl-4">จัดการการจองห้องพัก</h1>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
                <p class="font-bold">สำเร็จ!</p>
                <p>
                    <?php echo $message; ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                <p class="font-bold">ผิดพลาด!</p>
                <p>
                    <?php echo $error; ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                <h3 class="font-bold text-gray-700">รายการจองล่าสุด</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-white text-gray-600 text-xs uppercase font-semibold tracking-wider">
                            <th class="px-6 py-4 text-center border-b border-gray-200">สถานะ</th>
                            <th class="px-6 py-4 text-left border-b border-gray-200">วันที่จอง</th>
                            <th class="px-6 py-4 text-left border-b border-gray-200">ผู้จอง</th>
                            <th class="px-6 py-4 text-left border-b border-gray-200">ห้องพัก</th>
                            <th class="px-6 py-4 text-left border-b border-gray-200">รายละเอียด</th>
                            <th class="px-6 py-4 text-center border-b border-gray-200">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php
                        $sql = "SELECT b.*, u.full_name, u.phone, r.room_number, r.floor, t.name as type_name 
                                FROM bookings b 
                                JOIN users u ON b.user_id = u.id 
                                JOIN rooms r ON b.room_id = r.id 
                                JOIN room_types t ON r.type_id = t.id
                                ORDER BY 
                                    CASE WHEN b.status = 'pending' THEN 0 ELSE 1 END,
                                    b.booking_date DESC";
                        $result = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $statusClass = '';
                                $statusText = '';
                                switch ($row['status']) {
                                    case 'pending':
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                        $statusText = 'booking';
                                        break;
                                    case 'confirmed':
                                        $statusClass = 'bg-green-100 text-green-800';
                                        $statusText = 'อนุมัติแล้ว';
                                        break;
                                    case 'cancelled':
                                        $statusClass = 'bg-red-100 text-red-800';
                                        $statusText = 'ยกเลิก';
                                        break;
                                }
                                ?>
                                <tr class="hover:bg-gray-50 transition border-b border-gray-100 last:border-b-0">
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                            <?php echo $row['status'] == 'pending' ? 'รอตรวจสอบ' : $statusText; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                        <?php echo date('d/m/Y H:i', strtotime($row['booking_date'])); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">
                                            <?php echo htmlspecialchars($row['full_name']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">โทร:
                                            <?php echo htmlspecialchars($row['phone']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-blue-100 text-blue-600 rounded-lg font-bold text-sm">
                                                <?php echo $row['room_number']; ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo $row['type_name']; ?>
                                                </div>
                                                <div class="text-xs text-gray-500">ชั้น
                                                    <?php echo $row['floor']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        เข้าพัก: <span class="font-medium">
                                            <?php echo date('d/m/Y', strtotime($row['check_in_date'])); ?>
                                        </span><br>
                                        สัญญา: <span class="font-medium">
                                            <?php echo $row['contract_duration']; ?> เดือน
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if ($row['status'] == 'pending'): ?>
                                            <div class="flex justify-center gap-2">
                                                <form method="POST"
                                                    onsubmit="return confirm('ยืนยันอนุมัติการจองนี้? ห้องพักจะถูกปรับสถานะเป็นไม่ว่าง');">
                                                    <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit"
                                                        class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg shadow transition hover:scale-105"
                                                        title="อนุมัติ">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                </form>
                                                <form method="POST" onsubmit="return confirm('ยืนยันปฏิเสธการจองนี้?');">
                                                    <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit"
                                                        class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow transition hover:scale-105"
                                                        title="ปฏิเสธ">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </form>
                                                <form method="POST" onsubmit="return confirm('ยืนยันลบคำขอนี้?');">
                                                    <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="text-gray-400 hover:text-red-500 p-2 transition"
                                                        title="ลบคำขอ">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php elseif ($row['status'] == 'confirmed'): ?>
                                            <div class="flex justify-center">
                                                <div class="flex justify-center items-center gap-1">
                                                    <form method="POST"
                                                        onsubmit="return confirm('ยืนยันการแจ้งย้ายออก/คืนห้อง? ห้องจะถูกปรับสถานะเป็นว่างทันที');">
                                                        <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit"
                                                            class="bg-orange-100 hover:bg-orange-200 text-orange-700 text-xs font-bold py-1 px-3 rounded border border-orange-200 transition flex items-center gap-1"
                                                            title="แจ้งย้ายออก / คืนห้อง">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                            </svg>
                                                            แจ้งย้ายออก
                                                        </button>
                                                    </form>

                                                    <form method="POST"
                                                        onsubmit="return confirm('ยืนยันการลบข้อมูลนี้? (ห้องจะว่างทันที)');">
                                                        <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <button type="submit"
                                                            class="text-gray-400 hover:text-red-500 transition p-1"
                                                            title="ลบข้อมูล">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php elseif ($row['status'] == 'cancelled'): ?>
                                                <div class="flex justify-center">
                                                    <form method="POST"
                                                        onsubmit="return confirm('ยืนยันการลบประวัติการจองนี้? ข้อมูลจะหายไปถาวร');">
                                                        <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <button type="submit"
                                                            class="text-gray-400 hover:text-red-500 transition p-2"
                                                            title="ลบประวัติ">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-xs text-gray-400 italic">ดำเนินการแล้ว</span>
                                            <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">ไม่มีข้อมูลการจองในขณะนี้</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
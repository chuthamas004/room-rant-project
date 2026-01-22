<?php
session_start();
require_once 'config/db.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle cancellation request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'cancel') {
    $bookingId = intval($_POST['booking_id']);

    // Validate that the booking belongs to this user and is still pending
    $checkSql = "SELECT status, room_id FROM bookings WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($stmt, "ii", $bookingId, $userId);
    mysqli_stmt_execute($stmt);
    $checkResult = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($checkResult)) {
        if ($row['status'] == 'pending' || $row['status'] == 'confirmed') {
            $roomId = $row['room_id'];

            // Start transaction
            mysqli_begin_transaction($conn);

            try {
                // 1. Update booking status
                $updateSql = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
                $stmt = mysqli_prepare($conn, $updateSql);
                mysqli_stmt_bind_param($stmt, "i", $bookingId);
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Failed to update booking status.");
                }

                // 2. Update room status back to available
                $updateRoomSql = "UPDATE rooms SET status = 'available' WHERE id = ?";
                $stmtRoom = mysqli_prepare($conn, $updateRoomSql);
                mysqli_stmt_bind_param($stmtRoom, "i", $roomId);
                if (!mysqli_stmt_execute($stmtRoom)) {
                    throw new Exception("Failed to update room status.");
                }

                mysqli_commit($conn);
                $_SESSION['success_message'] = "ยกเลิกการจองเรียบร้อยแล้ว ห้องพักถูกปรับสถานะเป็น 'ว่าง' พร้อมให้จองใหม่";
                header("Location: index.php#rooms");
                exit();
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error = "เกิดข้อผิดพลาด: " . $e->getMessage();
            }
        } else {
            $error = "ไม่สามารถยกเลิกได้ เนื่องจากสถานะปัจจุบันไม่อนุญาตให้ยกเลิก";
        }
    } else {
        $error = "ไม่พบรายการจองนี้";
    }
}

// ดึงข้อมูลการจอง
$sql = "SELECT b.*, r.room_number, r.floor, t.name as type_name, t.price, t.deposit 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        JOIN room_types t ON r.type_id = t.id 
        WHERE b.user_id = ? 
        ORDER BY b.booking_date DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการจอง - Dormitory System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ffffff',
                        secondary: '#1e40af', // Blue-800
                        accent: '#3b82f6', // Blue-500
                        dark: '#1f2937',
                    },
                    fontFamily: {
                        sans: ['Sarabun', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="bg-gray-50 text-gray-800 font-sans">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-secondary flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                DormSystem
            </a>
            <div class="flex items-center gap-4">
                <a href="index.php" class="text-gray-600 hover:text-secondary transition text-sm">หน้าหลัก</a>
                <span class="text-gray-300">|</span>
                <span class="text-sm font-medium text-gray-600">สวัสดี,
                    <?php echo $_SESSION['username']; ?>
                </span>
                <a href="change_password.php" class="text-gray-400 hover:text-secondary transition"
                    title="เปลี่ยนรหัสผ่าน">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 19l-1 1-1-1-1 1-1-1-1 1-1-1 5-5A6 6 0 012 9a6 6 0 016-6 2 2 0 012 2 2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </a>
                <a href="logout.php"
                    class="bg-red-50 text-red-600 px-4 py-2 rounded-full text-sm font-semibold hover:bg-red-100 transition">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-10 max-w-5xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800">ประวัติการจองห้องพัก</h1>
            <a href="index.php#rooms"
                class="text-secondary hover:underline flex items-center gap-1 text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                จองห้องพักเพิ่ม
            </a>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-xs tracking-wider">
                                <th class="p-5 font-semibold">ห้องพัก</th>
                                <th class="p-5 font-semibold">ประเภท</th>
                                <th class="p-5 font-semibold">วันที่จอง</th>
                                <th class="p-5 font-semibold">วันเข้าพัก</th>
                                <th class="p-5 font-semibold text-center">ระยะสัญญา</th>
                                <th class="p-5 font-semibold text-right">สถานะ</th>
                                <th class="p-5 font-semibold text-right">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-blue-50/30 transition-colors group">
                                    <td class="p-5">
                                        <div class="flex items-center gap-3">
                                            <div class="bg-blue-100 p-2 rounded-lg text-secondary">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 text-lg">ห้อง
                                                    <?php echo $row['room_number']; ?>
                                                </p>
                                                <p class="text-xs text-gray-500">ชั้น
                                                    <?php echo $row['floor']; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-5 text-gray-600">
                                        <?php echo $row['type_name']; ?>
                                    </td>
                                    <td class="p-5 text-gray-600 text-sm">
                                        <?php echo date('d/m/Y H:i', strtotime($row['booking_date'])); ?>
                                    </td>
                                    <td class="p-5 text-gray-600 text-sm font-medium">
                                        <?php echo date('d M Y', strtotime($row['check_in_date'])); ?>
                                    </td>
                                    <td class="p-5 text-center text-gray-600">
                                        <span class="bg-gray-100 px-3 py-1 rounded-full text-xs font-semibold">
                                            <?php echo $row['contract_duration']; ?> เดือน
                                        </span>
                                    </td>
                                    <td class="p-5 text-right">
                                        <?php
                                        $statusClass = '';
                                        $statusText = '';
                                        switch ($row['status']) {
                                            case 'pending':
                                                $statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                                $statusText = 'รอตรวจสอบ';
                                                break;
                                            case 'confirmed':
                                                $statusClass = 'bg-green-100 text-green-800 border-green-200';
                                                $statusText = 'ยืนยันแล้ว';
                                                break;
                                            case 'cancelled':
                                                $statusClass = 'bg-red-100 text-red-800 border-red-200';
                                                $statusText = 'ยกเลิก';
                                                break;
                                            default:
                                                $statusClass = 'bg-gray-100 text-gray-800';
                                                $statusText = $row['status'];
                                        }
                                        ?>
                                        <span
                                            class="px-3 py-1.5 rounded-full text-xs font-bold border <?php echo $statusClass; ?>">
                                            <?php echo $statusText; ?>
                                        </span>
                                    </td>
                                    <td class="p-5 text-right">
                                        <?php if ($row['status'] == 'pending' || $row['status'] == 'confirmed'): ?>
                                            <form method="POST" onsubmit="return confirm('คุณต้องการยกเลิกการจองนี้ใช่หรือไม่?');">
                                                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="action" value="cancel">
                                                <button type="submit"
                                                    class="text-red-500 hover:text-red-700 text-sm font-bold border border-red-200 hover:border-red-400 bg-red-50 hover:bg-red-100 px-3 py-1 rounded transition">
                                                    ยกเลิกจอง
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-gray-300 transform scale-150 inline-block">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6 hidden">
                <!-- Mobile Card View (Optional implementation for very small screens) -->
            </div>

        <?php else: ?>
            <div class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">ยังไม่มีประวัติการจอง</h3>
                <p class="text-gray-500 mb-8">คุณยังไม่ได้จองห้องพักใดๆ กับเรา</p>
                <a href="index.php#rooms"
                    class="inline-block bg-secondary text-white px-8 py-3 rounded-full font-bold hover:bg-blue-800 transition shadow-lg transform hover:-translate-y-1">
                    ค้นหาห้องพัก
                </a>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>
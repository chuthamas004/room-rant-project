<?php
session_start();
require_once 'config/db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['room_id'])) {
    header("Location: index.php");
    exit();
}

$room_id = escape($conn, $_GET['room_id']);
$user_id = $_SESSION['user_id'];

// กำหนดตัวแปรค่าน้ำค่าไฟ (สามารถย้ายไปเก็บในฐานข้อมูลได้ในอนาคต)
$water_rate = 25;
$electric_rate = 7;

// Get room details
$sql = "SELECT r.*, t.name as type_name, t.price, t.deposit, t.facilities, t.description 
        FROM rooms r 
        JOIN room_types t ON r.type_id = t.id 
        WHERE r.id = '$room_id' AND r.status = 'available'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "ห้องนี้ไม่ว่างหรือไม่มีอยู่จริง";
    exit();
}

$room = mysqli_fetch_assoc($result);

// Handle Booking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $check_in_date = escape($conn, $_POST['check_in_date']);
    $contract_duration = escape($conn, $_POST['contract_duration']);

    // Insert booking
    $insert_sql = "INSERT INTO bookings (user_id, room_id, check_in_date, contract_duration, status) 
                   VALUES ('$user_id', '$room_id', '$check_in_date', '$contract_duration', 'pending')";

    if (mysqli_query($conn, $insert_sql)) {
        // อัปเดตสถานะห้องเป็น occupied เพื่อไม่ให้คนอื่นจองซ้ำ
        $update_room = "UPDATE rooms SET status = 'occupied' WHERE id = '$room_id'";
        mysqli_query($conn, $update_room);

        header("Location: booking_success.php");
        exit();
    } else {
        $error = "เกิดข้อผิดพลาดในการจอง: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จองห้องพัก - <?php echo $room['room_number']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Sarabun', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen py-10">
    <div class="container mx-auto px-4 max-w-2xl">
        <a href="index.php" class="text-blue-700 hover:underline mb-4 inline-block">&larr; กลับหน้าหลัก</a>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-blue-800 p-4">
                <h1 class="text-2xl font-bold text-white">ยืนยันการจองห้องพัก <?php echo $room['room_number']; ?></h1>
            </div>

            <div class="p-6">
                <div class="mb-6 border-b pb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">รายละเอียดห้องพัก</h2>
                    <div class="grid grid-cols-2 gap-4 text-gray-600">
                        <div><span class="font-semibold">ประเภท:</span> <?php echo $room['type_name']; ?></div>
                        <div><span class="font-semibold">ชั้น:</span> <?php echo $room['floor']; ?></div>
                        <div><span class="font-semibold">ค่าเช่ารายเดือน:</span> <span
                                class="text-blue-700 font-bold">฿<?php echo number_format($room['price']); ?></span>
                        </div>
                        <div><span class="font-semibold">ค่ามัดจำ:</span>
                            ฿<?php echo number_format($room['deposit']); ?></div>
                    </div>

                    <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <h3 class="font-semibold text-blue-800 mb-2 text-sm uppercase">อัตราค่าสาธารณูปโภค</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center">
                                <span class="text-gray-600 mr-2">💧 ค่าน้ำ:</span>
                                <span class="font-bold text-gray-800"><?php echo $water_rate; ?> บาท/หน่วย</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-gray-600 mr-2">⚡ ค่าไฟ:</span>
                                <span class="font-bold text-gray-800"><?php echo $electric_rate; ?> บาท/หน่วย</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <span class="font-semibold text-gray-600">สิ่งอำนวยความสะดวก:</span>
                        <p class="text-gray-500 text-sm"><?php echo $room['facilities']; ?></p>
                    </div>
                </div>

                <form action="" method="POST">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">ข้อมูลการจอง</h2>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">วันที่เริ่มเข้าอยู่</label>
                        <input type="date" name="check_in_date" required
                            class="shadow-sm border rounded w-full py-2.5 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">ระยะเวลาสัญญา</label>
                        <select name="contract_duration"
                            class="shadow-sm border rounded w-full py-2.5 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="1">1 เดือน</option>
                            <option value="3">3 เดือน</option>
                            <option value="6">6 เดือน</option>
                            <option value="12">12 เดือน</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-200">
                        ยืนยันการจองห้องพัก
                    </button>

                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-500">
                            ยอดรวมที่ต้องชำระในวันทำสัญญา:
                            <span
                                class="font-bold text-gray-800">฿<?php echo number_format($room['price'] + $room['deposit']); ?></span>
                        </p>
                        <p class="text-xs text-gray-400 mt-2">*ไม่รวมค่าน้ำและค่าไฟตามการใช้งานจริงในแต่ละเดือน</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
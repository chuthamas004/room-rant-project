<?php
session_start();
require_once 'config/db.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$id = '';
$room_number = '';
$floor = '';
$type_id = '';
$status = 'available';
$is_edit = false;

// If editing, fetch data
if (isset($_GET['id'])) {
    $is_edit = true;
    $id = escape($conn, $_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM rooms WHERE id = '$id'");
    $room = mysqli_fetch_assoc($result);
    $room_number = $room['room_number'];
    $floor = $room['floor'];
    $type_id = $room['type_id'];
    $status = $room['status'];
}

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_number = escape($conn, $_POST['room_number']);
    $floor = escape($conn, $_POST['floor']);
    $type_id = escape($conn, $_POST['type_id']);
    $status = escape($conn, $_POST['status']);

    if (isset($_POST['id']) && $_POST['id'] != '') {
        // Update
        $id = escape($conn, $_POST['id']);
        $sql = "UPDATE rooms SET room_number = '$room_number', floor = '$floor', type_id = '$type_id', status = '$status' WHERE id = '$id'";
    } else {
        // Insert
        $sql = "INSERT INTO rooms (room_number, floor, type_id, status) VALUES ('$room_number', '$floor', '$type_id', '$status')";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข้อมูลห้อง - Admin</title>
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Sarabun', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-secondary">
            <?php echo $is_edit ? 'แก้ไขห้องพัก' : 'เพิ่มห้องพัก'; ?>
        </h2>

        <form action="admin_room_form.php" method="POST">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?php echo $id; ?>">
            <?php endif; ?>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">เลขห้อง</label>
                <input
                    class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-secondary"
                    name="room_number" type="text" value="<?php echo $room_number; ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">ชั้น</label>
                <input
                    class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-secondary"
                    name="floor" type="number" value="<?php echo $floor; ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">ประเภทห้อง</label>
                <select
                    class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-secondary"
                    name="type_id" required>
                    <?php
                    $types = mysqli_query($conn, "SELECT * FROM room_types");
                    while ($t = mysqli_fetch_assoc($types)) {
                        $selected = ($t['id'] == $type_id) ? 'selected' : '';
                        echo "<option value='" . $t['id'] . "' $selected>" . $t['name'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">สถานะ</label>
                <select
                    class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-secondary"
                    name="status">
                    <option value="available" <?php if ($status == 'available')
                        echo 'selected'; ?>>ว่าง (Available)
                    </option>
                    <option value="occupied" <?php if ($status == 'occupied')
                        echo 'selected'; ?>>ไม่ว่าง (Occupied)
                    </option>
                    <option value="maintenance" <?php if ($status == 'maintenance')
                        echo 'selected'; ?>>ปรับปรุง
                        (Maintenance)</option>
                </select>
            </div>

            <div class="flex items-center justify-between gap-4">
                <a href="admin_dashboard.php"
                    class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded w-1/2 text-center transition">ยกเลิก</a>
                <button class="bg-secondary hover:bg-blue-900 text-white font-bold py-2 px-4 rounded w-1/2 transition"
                    type="submit">
                    บันทึก
                </button>
            </div>
        </form>
    </div>
</body>

</html>
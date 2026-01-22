<?php
session_start();
require_once 'config/db.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = escape($conn, $_POST['current_password']);
    $new_password = escape($conn, $_POST['new_password']);
    $confirm_password = escape($conn, $_POST['confirm_password']);
    $user_id = $_SESSION['user_id'];

    if ($new_password !== $confirm_password) {
        $error = "รหัสผ่านใหม่ไม่ตรงกัน";
    } else {
        // Verify current password
        $sql = "SELECT password FROM users WHERE id = '$user_id'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);

        if (password_verify($current_password, $user['password'])) {
            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password
            $update_sql = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";
            if (mysqli_query($conn, $update_sql)) {
                $message = "เปลี่ยนรหัสผ่านสำเร็จ";
            } else {
                $error = "เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน";
            }
        } else {
            $error = "รหัสผ่านปัจจุบันไม่ถูกต้อง";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เปลี่ยนรหัสผ่าน - Dormitory System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ffffff',
                        secondary: '#1e40af', // Blue-800
                        accent: '#3b82f6', // Blue-500
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

<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg border-t-4 border-secondary">
        <div>
            <h2 class="mt-2 text-center text-3xl font-extrabold text-gray-900">
                เปลี่ยนรหัสผ่าน
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                สำหรับบัญชี: <span class="font-medium text-secondary">
                    <?php echo $_SESSION['username']; ?>
                </span>
            </p>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                <p class="font-bold">สำเร็จ!</p>
                <p>
                    <?php echo $message; ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                <p class="font-bold">ผิดพลาด!</p>
                <p>
                    <?php echo $error; ?>
                </p>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" action="" method="POST">
            <div class="rounded-md shadow-sm -space-y-px">
                <div class="mb-4">
                    <label for="current_password"
                        class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่านปัจจุบัน</label>
                    <input id="current_password" name="current_password" type="password" required
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-secondary focus:border-secondary focus:z-10 sm:text-sm"
                        placeholder="ระบุรหัสผ่านเดิม">
                </div>
                <div class="mb-4">
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่านใหม่</label>
                    <input id="new_password" name="new_password" type="password" required
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-secondary focus:border-secondary focus:z-10 sm:text-sm"
                        placeholder="ระบุรหัสผ่านใหม่">
                </div>
                <div class="mb-4">
                    <label for="confirm_password"
                        class="block text-sm font-medium text-gray-700 mb-1">ยืนยันรหัสผ่านใหม่</label>
                    <input id="confirm_password" name="confirm_password" type="password" required
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-secondary focus:border-secondary focus:z-10 sm:text-sm"
                        placeholder="ระบุรหัสผ่านใหม่ซ้ำอีกครั้ง">
                </div>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-secondary hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary shadow-md transition transform hover:-translate-y-0.5">
                    เปลี่ยนรหัสผ่าน
                </button>
            </div>

            <div class="text-center mt-4">
                <a href="<?php echo ($_SESSION['role'] == 'admin') ? 'admin_dashboard.php' : 'index.php'; ?>"
                    class="text-sm font-medium text-gray-600 hover:text-secondary hover:underline transition">
                    &larr; กลับหน้าหลัก
                </a>
            </div>
        </form>
    </div>
</body>

</html>
<?php
session_start();
require_once 'config/db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = escape($conn, $_POST['username']);
    $password = escape($conn, $_POST['password']);
    $confirm_password = escape($conn, $_POST['confirm_password']);
    $full_name = escape($conn, $_POST['full_name']);
    $email = escape($conn, $_POST['email']);
    $phone = escape($conn, $_POST['phone']);

    if ($password !== $confirm_password) {
        $error = "รหัสผ่านไม่ตรงกัน";
    } else {
        // Check if username exists
        $check_query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($result) > 0) {
            $error = "ชื่อผู้ใช้นี้ถูกใช้ไปแล้ว";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, password, full_name, email, phone) 
                    VALUES ('$username', '$hashed_password', '$full_name', '$email', '$phone')";

            if (mysqli_query($conn, $sql)) {
                $success = "สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ";
            } else {
                $error = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - ระบบจัดการหอพัก</title>
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
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md border-t-4 border-secondary">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">สมัครสมาชิก</h2>
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">
                    <?php echo $error; ?>
                </span>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">
                    <?php echo $success; ?>
                </span>
                <p class="mt-2 text-sm"><a href="login.php" class="underline font-bold">ไปหน้าเข้าสู่ระบบ</a></p>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    ชื่อผู้ใช้
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-secondary"
                    id="username" name="username" type="text" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="full_name">
                    ชื่อ-นามสกุล
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-secondary"
                    id="full_name" name="full_name" type="text" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    อีเมล
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-secondary"
                    id="email" name="email" type="email" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                    เบอร์โทรศัพท์
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-secondary"
                    id="phone" name="phone" type="text" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    รหัสผ่าน
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline focus:border-secondary"
                    id="password" name="password" type="password" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="confirm_password">
                    ยืนยันรหัสผ่าน
                </label>
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline focus:border-secondary"
                    id="confirm_password" name="confirm_password" type="password" required>
            </div>
            <div class="flex items-center justify-between">
                <button
                    class="bg-secondary hover:bg-blue-900 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full transition"
                    type="submit">
                    สมัครสมาชิก
                </button>
            </div>
            <div class="mt-4 text-center">
                <a class="inline-block align-baseline font-bold text-sm text-secondary hover:text-blue-800"
                    href="login.php">
                    มีบัญชีอยู่แล้ว? เข้าสู่ระบบ
                </a>
            </div>
        </form>
    </div>
</body>

</html>
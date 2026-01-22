<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จองสำเร็จ - ระบบจัดการหอพัก</title>
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
    <div class="bg-white p-8 rounded-lg shadow-lg text-center max-w-lg border-t-4 border-green-500">
        <div class="mb-4 text-green-500">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold mb-4 text-gray-800">จองห้องพักสำเร็จ!</h2>
        <p class="text-gray-600 mb-8">
            ขอบคุณสำหรับการจองห้องพัก<br>
            เจ้าหน้าที่จะทำการตรวจสอบและติดต่อกลับภายใน 24 ชั่วโมง<br>
            เพื่อยืนยันการชำระมัดจำและทำสัญญา
        </p>
        <div class="flex justify-center gap-4">
            <a href="index.php"
                class="bg-secondary text-white font-bold py-2 px-6 rounded hover:bg-blue-900 transition">
                กลับหน้าหลัก
            </a>
        </div>
    </div>
</body>

</html>
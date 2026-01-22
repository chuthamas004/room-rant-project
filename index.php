<?php
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="th" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dormitory System - หอพักคุณภาพเยี่ยม</title>
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
    <style>
        .hero-bg {
            background-image: linear-gradient(rgba(67, 67, 67, 0.7), rgba(67, 67, 67, 0.5)), url('https://filebroker-cdn.lazada.co.th/kf/Sd48e687d3dfa4d34a32ecc59d6706547q.jpg');
            background-size: cover;
            background-position: center;
        }

        /* Smooth fade-in animation */
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animation-delay-200 {
            animation-delay: 0.2s;
        }

        .animation-delay-400 {
            animation-delay: 0.4s;
        }

        /* Carousel Styles */
        .carousel-container:hover .carousel-btn {
            opacity: 1;
        }

        .carousel-btn {
            opacity: 0;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.9);
            color: #1e40af;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            cursor: pointer;
            border: none;
            shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .carousel-btn:hover {
            background: #ffffff;
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-track {
            display: flex;
            transition: transform 0.5s ease-in-out;
            height: 100%;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-300 bg-white/95 backdrop-blur-sm shadow-sm" id="navbar">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-secondary flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                DormSystem
            </a>
            <div class="hidden md:flex items-center space-x-8 font-medium">
                <a href="#home" class="text-gray-600 hover:text-secondary transition">หน้าหลัก</a>
                <a href="#rooms" class="text-gray-600 hover:text-secondary transition">ห้องพักว่าง</a>
                <a href="#features" class="text-gray-600 hover:text-secondary transition">สิ่งอำนวยความสะดวก</a>
            </div>
            <div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-gray-600">สวัสดี, <?php echo $_SESSION['username']; ?></span>

                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="admin_dashboard.php"
                                class="text-sm font-medium text-red-600 hover:text-red-800 hover:underline font-bold">
                                กลับหน้าผู้ดูแล
                            </a>
                        <?php endif; ?>

                        <a href="booking_history.php"
                            class="text-sm font-medium text-secondary hover:underline">ประวัติการจอง</a>
                        <a href="change_password.php"
                            class="text-sm font-medium text-gray-500 hover:text-secondary hover:underline"
                            title="เปลี่ยนรหัสผ่าน">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 19l-1 1-1-1-1 1-1-1-1 1-1-1 5-5A6 6 0 012 9a6 6 0 016-6 2 2 0 012 2 2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </a>
                        <a href="logout.php"
                            class="bg-red-50 text-red-600 px-4 py-2 rounded-full text-sm font-semibold hover:bg-red-100 transition">ออกจากระบบ</a>
                    </div>
                <?php else: ?>
                    <a href="login.php"
                        class="bg-secondary text-white px-6 py-2.5 rounded-full text-sm font-semibold hover:bg-blue-800 hover:shadow-lg transition transform hover:-translate-y-0.5">เข้าสู่ระบบ</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="fixed top-24 left-1/2 transform -translate-x-1/2 z-50 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg flex items-center gap-3 animate-bounce-in"
            id="success-alert">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p><?php echo $_SESSION['success_message']; ?></p>
            <button onclick="document.getElementById('success-alert').remove()"
                class="ml-4 text-green-700 hover:text-green-900 font-bold">&times;</button>
        </div>
        <script>
            setTimeout(() => {
                const alert = document.getElementById('success-alert');
                if (alert) {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            }, 5000); // Hide after 5 seconds
        </script>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <!-- Hero Section -->
    <header id="home" class="relative h-screen flex items-center justify-center hero-bg text-white">
        <div class="container mx-auto px-6 text-center z-10">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight fade-in-up">
                หอพักนักศึกษา
            </h1>
            <p class="text-xl md:text-2xl mb-10 text-blue-100 fade-in-up animation-delay-200 font-light">
                สะดวกสบาย ปลอดภัย ใกล้สถานศึกษา พร้อมสิ่งอำนวยความสะดวกครบครัน
            </p>
            <a href="#rooms"
                class="inline-block bg-white text-secondary px-8 py-4 rounded-full font-bold text-lg hover:bg-blue-50 transition transform hover:scale-105 shadow-xl fade-in-up animation-delay-400">
                ค้นหาห้องพักของคุณ
            </a>
        </div>

        <!-- Scroll Down Indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3">
                </path>
            </svg>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <span class="text-secondary font-bold tracking-wider uppercase text-sm">Features</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">ทำไมต้องเลือกเรา?</h2>
                <div class="w-20 h-1 bg-secondary mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- Feature 1 -->
                <div class="text-center group hover:bg-gray-50 p-8 rounded-2xl transition duration-300">
                    <div
                        class="w-16 h-16 bg-blue-100 text-secondary rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-secondary group-hover:text-white transition duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">ระบบความปลอดภัย 24 ชม.</h3>
                    <p class="text-gray-500">อุ่นใจด้วยกล้องวงจรปิดทั่วบริเวณ
                        และเจ้าหน้าที่รักษาความปลอดภัยที่พร้อมดูแลคุณตลอดเวลา</p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center group hover:bg-gray-50 p-8 rounded-2xl transition duration-300">
                    <div
                        class="w-16 h-16 bg-blue-100 text-secondary rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-secondary group-hover:text-white transition duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">ฟรี Wi-Fi</h3>
                    <p class="text-gray-500">อินเทอร์เน็ตครอบคลุมทุกห้องพักและพื้นที่ส่วนกลาง
                        ไม่พลาดทุกการเชื่อมต่อ</p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center group hover:bg-gray-50 p-8 rounded-2xl transition duration-300">
                    <div
                        class="w-16 h-16 bg-blue-100 text-secondary rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-secondary group-hover:text-white transition duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">บริการ</h3>
                    <p class="text-gray-500">เครื่องซักผ้าหยอดเหรียญ/ตู้กดน้ำ และลานจอดรถ</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <section id="rooms" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                <div class="md:w-2/3">
                    <span class="text-secondary font-bold tracking-wider uppercase text-sm">Available Rooms</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">ห้องพักที่ว่างอยู่ในขณะนี้</h2>
                    <p class="text-gray-500 mt-4 max-w-lg">เลือกห้องพักที่เหมาะกับคุณ พร้อมเข้าอยู่ได้ทันที
                        จองง่ายๆ ผ่านระบบออนไลน์ของเรา</p>
                </div>
                <!-- Controls/Filters could go here -->
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                $sql = "SELECT r.*, t.name as type_name, t.price, t.facilities 
                        FROM rooms r 
                        JOIN room_types t ON r.type_id = t.id 
                        WHERE r.status = 'available' 
                        ORDER BY r.room_number";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $roomId = $row['id'];
                        $typeName = $row['type_name'];

                        // ตรวจสอบประเภทห้องเพื่อแยกชุดรูปภาพ
                        if (strpos($typeName, 'Deluxe') !== false || strpos($typeName, 'ดีลักซ์') !== false) {
                            $images = [
                                "https://scontent-bkk1-1.xx.fbcdn.net/v/t39.30808-6/485381593_1183534053473751_5561355666614746925_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=833d8c&_nc_ohc=a0QK-8lzkhkQ7kNvwHnV3Yc&_nc_oc=AdmkbLjTYF0HuFB_o6N6WlxImy_ZR3aH73JF_2K8iUDVb5x96eKbmU2GVWri-cfJLOM&_nc_zt=23&_nc_ht=scontent-bkk1-1.xx&_nc_gid=H-Bnks9vrzP4_BFAVWFXkw&oh=00_AfpFKkniUyKki7YEdTk0vm3MI6DJ2CEcwjAsHG_Z7ZZQyQ&oe=696646E1",
                                "https://scontent-bkk1-2.xx.fbcdn.net/v/t39.30808-6/485285271_1183534396807050_4448563805784515480_n.jpg?_nc_cat=105&ccb=1-7&_nc_sid=833d8c&_nc_ohc=TZbbG_wMFM4Q7kNvwEa2JUU&_nc_oc=AdkNWSui7tb2DjP57cKcHR-UQllkV84Bv3cnrpR3QSLPALYlrMQgKqFb55VZo4ScH5c&_nc_zt=23&_nc_ht=scontent-bkk1-2.xx&_nc_gid=ZuBm-BK66zibR2h3ec-zTg&oh=00_Afoo9g0ihCU3HyHnOLLrPSHt7nSg7lUdYFAjYGSDFn9Vfw&oe=69664630",
                                "https://scontent-bkk1-2.xx.fbcdn.net/v/t39.30808-6/485328695_1183534390140384_8358451567243733845_n.jpg?_nc_cat=107&ccb=1-7&_nc_sid=833d8c&_nc_ohc=_PJMOmanQucQ7kNvwHH5zLy&_nc_oc=Adk-NuR-DL1crhIwKuTIMwWPNn4C9GsqN3BAKhVO-rAKoIXX3FdxAvTDX2quYgEnql8&_nc_zt=23&_nc_ht=scontent-bkk1-2.xx&_nc_gid=bAlkJqHhntyQ3h_rIcf6Fg&oh=00_AfriyiXsM3RjClC3NoHBhiaiH47jnFCQ9etMhZWirI4CUA&oe=696629E7",
                                "https://scontent-bkk1-2.xx.fbcdn.net/v/t39.30808-6/485183263_1183682080125615_1350036246661429452_n.jpg?_nc_cat=102&ccb=1-7&_nc_sid=833d8c&_nc_ohc=mNqBvpWwcJMQ7kNvwF0BeEw&_nc_oc=AdkX2Ct_Bs35WzHvtbxwSL7nG07g79bLo0fUnmsrurCxJV6VT7bWFJi9Ao5P0dplnGk&_nc_zt=23&_nc_ht=scontent-bkk1-2.xx&_nc_gid=M8Jd5Qz0XmJUTfIRHHJpXQ&oh=00_AfoVveZgRu-U_nWZxkEU_pHAk_0cjS2VKNP5MKi3fobqjw&oe=69662E2F"
                            ];
                        } else {
                            $images = [
                                "https://scontent-bkk1-2.xx.fbcdn.net/v/t39.30808-6/484897734_1183682543458902_8142504132940322169_n.jpg?_nc_cat=104&ccb=1-7&_nc_sid=833d8c&_nc_ohc=bPeXlaRpfmEQ7kNvwF-Ns6n&_nc_oc=AdkWx6hJ2k9xD9iSOF2Sony-5JwmGtankdZpJdoMVb7y3fTbGyPcxD5xYx98pLQR3h4&_nc_zt=23&_nc_ht=scontent-bkk1-2.xx&_nc_gid=M3lHaEvJ8uITfRcJYiseGQ&oh=00_Afp2zWh-A4vswYEhugO6BiSB20RkHJphmWz0SFN0YKznqA&oe=69664092",
                                "https://scontent-bkk1-2.xx.fbcdn.net/v/t39.30808-6/485285271_1183534396807050_4448563805784515480_n.jpg?_nc_cat=105&ccb=1-7&_nc_sid=833d8c&_nc_ohc=TZbbG_wMFM4Q7kNvwEa2JUU&_nc_oc=AdkNWSui7tb2DjP57cKcHR-UQllkV84Bv3cnrpR3QSLPALYlrMQgKqFb55VZo4ScH5c&_nc_zt=23&_nc_ht=scontent-bkk1-2.xx&_nc_gid=ZuBm-BK66zibR2h3ec-zTg&oh=00_Afoo9g0ihCU3HyHnOLLrPSHt7nSg7lUdYFAjYGSDFn9Vfw&oe=69664630",
                                "https://scontent-bkk1-2.xx.fbcdn.net/v/t39.30808-6/485328695_1183534390140384_8358451567243733845_n.jpg?_nc_cat=107&ccb=1-7&_nc_sid=833d8c&_nc_ohc=_PJMOmanQucQ7kNvwHH5zLy&_nc_oc=Adk-NuR-DL1crhIwKuTIMwWPNn4C9GsqN3BAKhVO-rAKoIXX3FdxAvTDX2quYgEnql8&_nc_zt=23&_nc_ht=scontent-bkk1-2.xx&_nc_gid=bAlkJqHhntyQ3h_rIcf6Fg&oh=00_AfriyiXsM3RjClC3NoHBhiaiH47jnFCQ9etMhZWirI4CUA&oe=696629E7",
                                "https://scontent-bkk1-2.xx.fbcdn.net/v/t39.30808-6/485183263_1183682080125615_1350036246661429452_n.jpg?_nc_cat=102&ccb=1-7&_nc_sid=833d8c&_nc_ohc=mNqBvpWwcJMQ7kNvwF0BeEw&_nc_oc=AdkX2Ct_Bs35WzHvtbxwSL7nG07g79bLo0fUnmsrurCxJV6VT7bWFJi9Ao5P0dplnGk&_nc_zt=23&_nc_ht=scontent-bkk1-2.xx&_nc_gid=M8Jd5Qz0XmJUTfIRHHJpXQ&oh=00_AfoVveZgRu-U_nWZxkEU_pHAk_0cjS2VKNP5MKi3fobqjw&oe=69662E2F"
                            ];
                        }
                        ?>

                        <div
                            class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col group h-full transform hover:-translate-y-2">
                            <!-- Carousel -->
                            <div class="relative h-64 overflow-hidden carousel-container" id="carousel-<?php echo $roomId; ?>">
                                <div class="carousel-track">
                                    <?php foreach ($images as $img): ?>
                                        <img src="<?php echo $img; ?>" class="w-full h-full object-cover flex-shrink-0"
                                            alt="รูปห้องพัก">
                                    <?php endforeach; ?>
                                </div>

                                <button onclick="moveSlide(<?php echo $roomId; ?>, -1)" class="carousel-btn left-3">❮</button>
                                <button onclick="moveSlide(<?php echo $roomId; ?>, 1)" class="carousel-btn right-3">❯</button>

                                <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5 z-20">
                                    <?php foreach ($images as $index => $img): ?>
                                        <div class="w-2 h-2 rounded-full bg-white/70 border border-black/10 backdrop-blur-sm"></div>
                                    <?php endforeach; ?>
                                </div>

                                <div
                                    class="absolute top-4 left-4 bg-secondary/90 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-md z-20">
                                    ห้อง <?php echo $row['room_number']; ?>
                                </div>
                            </div>

                            <div class="p-6 flex flex-col flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-bold text-xl text-gray-800 line-clamp-1"><?php echo $typeName; ?></h3>
                                    <span
                                        class="bg-blue-50 text-secondary text-xs px-2.5 py-1 rounded-md font-semibold whitespace-nowrap">ชั้น
                                        <?php echo $row['floor']; ?></span>
                                </div>

                                <p class="text-gray-500 text-sm mb-4 line-clamp-2 italic h-10">
                                    "<?php echo $row['facilities']; ?>"</p>

                                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                                    <div>
                                        <span
                                            class="text-2xl font-extrabold text-secondary">฿<?php echo number_format($row['price']); ?></span>
                                        <span class="text-gray-400 text-xs">/เดือน</span>
                                    </div>
                                    <a href="booking.php?room_id=<?php echo $roomId; ?>"
                                        class="bg-gray-900 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-secondary transition shadow-lg text-sm flex items-center gap-2 group-hover:gap-3">
                                        จองเลย <span class="transition-all">→</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <?php
                    }
                } else {
                    echo "<div class='col-span-full flex flex-col items-center justify-center py-20 text-gray-400 bg-white rounded-xl border border-dashed border-gray-300'>
                        <svg class='w-16 h-16 mb-4 text-gray-300' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'></path></svg>
                        <span class='text-lg font-medium'>ขออภัย ไม่พบห้องว่างในขณะนี้</span>
                        </div>";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-secondary text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 pattern-dots"></div>
        <div class="container mx-auto px-6 text-center relative z-10">
            <h2 class="text-3xl md:text-5xl font-bold mb-8">พร้อมสัมผัสประสบการณ์การอยู่อาศัยที่ดีกว่า?</h2>
            <p class="text-xl text-blue-100 mb-10 max-w-2xl mx-auto">อย่ารอช้า! ห้องพักดีๆ มีจำนวนจำกัด
            </p>
            <div class="flex flex-col md:flex-row justify-center gap-4">
                <a href="#rooms"
                    class="bg-white text-secondary px-8 py-4 rounded-full font-bold text-lg hover:bg-gray-100 transition shadow-lg">ดูห้องว่างทั้งหมด</a>
                <a href="register.php"
                    class="bg-transparent border-2 border-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-secondary transition">สมัครสมาชิก</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="col-span-1 md:col-span-2">
                    <h4 class="text-xl font-bold text-white mb-4">DormSystem</h4>
                    <p class="mb-4 max-w-sm">ระบบจัดการหอพักที่ทันสมัยที่สุด
                        ให้บริการห้องพักคุณภาพเยี่ยมในราคาที่จับต้องได้ พร้อมสิ่งอำนวยความสะดวกครบครัน</p>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-white mb-4">เมนูด่วน</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition">หน้าหลัก</a></li>
                        <li><a href="#" class="hover:text-white transition">ค้นหาห้องพัก</a></li>
                        <li><a href="#" class="hover:text-white transition">เข้าสู่ระบบ</a></li>
                        <li><a href="#" class="hover:text-white transition">สมัครสมาชิก</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-white mb-4">ติดต่อเรา</h4>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg> 02-123-4567</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg> info@dormsystem.com</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-sm">
                <p>&copy; <?php echo date('Y'); ?> Dormitory Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        const slideStates = {};

        function moveSlide(roomId, direction) {
            if (slideStates[roomId] === undefined) slideStates[roomId] = 0;

            const container = document.getElementById(`carousel-${roomId}`);
            const track = container.querySelector('.carousel-track');
            const slides = track.querySelectorAll('img');
            const totalSlides = slides.length;

            slideStates[roomId] += direction;

            if (slideStates[roomId] >= totalSlides) slideStates[roomId] = 0;
            if (slideStates[roomId] < 0) slideStates[roomId] = totalSlides - 1;

            const offset = slideStates[roomId] * -100;
            track.style.transform = `translateX(${offset}%)`;

            const dots = container.querySelectorAll('.w-2.h-2');
            dots.forEach((dot, index) => {
                dot.style.backgroundColor = (index === slideStates[roomId]) ? 'white' : 'rgba(255,255,255,0.6)';
            });
        }

        // Navbar Scroll Effect
        window.addEventListener('scroll', function () {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-md');
                navbar.classList.replace('bg-white/95', 'bg-white');
            } else {
                navbar.classList.remove('shadow-md');
                navbar.classList.replace('bg-white', 'bg-white/95');
            }
        });
    </script>
</body>

</html>
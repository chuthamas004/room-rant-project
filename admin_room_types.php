<?php
session_start();
require_once 'config/db.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    // Add New Type
    if ($_POST['action'] == 'add') {
        $name = escape($conn, $_POST['name']);
        $price = escape($conn, $_POST['price']);
        $deposit = escape($conn, $_POST['deposit']);
        $description = escape($conn, $_POST['description']);
        $facilities = escape($conn, $_POST['facilities']);

        $sql = "INSERT INTO room_types (name, price, deposit, description, facilities) VALUES ('$name', '$price', '$deposit', '$description', '$facilities')";
        if (mysqli_query($conn, $sql)) {
            $success = "เพิ่มประเภทห้องเรียบร้อยแล้ว";
        } else {
            $error = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
        }
    }
    // Edit Type
    elseif ($_POST['action'] == 'edit') {
        $id = escape($conn, $_POST['type_id']);
        $name = escape($conn, $_POST['name']);
        $price = escape($conn, $_POST['price']);
        $deposit = escape($conn, $_POST['deposit']);
        $description = escape($conn, $_POST['description']);
        $facilities = escape($conn, $_POST['facilities']);

        $sql = "UPDATE room_types SET name = '$name', price = '$price', deposit = '$deposit', description = '$description', facilities = '$facilities' WHERE id = '$id'";
        if (mysqli_query($conn, $sql)) {
            $success = "แก้ไขข้อมูลเรียบร้อยแล้ว";
        } else {
            $error = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
        }
    }
}

// Handle Delete (GET)
if (isset($_GET['delete'])) {
    $id = escape($conn, $_GET['delete']);
    // Check if used in rooms
    $check = mysqli_query($conn, "SELECT id FROM rooms WHERE type_id = '$id' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $error = "ไม่สามารถลบประเภทห้องนี้ได้ เนื่องจากมีห้องพักที่ใช้งานอยู่";
    } else {
        mysqli_query($conn, "DELETE FROM room_types WHERE id = '$id'");
        header("Location: admin_room_types.php"); // Clean URL
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการประเภทห้องพัก - Admin</title>
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

<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <!-- Mobile Header -->
    <header
        class="md:hidden bg-secondary text-white p-4 flex justify-between items-center w-full fixed z-20 top-0 shadow-lg h-16">
        <span class="text-xl font-bold">Dormitory Admin</span>
        <button id="mobile-menu-btn" class="focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </header>

    <!-- Sidebar -->
    <aside id="sidebar"
        class="bg-secondary text-white w-64 flex flex-col fixed md:relative z-30 inset-y-0 left-0 transform -translate-x-full md:translate-x-0 transition duration-200 ease-in-out shadow-xl h-full pt-16 md:pt-0">
        <div class="p-6 text-center border-b border-blue-800 hidden md:block">
            <h1 class="text-2xl font-bold">Dormitory Admin</h1>
            <p class="text-sm text-blue-200 mt-2">ผู้ดูแล: <?php echo $_SESSION['username']; ?></p>
        </div>

        <nav class="flex-1 py-4 overflow-y-auto">
            <a href="admin_dashboard.php" class="flex items-center px-6 py-3 hover:bg-blue-800 transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                ห้องพักทั้งหมด
            </a>

            <a href="admin_bookings.php" class="flex items-center px-6 py-3 hover:bg-blue-800 transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                อนุมัติการจอง
            </a>

            <a href="admin_room_types.php"
                class="flex items-center px-6 py-3 hover:bg-blue-800 transition duration-200 bg-blue-900 border-l-4 border-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                ประเภทและราคา
            </a>

            <a href="change_password.php"
                class="flex items-center px-6 py-3 hover:bg-blue-800 transition duration-200 border-t border-blue-800 mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                เปลี่ยนรหัสผ่าน
            </a>

            <a href="index.php" class="flex items-center px-6 py-3 hover:bg-blue-800 transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                ไปยังหน้าเว็บไซต์
            </a>
        </nav>

        <div class="p-4 border-t border-blue-800">
            <a href="logout.php"
                class="flex items-center justify-center w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                ออกจากระบบ
            </a>
        </div>
    </aside>

    <!-- Overlay -->
    <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black opacity-50 z-20 md:hidden"></div>

    <!-- Main Content -->
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 md:p-8 mt-16 md:mt-0">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <h1 class="text-3xl font-bold text-gray-800">จัดการประเภทห้องพัก</h1>
                <button onclick="openModal()"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded shadow transition flex items-center w-full md:w-auto justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    เพิ่มประเภทห้องใหม่
                </button>
            </div>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline"><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ประเภทห้อง</th>
                            <th class="py-3 px-6 text-right">ราคา (บาท)</th>
                            <th class="py-3 px-6 text-right">มัดจำ (บาท)</th>
                            <th class="py-3 px-6 text-left">รายละเอียด / สิ่งอำนวยความสะดวก</th>
                            <th class="py-3 px-6 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <?php
                        $query = "SELECT * FROM room_types ORDER BY price ASC";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Prepare data for JS
                            $rowData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-100 transition">
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    <span class="font-bold text-gray-800 text-md">
                                        <?php echo $row['name']; ?>
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-right">
                                    <span>
                                        <?php echo number_format($row['price']); ?>
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-right">
                                    <span>
                                        <?php echo number_format($row['deposit']); ?>
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-left max-w-xs truncate">
                                    <div class="text-xs text-gray-500 mb-1"><?php echo $row['description']; ?></div>
                                    <span class="bg-blue-100 text-blue-700 py-0.5 px-2 rounded-full text-xs">
                                        <?php echo $row['facilities']; ?>
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex item-center justify-center">
                                        <button onclick="openModal(<?php echo $rowData; ?>)"
                                            class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-2 hover:bg-blue-200 hover:text-blue-800 transition"
                                            title="แก้ไข">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button
                                            onclick="openDeleteModal('admin_room_types.php?delete=<?php echo $row['id']; ?>')"
                                            class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center hover:bg-red-200 hover:text-red-800 transition"
                                            title="ลบ">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Delete Modal -->
    <div id="delete-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeDeleteModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                ยืนยันการลบข้อมูล
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    คุณต้องการลบประเภทห้องพักนี้ใช่หรือไม่? <br>
                                    <span class="text-red-500 text-xs">(หากมีห้องพักที่ใช้ประเภทนี้อยู่
                                        จะไม่สามารถลบได้)</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirm-delete-btn"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        ลบข้อมูล
                    </button>
                    <button type="button" onclick="closeDeleteModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        ยกเลิก
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit/Add Modal -->
    <div id="edit-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="admin_room_types.php" method="POST" id="edit-form">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start w-full">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="edit-modal-title">
                                    เพิ่ม/แก้ไข ประเภทห้องพัก
                                </h3>

                                <input type="hidden" name="action" id="modal-action" value="add">
                                <input type="hidden" name="type_id" id="modal-type-id">

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">ชื่อประเภทห้อง</label>
                                    <input
                                        class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-secondary transition"
                                        name="name" id="modal-name" type="text" required
                                        placeholder="เช่น Standard, Deluxe">
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">ราคา (เดือน)</label>
                                        <input
                                            class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-secondary transition"
                                            name="price" id="modal-price" type="number" step="0.01" required>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">มัดจำ</label>
                                        <input
                                            class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-secondary transition"
                                            name="deposit" id="modal-deposit" type="number" step="0.01" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">รายละเอียด (ย่อ)</label>
                                    <textarea
                                        class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-secondary transition"
                                        name="description" id="modal-description" rows="2"
                                        placeholder="รายละเอียดสั้นๆ"></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">สิ่งอำนวยความสะดวก</label>
                                    <textarea
                                        class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-secondary transition"
                                        name="facilities" id="modal-facilities" rows="2"
                                        placeholder="เช่น แอร์, เครื่องทำน้ำอุ่น, Wi-Fi" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            บันทึก
                        </button>
                        <button type="button" onclick="closeModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            ยกเลิก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const deleteModal = document.getElementById('delete-modal');
        const editModal = document.getElementById('edit-modal');
        let deleteUrl = '';

        function toggleSidebar() {
            const isClosed = sidebar.classList.contains('-translate-x-full');
            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        mobileMenuBtn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

        // Delete Modal Functions
        function openDeleteModal(url) {
            deleteUrl = url;
            deleteModal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            deleteModal.classList.add('hidden');
            deleteUrl = '';
        }

        document.getElementById('confirm-delete-btn').addEventListener('click', function () {
            if (deleteUrl) {
                window.location.href = deleteUrl;
            }
        });

        // Edit/Add Modal Functions
        function openModal(data = null) {
            const modalTitle = document.getElementById('edit-modal-title');
            const actionField = document.getElementById('modal-action');
            const idField = document.getElementById('modal-type-id');
            const nameField = document.getElementById('modal-name');
            const priceField = document.getElementById('modal-price');
            const depositField = document.getElementById('modal-deposit');
            const descField = document.getElementById('modal-description');
            const facilitiesField = document.getElementById('modal-facilities');

            if (data) {
                // Edit Mode
                modalTitle.textContent = 'แก้ไขประเภทห้องพัก';
                actionField.value = 'edit';
                idField.value = data.id;
                nameField.value = data.name;
                priceField.value = data.price;
                depositField.value = data.deposit;
                descField.value = data.description || '';
                facilitiesField.value = data.facilities || '';
            } else {
                // Add Mode
                modalTitle.textContent = 'เพิ่มประเภทห้องใหม่';
                actionField.value = 'add';
                idField.value = '';
                nameField.value = '';
                priceField.value = '';
                depositField.value = '';
                descField.value = '';
                facilitiesField.value = '';
            }

            editModal.classList.remove('hidden');
        }

        function closeModal() {
            editModal.classList.add('hidden');
        }
    </script>
</body>

</html>
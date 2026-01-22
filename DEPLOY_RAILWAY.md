# วิธีนำเว็บไซต์ขึ้น Railway (Deploy to Railway)

เราได้ทำการเตรียมไฟล์ `config/db.php` และ `composer.json` ให้รองรับการนำขึ้นระบบ Railway เรียบร้อยแล้ว ต่อไปนี้คือขั้นตอนการนำขึ้นเว็บไซต์จริง:

## 1. นำโค้ดขึ้น GitHub
ก่อนอื่นต้องนำโค้ดทั้งหมดขึ้น GitHub Repository ก่อน
1. สร้าง Repository ใหม่ใน GitHub
2. อัพโหลดไฟล์ทั้งหมดในโฟลเดอร์นี้ขึ้นไป

## 2. สมัครและตั้งค่า Railway
1. ไปที่ [Railway.app](https://railway.app/) และล็อกอิน (แนะนำให้ล็อกอินด้วย GitHub)
2. กดปุ่ม **New Project** -> **Deploy from GitHub repo**
3. เลือก Repository ที่เราเพิ่งสร้าง
4. กด **Deploy Now**

## 3. สร้างฐานข้อมูล (MySQL)
1. ในหน้า Dashboard ของโปรเจกต์ใน Railway
2. กดปุ่ม **New** (หรือคลิกขวาที่พื้นที่ว่าง) -> เลือก **Database** -> **MySQL**
3. รอสักครู่ให้ MySQL สร้างเสร็จ

## 4. เชื่อมต่อเว็บไซต์กับฐานข้อมูล
ปกติ Railway จะเชื่อมต่อให้โดยอัตโนมัติผ่าน Environment Variables แต่เพื่อความชัวร์:
1. คลิกที่กล่อง **MySQL**
2. ไปที่แท็บ **Variables** จะเห็นค่าต่างๆ เช่น `MYSQLHOST`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`
3. โค้ดของเราใน `config/db.php` เขียนไว้ให้รองรับตัวแปรเหล่านี้แล้ว ไม่ต้องแก้ไขอะไรเพิ่มเติม

## 5. นำเข้าข้อมูล (Import Database)
ตอนนี้ฐานข้อมูลบน Railway ยังเป็นตารางเปล่าๆ เราต้องนำเข้าไฟล์ `database.sql`:
1. คลิกที่กล่อง **MySQL**
2. ไปที่แท็บ **Connect**
3. ดูค่า **MySQL Connection URL** หรือติดตั้งโปรแกรมอย่าง [TablePlus](https://tableplus.com/) หรือ [HeidiSQL]
4. ใช้ข้อมูลในแท็บ Connect เพื่อเชื่อมต่อ Database ผ่านโปรแกรมดังกล่าว
5. หลังจากเชื่อมต่อได้แล้ว ให้เปิดไฟล์ `database.sql` ในโปรแกรม แล้วกด Run/Execute เพื่อสร้างตารางและข้อมูล

## 6. สร้างโดเมน (Public URL)
1. คลิกที่กล่องโปรเจกต์เว็บไซต์ของเรา (ไม่ใช่กล่อง MySQL)
2. ไปที่แท็บ **Settings**
3. เลื่อนลงมาที่ส่วน **Networking** -> **Public Networking**
4. กด **Generate Domain** คุณจะได้ลิงก์เว็บไซต์จริง (เช่น `xxx-production.up.railway.app`)

เสร็จสิ้น! ตอนนี้เว็บไซต์ของคุณควรจะใช้งานได้จริงบนอินเทอร์เน็ตแล้ว

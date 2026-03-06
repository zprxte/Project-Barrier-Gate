<div align="center">

# 🚧 Barrier Gate & Parking Management System
**ระบบบริหารจัดการลานจอดรถและไม้กั้นอัจฉริยะ**

[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](#)
[![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)](#)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](#)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](#)

<p>
  โปรเจกต์แอปพลิเคชันบนเว็บสำหรับบริหารจัดการลานจอดรถ ควบคุมระบบไม้กั้น บันทึกการเข้า-ออก และระบบชำระเงินออนไลน์
</p>

</div>

---

## 📖 เกี่ยวกับโปรเจกต์ (About The Project)
ระบบนี้ถูกพัฒนาขึ้นเพื่อช่วยอำนวยความสะดวกในการจัดการพื้นที่จอดรถ โดยรองรับผู้ใช้งาน 3 ระดับ ได้แก่ **ผู้ใช้งานทั่วไป (User)**, **ผู้ดูแลระบบ (Admin)** และ **ผู้บริหาร (Executive)** ครอบคลุมตั้งแต่การลงทะเบียนยานพาหนะ การบันทึกเวลาเข้าจอด การคำนวณค่าบริการ ไปจนถึงการออกรายงานสถิติ

---

## 📸 หน้าจอการใช้งานที่สำคัญ (Screenshots)

### 1. หน้าแรกและเข้าสู่ระบบ (Home & Login)
หน้าจอหลักสำหรับต้อนรับผู้ใช้งานและเข้าสู่ระบบของแต่ละระดับสิทธิ์
![Home Page](image/your-home-screenshot.png)

### 2. หน้าจัดการของผู้ใช้งาน (User Dashboard & Parking Form)
ผู้ใช้สามารถลงทะเบียนรถยนต์ บันทึกการเข้าจอด และดูประวัติการจอดรถของตนเองได้
![User Dashboard](image/your-user-dashboard.png)

### 3. ระบบชำระเงิน (Payment System)
หน้าระบบชำระเงินค่าบริการจอดรถ พร้อมการยืนยันสถานะ (Payment Success)
![Payment Page](image/your-payment-screenshot.png)

### 4. หน้าผู้ดูแลระบบและผู้บริหาร (Admin & Executive Dashboard)
แสดงภาพรวมของระบบ ข้อมูลรถที่เข้าจอด ตรวจสอบการชำระเงิน และการจัดการสิทธิ์
![Admin Dashboard](image/your-admin-dashboard.png)

---

## ✨ ฟีเจอร์เด่น (Key Features)

👤 **สำหรับผู้ใช้งาน (User):**
- สมัครสมาชิกและจัดการข้อมูลรถยนต์ส่วนตัว
- ระบบบันทึกการเข้าจอดรถ
- ระบบชำระเงินค่าบริการ

🛡️ **สำหรับผู้ดูแลระบบ (Admin):**
- ตรวจสอบข้อมูลการจอดรถทั้งหมดแบบเรียลไทม์
- ตรวจสอบและอนุมัติการชำระเงิน
- จัดการและเพิกถอนสิทธิ์ผู้ใช้งาน

📊 **สำหรับผู้บริหาร (Executive):**
- แดชบอร์ดแสดงสถิติและภาพรวมการทำงานของลานจอดรถ

---

## 🛠️ โครงสร้างไฟล์ (File Structure)
```text
📦 Project
 ┣ 📂 image/                  # โฟลเดอร์เก็บรูปภาพประกอบ
 ┣ 📜 index.html              # หน้าแรกของเว็บไซต์
 ┣ 📜 connect.php             # ตั้งค่าการเชื่อมต่อฐานข้อมูล
 ┣ 📜 barrier_gate_system.sql # ไฟล์ฐานข้อมูล (Import ลง MySQL)
 ┣ 📜 user_dashboard.php      # แดชบอร์ดผู้ใช้งานทั่วไป
 ┣ 📜 admin_dashboard.php     # แดชบอร์ดผู้ดูแลระบบ
 ┣ 📜 executive_dashboard.php # แดชบอร์ดผู้บริหาร
 ┣ 📜 payment_page.php        # หน้าต่างชำระเงิน
 ┗ 📜 style.css               # ไฟล์จัดการความสวยงาม (CSS)

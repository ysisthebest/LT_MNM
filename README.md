# Plugin Quản lý Cán bộ (WordPress + PHP)

**Đề tài:** Sử dụng phần mềm WordPress và ngôn ngữ PHP xây dựng chức hệ thống quản lý cán bộ

| Thành viên | MSSV | Phân công công việc |
|------------|------|---------------------|
| **Nguyễn Thanh Tịnh** | 23810310439 | Cài đặt XAMPP, WordPress, plugin, kiểm tra tạo bảng dữ liệu và chạy demo hệ thống |
| **Ngô Xuân Trường** | 23810310345 | Phụ trách viết báo cáo phần CRUD cán bộ, tìm kiếm, lọc dữ liệu, import CSV và mô tả cơ sở dữ liệu |
| **Nguyễn Hoàng Việt** | 23810310438 | Phụ trách viết báo cáo phần phân quyền, thống kê, báo cáo, nhật ký thao tác và chuẩn bị ảnh minh họa giao diện |

**Báo cáo môn Lập trình phần mềm mã nguồn mở**

Plugin chạy trên **WordPress** (cài bằng **XAMPP** trên Windows). Đọc file này **từ đầu đến cuối** nếu chưa từng cài — làm đúng thứ tự.

---

## 1. Cần cài gì trên máy?

| Phần mềm | Dùng để làm gì | Link tải |
|----------|-----------------|----------|
| **XAMPP** | Chạy Apache (web) + MySQL (database) | https://www.apachefriends.org/ |
| **WordPress** | Hệ thống web (đã có trong project hoặc tải thêm) | https://vi.wordpress.org/download/ |

**Không cần** cài PHP riêng — XAMPP đã có sẵn.

---

## 2. Bật XAMPP (làm mỗi lần mở máy làm bài)

1. Mở **XAMPP Control Panel**.
2. Bấm **Start** ở dòng **Apache** → chờ chuyển xanh.
3. Bấm **Start** ở dòng **MySQL** → chờ chuyển xanh.

Nếu Apache/MySQL báo lỗi (port 80 hoặc 3306 bị chiếm): tắt Skype, tắt ứng dụng web khác, hoặc hỏi nhóm trưởng.

---

## 3. Lấy code plugin về máy (cả nhóm)

### Cách A — Copy từ USB / Zalo / Google Drive (dễ nhất)

1. Người có project đẩy đủ **nén (zip)** cả thư mục `quanlicb`.
2. Mỗi người giải nén vào đúng chỗ:

```
C:\xampp\htdocs\wordpress\wp-content\plugins\quanlicb\
```

**Quan trọng:** Trong `plugins` phải thấy file `quanlicb.php`, không phải `plugins\quanlicb\quanlicb\quanlicb.php` (lồng 2 lần là sai).

Cấu trúc đúng:

```
C:\xampp\htdocs\wordpress\
+-- wp-admin\
+-- wp-config.php
+-- wp-content\
    +-- plugins\
        +-- quanlicb\          ← thư mục plugin
            +-- quanlicb.php   ← file chính
            +-- sql\
            +-- includes\
            +-- admin\
            +-- assets\
```

### Cách B — Clone Git (nếu nhóm dùng GitHub)

```bash
cd C:\xampp\htdocs\wordpress\wp-content\plugins
git clone <link-repo-cua-nhom> quanlicb
```

---

## 4. Tạo bảng dữ liệu `tblCanBo` (phpMyAdmin)

**Ai làm một lần** (hoặc mỗi người làm trên máy mình) — chọn **một** trong hai cách:

### Cách 1 — Chạy file SQL (khuyến dùng, dễ kiểm tra)

1. Mở **http://localhost/phpmyadmin**
2. Bên trái bấm database **`wordpress`**
3. Tab **SQL**
4. Mở file trong project: `quanlicb\sql\tblCanBo.sql`
5. **Ctrl+A** → **Ctrl+C** → dán vào ô SQL → **Go** / **Thực hiện**
6. Thành công khi bên trái thấy bảng **`wp_tblCanBo`** (có thể có 5 dòng mẫu)

### Cách 2 — Để plugin tự tạo bảng

1. Làm bước 5 (kích hoạt plugin) trước.
2. Plugin tự tạo bảng khi **Activate**.
3. Vào **Quản lý CB** → nếu trống thì **Thêm mới** hoặc chạy lại file SQL cách 1.

**Lưu ý:** Database phải tên **`wordpress`** (hoặc sửa dòng `USE wordpress;` trong file SQL cho đúng tên DB trên máy bạn — xem `wp-config.php` dùng `DB_NAME`).

---

## 5. Kích hoạt plugin WordPress

1. Đăng nhập WordPress admin: **http://localhost/wordpress/wp-admin/**
2. Menu trái: **Plugins** → **Installed Plugins**
3. Tìm **Quản lý Cán bộ** → bấm **Activate** / **Kích hoạt**
4. Menu trái xuất hiện **Quản lý CB** (icon người)

Không thấy plugin → quay lại **mục 3** kiểm tra đường dẫn thư mục.

---

## 6. Dùng plugin (demo báo cáo)

| Menu | Làm gì |
|------|--------|
| **Quản lý CB → Danh sách** | Xem, tìm kiếm, lọc, sửa, xóa |
| **Quản lý CB → Thêm mới** | Thêm cán bộ |
| **Quản lý CB → Thống kê & Báo cáo** | Biểu đồ tổng quan, lọc theo kỳ, in báo cáo |

### Thử nhanh

1. **Danh sách** → bấm **Thêm mới**
2. Điền: Mã `CB006`, Họ tên, Ngày sinh, Phòng ban, Hệ số, Lương cơ bản
3. **Tổng lương** tự tính = Hệ số × Lương cơ bản
4. **Chọn ảnh** (tùy chọn) → **Thêm**
5. Về **Danh sách** → thử **Lọc** theo phòng ban
6. Vào **Thống kê & Báo cáo** xem biểu đồ và lọc theo kỳ

---

## 7. Phân quyền (phần báo cáo "đúng điểm")

### Admin (mặc định — tài khoản cài WordPress)

- Thêm / sửa / xóa cán bộ

### Nhân viên — chỉ xem

1. **Users → Add New** tạo user test (vd: `nhanvien` / mật khẩu `123456`)
2. **Role:** chọn **Nhân viên (Quản lý CB)**
3. Đăng xuất admin → đăng nhập `nhanvien`
4. Chỉ thấy **Danh sách** + **Thống kê & Báo cáo**, **không** có **Thêm mới**, **không** có nút Sửa/Xóa

---

## 8. Phân công nhóm 3 người (gợi ý)

| Thành viên | Việc |
|------------|------|
| **Người 1** | Cài XAMPP + WordPress + SQL + quay màn hình "cài đặt" |
| **Người 2** | Viết báo cáo: CRUD, tìm kiếm, validate, SQL minh họa |
| **Người 3** | Viết báo cáo: phân quyền, thống kê, upload ảnh, phân trang |

**Cả 3** đều phải chạy được trên máy mình (làm theo mục 2 → 7).

---

## 9. Lỗi thường gặp — xử lý

| Triệu chứng | Cách sửa |
|-------------|----------|
| `localhost` không mở | Bật **Apache** trong XAMPP |
| WordPress báo lỗi database | Bật **MySQL**; kiểm tra DB `wordpress` đã tạo chưa |
| Không thấy plugin | Thư mục phải là `plugins\quanlicb\quanlicb.php` |
| Vào **Quản lý CB** trống / lỗi bảng | Chạy lại `sql\tblCanBo.sql` trong phpMyAdmin |
| Trang admin không đổi giao diện | **Ctrl + F5** hoặc tắt/bật lại plugin |
| Không upload được ảnh | Đang nhập bằng tài khoản **Administrator** |
| Mã cán bộ báo trùng | Đổi mã khác (vd `CB007`) — mỗi mã chỉ 1 lần |
| Chữ tiếng Việt bị lỗi / thiếu dấu | Lưu file plugin UTF-8 (không BOM); trình duyệt dùng font hỗ trợ tiếng Việt |

---

## 10. Đường dẫn & link nhanh

| Mục | Địa chỉ |
|-----|---------|
| Trang chủ WordPress | http://localhost/wordpress/ |
| Trang quản trị | http://localhost/wordpress/wp-admin/ |
| phpMyAdmin | http://localhost/phpmyadmin |
| Plugin trên ổ đĩa | `C:\xampp\htdocs\wordpress\wp-content\plugins\quanlicb\` |
| File SQL | `quanlicb\sql\tblCanBo.sql` |

---

## 11. Bảng dữ liệu `wp_tblCanBo`

| Cột | Ý nghĩa |
|-----|---------|
| MaCB | Mã cán bộ (khóa chính, không trùng) |
| HoTen | Họ tên |
| NgaySinh | Ngày sinh |
| GioiTinh | Nam / Nữ / Khác |
| PhongBan | Phòng ban |
| HeSoLuong | Hệ số lương (> 0) |
| LuongCoBan | Lương cơ bản |
| TongLuong | **HeSoLuong × LuongCoBan** (tự tính khi lưu) |
| AnhDaiDien | ID ảnh trong Media Library |

---

## 12. Chức năng (đối chiếu đề bài)

| # | Yêu cầu | Trong plugin |
|---|---------|--------------|
| 1 | CRUD | Danh sách / Thêm / Sửa / Xóa |
| 2 | Tìm kiếm + lọc | Mã, họ tên, phòng ban, giới tính |
| 3 | Tính lương | `TongLuong = HeSoLuong × LuongCoBan` |
| 4 | Phân quyền | Admin đủ quyền; Nhân viên chỉ xem |
| 5 | Upload ảnh | Media Library |
| 6 | Thống kê & Báo cáo | Tổng quan, lọc kỳ, biểu đồ, in báo cáo |
| 7 | Phân trang | 10 dòng/trang |
| 8 | Validate | Mã không trùng, hệ số > 0, ngày sinh hợp lệ |

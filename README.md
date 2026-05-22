# Plugin Quản lý Cán bộ (WordPress + PHP)

**Báo cáo môn Lập trình phần mềm mã nguồn mở**

**Đề tài:** Sử dụng phần mềm WordPress và ngôn ngữ PHP xây dựng chức năng cập nhật và tìm kiếm cán bộ

| STT | Thành viên | MSSV | Phân công công việc |
|---|---|---|---|
| 1 | **Nguyễn Thanh Tịnh** | 23810310439 | Cài đặt XAMPP, WordPress, plugin; kiểm tra tạo bảng dữ liệu; chạy demo hệ thống |
| 2 | **Ngô Xuân Trường** | 23810310345 | Viết báo cáo phần CRUD cán bộ, tìm kiếm, lọc dữ liệu, import CSV và mô tả cơ sở dữ liệu |
| 3 | **Nguyễn Hoàng Việt** | 23810310438 | Viết báo cáo phần phân quyền, thống kê, báo cáo, nhật ký thao tác và chuẩn bị ảnh minh họa giao diện |

Plugin chạy trên **WordPress** (cài bằng **XAMPP** trên Windows). Nếu chưa từng cài hoặc chạy project, nên đọc file này theo đúng thứ tự từ trên xuống dưới.

---

## 1. Cần cài gì trên máy?

| Phần mềm | Dùng để làm gì | Link tải |
|---|---|---|
| **XAMPP** | Chạy Apache, MySQL, PHP | https://www.apachefriends.org/ |
| **WordPress** | Nền tảng website để cài plugin | https://vi.wordpress.org/download/ |

**Không cần** cài PHP riêng vì XAMPP đã có sẵn.

---

## 2. Bật XAMPP

1. Mở **XAMPP Control Panel**.
2. Nhấn **Start** ở dòng **Apache**.
3. Nhấn **Start** ở dòng **MySQL**.
4. Kiểm tra cả hai dịch vụ đều chuyển sang màu xanh.

Nếu Apache hoặc MySQL báo lỗi cổng:
- Tắt các ứng dụng đang chiếm cổng 80 hoặc 3306.
- Kiểm tra lại cấu hình XAMPP.

---

## 3. Lấy code plugin về máy

### Cách 1: Copy thư mục plugin

Sao chép thư mục plugin vào đúng đường dẫn:

```text
C:\xampp\htdocs\wordpress\wp-content\plugins\quanlicb\
```

Kiểm tra trong thư mục `quanlicb` phải có file:

```text
quanlicb.php
```

### Cách 2: Clone bằng Git

```bash
cd C:\xampp\htdocs\wordpress\wp-content\plugins
git clone https://github.com/ysisthebest/LT_MNM.git quanlicb
```

---

## 4. Tạo cơ sở dữ liệu và bảng dữ liệu

### Tạo database WordPress

1. Mở `http://localhost/phpmyadmin`
2. Tạo database tên `wordpress`
3. Giữ mã hóa mặc định hoặc chọn `utf8mb4`

### Tạo bảng `tblCanBo`

Có thể chọn **một** trong hai cách:

#### Cách 1: Chạy file SQL thủ công

1. Mở database `wordpress` trong phpMyAdmin
2. Chọn tab **SQL**
3. Mở file:

```text
quanlicb\sql\tblCanBo.sql
```

4. Copy toàn bộ nội dung file và dán vào ô SQL
5. Nhấn **Go / Thực hiện**
6. Kiểm tra đã có bảng `wp_tblCanBo`

#### Cách 2: Để plugin tự tạo bảng

1. Kích hoạt plugin ở bước 5
2. Plugin sẽ tự tạo bảng khi Activate
3. Nếu chưa có dữ liệu thì có thể thêm mới hoặc chạy lại file SQL

---

## 5. Kích hoạt plugin WordPress

1. Đăng nhập trang quản trị:

```text
http://localhost/wordpress/wp-admin/
```

2. Vào **Plugins** -> **Installed Plugins**
3. Tìm plugin **Quản lý Cán bộ**
4. Nhấn **Activate / Kích hoạt**
5. Sau khi kích hoạt, menu **Quản lý CB** sẽ xuất hiện ở thanh bên trái

Nếu không thấy plugin:
- Kiểm tra lại đường dẫn thư mục plugin
- Đảm bảo file `quanlicb.php` nằm đúng trong thư mục `quanlicb`

---

## 6. Dùng plugin

| Menu | Chức năng |
|---|---|
| **Quản lý CB -> Dashboard** | Xem tổng quan dữ liệu cán bộ |
| **Quản lý CB -> Danh sách** | Xem, tìm kiếm, lọc, sửa, xóa cán bộ |
| **Quản lý CB -> Thêm mới** | Thêm hồ sơ cán bộ |
| **Quản lý CB -> Phòng ban** | Quản lý danh mục phòng ban |
| **Quản lý CB -> Chức vụ** | Quản lý danh mục chức vụ |
| **Quản lý CB -> Import CSV** | Nhập dữ liệu hàng loạt từ file CSV |
| **Quản lý CB -> Thống kê & Báo cáo** | Xem biểu đồ, thống kê, in báo cáo |
| **Quản lý CB -> Nhật ký** | Xem lịch sử thao tác hệ thống |

### Thử nhanh

1. Vào **Danh sách** rồi nhấn **Thêm mới**
2. Nhập:
   - Mã cán bộ: `CB006`
   - Họ tên
   - Ngày sinh
   - Phòng ban
   - Chức vụ
   - Hệ số lương
   - Lương cơ bản
3. Hệ thống tự tính:

```text
Tổng lương = Hệ số lương × Lương cơ bản
```

4. Chọn ảnh đại diện nếu cần
5. Lưu lại và kiểm tra trong danh sách
6. Thử lọc theo phòng ban hoặc chức vụ
7. Vào mục **Thống kê & Báo cáo** để xem biểu đồ

---

## 7. Phân quyền

### Administrator

Tài khoản quản trị WordPress có toàn quyền:
- Xem dữ liệu
- Thêm, sửa, xóa cán bộ
- Quản lý phòng ban, chức vụ
- Import CSV
- Xem báo cáo và nhật ký

### Nhân viên (Quản lý CB)

Role này được plugin tự tạo. Người dùng role này chỉ có quyền:
- Xem danh sách cán bộ
- Xem chi tiết cán bộ
- Xem dashboard
- Xem thống kê và báo cáo

Không có quyền:
- Thêm mới
- Chỉnh sửa
- Xóa dữ liệu
- Quản lý danh mục
- Import dữ liệu

### Tạo tài khoản nhân viên để test

1. Vào **Users -> Add New**
2. Tạo user mới, ví dụ:
   - Username: `nhanvien`
   - Password: `123456`
3. Chọn role:

```text
Nhân viên (Quản lý CB)
```

4. Đăng xuất admin và đăng nhập lại bằng tài khoản nhân viên để kiểm tra quyền

---

## 8. Lỗi thường gặp và cách xử lý

| Triệu chứng | Cách xử lý |
|---|---|
| Không mở được `localhost` | Kiểm tra Apache đã bật trong XAMPP chưa |
| WordPress lỗi kết nối database | Kiểm tra MySQL đã bật và database `wordpress` đã tạo chưa |
| Không thấy plugin trong admin | Kiểm tra thư mục `wp-content/plugins/quanlicb` và file `quanlicb.php` |
| Vào `Quản lý CB` bị lỗi bảng | Chạy lại file `sql/tblCanBo.sql` hoặc kích hoạt lại plugin |
| Không upload được ảnh | Đăng nhập bằng tài khoản Administrator |
| Mã cán bộ bị trùng | Đổi sang mã khác, mỗi mã chỉ được dùng một lần |
| Chữ tiếng Việt hiển thị lỗi | Đảm bảo file được lưu UTF-8 |

---

## 9. Đường dẫn nhanh

| Mục | Đường dẫn |
|---|---|
| Trang chủ WordPress | `http://localhost/wordpress/` |
| Trang quản trị | `http://localhost/wordpress/wp-admin/` |
| phpMyAdmin | `http://localhost/phpmyadmin` |
| Thư mục plugin | `C:\xampp\htdocs\wordpress\wp-content\plugins\quanlicb\` |
| File SQL | `quanlicb\sql\tblCanBo.sql` |

---

## 10. Bảng dữ liệu `wp_tblCanBo`

| Cột | Ý nghĩa |
|---|---|
| `MaCB` | Mã cán bộ, khóa chính |
| `HoTen` | Họ tên cán bộ |
| `NgaySinh` | Ngày sinh |
| `GioiTinh` | Nam / Nữ / Khác |
| `PhongBan` | Phòng ban |
| `ChucVu` | Chức vụ |
| `HeSoLuong` | Hệ số lương |
| `LuongCoBan` | Lương cơ bản |
| `TongLuong` | Tổng lương tự động tính |
| `AnhDaiDien` | ID ảnh trong Media Library |

---

## 11. Chức năng đối chiếu đề bài

| STT | Yêu cầu | Trong plugin |
|---|---|---|
| 1 | CRUD cán bộ | Danh sách / Thêm / Sửa / Xóa |
| 2 | Tìm kiếm và lọc | Theo mã, họ tên, phòng ban, chức vụ, giới tính, khoảng lương |
| 3 | Tính lương | `TongLuong = HeSoLuong × LuongCoBan` |
| 4 | Phân quyền | Admin toàn quyền, nhân viên chỉ xem |
| 5 | Upload ảnh | Dùng Media Library |
| 6 | Thống kê & báo cáo | Dashboard, biểu đồ, lọc theo kỳ, in báo cáo |
| 7 | Phân trang | 10 dòng mỗi trang |
| 8 | Validate dữ liệu | Mã không trùng, hệ số > 0, ngày sinh hợp lệ |
| 9 | Nhật ký thao tác | Ghi create, update, delete, import |
| 10 | Import CSV | Thêm mới hoặc cập nhật theo `MaCB` |

---

## 12. Cấu trúc code chính

```text
quanlicb/
|-- quanlicb.php
|-- README.md
|-- sql/
|   `-- tblCanBo.sql
|-- includes/
|   |-- class-database.php
|   |-- class-canbo.php
|   |-- class-validator.php
|   |-- class-phongban.php
|   |-- class-chucvu.php
|   |-- class-statistics.php
|   |-- class-permissions.php
|   |-- class-audit-log.php
|   `-- class-seed.php
|-- admin/
|   |-- class-admin.php
|   `-- views/
|-- assets/
|   |-- css/
|   `-- js/
```

---

## 13. Checklist trước khi nộp bài

- [ ] Apache và MySQL đang chạy
- [ ] Vào được trang quản trị WordPress
- [ ] Plugin đã kích hoạt
- [ ] Có bảng `wp_tblCanBo`
- [ ] Chạy được thêm, sửa, xóa, tìm kiếm
- [ ] Có dữ liệu để demo dashboard và báo cáo
- [ ] Test thử tài khoản nhân viên chỉ xem
- [ ] Ảnh đại diện và import CSV hoạt động bình thường


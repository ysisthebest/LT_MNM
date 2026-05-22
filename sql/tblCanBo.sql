-- =============================================================================
-- Plugin Quản lý Cán bộ (WordPress)
-- Chạy trong phpMyAdmin: chọn database WordPress -> tab SQL -> dán & Execute
-- Bảng chính: wp_tblCanBo
-- Công thức: TongLuong = HeSoLuong * LuongCoBan
-- =============================================================================

CREATE TABLE IF NOT EXISTS `wp_tblCanBo` (
  `MaCB`        varchar(20)   NOT NULL,
  `HoTen`       varchar(255)  NOT NULL,
  `NgaySinh`    date          NOT NULL,
  `GioiTinh`    varchar(10)   NOT NULL DEFAULT 'Nam',
  `PhongBan`    varchar(100)  NOT NULL,
  `ChucVu`      varchar(100)  NOT NULL DEFAULT '',
  `HeSoLuong`   decimal(5,2)  NOT NULL DEFAULT 1.00,
  `LuongCoBan`  decimal(15,0) NOT NULL DEFAULT 0,
  `TongLuong`   decimal(15,0) NOT NULL DEFAULT 0,
  `AnhDaiDien`  bigint(20) unsigned DEFAULT NULL,
  `created_at`  datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`MaCB`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wp_tblCanBo` (
  `MaCB`, `HoTen`, `NgaySinh`, `GioiTinh`, `PhongBan`, `ChucVu`,
  `HeSoLuong`, `LuongCoBan`, `TongLuong`, `AnhDaiDien`
) VALUES
('CB001', 'Nguyen Van An',  '1990-05-15', 'Nam', 'Kế toán',    'Trưởng phòng', 2.50, 5000000, 12500000, NULL),
('CB002', 'Tran Thi Binh',  '1992-08-20', 'Nu',  'Nhân sự',    'Nhân viên',    2.00, 4800000,  9600000, NULL),
('CB003', 'Le Van Cuong',   '1988-03-10', 'Nam', 'Kỹ thuật',   'Phó phòng',    3.00, 5500000, 16500000, NULL),
('CB004', 'Pham Thi Dung',  '1995-11-25', 'Nu',  'Kế toán',    'Nhân viên',    2.20, 4900000, 10780000, NULL),
('CB005', 'Hoang Van Em',   '1991-07-07', 'Nam', 'Hành chính', 'Nhân viên',    1.80, 4500000,  8100000, NULL);

-- Truy vấn tham khảo:
-- SELECT * FROM wp_tblCanBo WHERE PhongBan = 'Kế toán';
-- SELECT * FROM wp_tblCanBo WHERE ChucVu = 'Trưởng phòng';
-- SELECT SUM(TongLuong) AS tong_quy_luong FROM wp_tblCanBo;
-- SELECT PhongBan, COUNT(*) AS so_luong FROM wp_tblCanBo GROUP BY PhongBan;

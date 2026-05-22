<?php
/**
 * Dữ liệu mẫu tùy chọn.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuanLiCB_Seed {

	/**
	 * Thêm vài cán bộ mẫu nếu bảng đang trống.
	 */
	public static function maybe_seed() {
		if ( class_exists( 'QuanLiCB_PhongBan' ) ) {
			QuanLiCB_PhongBan::maybe_seed_defaults();
		}

		if ( class_exists( 'QuanLiCB_ChucVu' ) ) {
			QuanLiCB_ChucVu::maybe_seed_defaults();
		}

		if ( QuanLiCB_Statistics::total_can_bo() > 0 ) {
			return;
		}

		$samples = array(
			array( 'MaCB' => 'CB001', 'HoTen' => 'Nguyen Van An', 'NgaySinh' => '1990-05-15', 'GioiTinh' => 'Nam', 'PhongBan' => 'Kế toán', 'ChucVu' => 'Trưởng phòng', 'HeSoLuong' => 2.5, 'LuongCoBan' => 5000000 ),
			array( 'MaCB' => 'CB002', 'HoTen' => 'Tran Thi Binh', 'NgaySinh' => '1992-08-20', 'GioiTinh' => 'Nu', 'PhongBan' => 'Nhân sự', 'ChucVu' => 'Nhân viên', 'HeSoLuong' => 2.0, 'LuongCoBan' => 4800000 ),
			array( 'MaCB' => 'CB003', 'HoTen' => 'Le Van Cuong', 'NgaySinh' => '1988-03-10', 'GioiTinh' => 'Nam', 'PhongBan' => 'Kỹ thuật', 'ChucVu' => 'Phó phòng', 'HeSoLuong' => 3.0, 'LuongCoBan' => 5500000 ),
			array( 'MaCB' => 'CB004', 'HoTen' => 'Pham Thi Dung', 'NgaySinh' => '1995-11-25', 'GioiTinh' => 'Nu', 'PhongBan' => 'Kế toán', 'ChucVu' => 'Nhân viên', 'HeSoLuong' => 2.2, 'LuongCoBan' => 4900000 ),
			array( 'MaCB' => 'CB005', 'HoTen' => 'Hoang Van Em', 'NgaySinh' => '1991-07-07', 'GioiTinh' => 'Nam', 'PhongBan' => 'Hành chính', 'ChucVu' => 'Nhân viên', 'HeSoLuong' => 1.8, 'LuongCoBan' => 4500000 ),
		);

		foreach ( $samples as $row ) {
			QuanLiCB_CanBo::create( $row );
		}
	}
}

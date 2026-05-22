<?php
/**
 * Validate dữ liệu cán bộ.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuanLiCB_Validator {

	/**
	 * Chuẩn hóa chuỗi nhập liệu.
	 */
	protected static function normalize_text( $value ) {
		$value = sanitize_text_field( (string) $value );
		return trim( preg_replace( '/\s+/u', ' ', $value ) );
	}

	/**
	 * Validate form thêm/sửa.
	 *
	 * @param array  $data     Dữ liệu POST.
	 * @param string $mode     'create' hoặc 'update'.
	 * @param string $old_macb Mã cũ khi sửa.
	 * @return array{valid: bool, errors: string[]}
	 */
	public static function validate( $data, $mode = 'create', $old_macb = '' ) {
		$errors = array();

		$macb = isset( $data['MaCB'] ) ? strtoupper( self::normalize_text( $data['MaCB'] ) ) : '';
		if ( empty( $macb ) ) {
			$errors[] = __( 'Mã cán bộ không được để trống.', 'quanlicb' );
		} elseif ( ! preg_match( '/^[A-Za-z0-9_-]{2,20}$/', $macb ) ) {
			$errors[] = __( 'Mã cán bộ chỉ gồm chữ, số, gạch ngang (2-20 ký tự).', 'quanlicb' );
		} elseif ( 'create' === $mode && QuanLiCB_CanBo::exists( $macb ) ) {
			$errors[] = __( 'Mã cán bộ đã tồn tại, không được trùng.', 'quanlicb' );
		} elseif ( 'update' === $mode && $macb !== $old_macb && QuanLiCB_CanBo::exists( $macb ) ) {
			$errors[] = __( 'Mã cán bộ mới đã tồn tại.', 'quanlicb' );
		}

		$hoten = isset( $data['HoTen'] ) ? self::normalize_text( $data['HoTen'] ) : '';
		if ( empty( $hoten ) ) {
			$errors[] = __( 'Họ tên không được để trống.', 'quanlicb' );
		} elseif ( mb_strlen( $hoten ) < 4 || mb_strlen( $hoten ) > 100 ) {
			$errors[] = __( 'Họ tên phải từ 4 đến 100 ký tự.', 'quanlicb' );
		} elseif ( ! preg_match( "/^[\p{L}\s'.-]+$/u", $hoten ) ) {
			$errors[] = __( 'Họ tên chứa ký tự không hợp lệ.', 'quanlicb' );
		}

		$ngaysinh = isset( $data['NgaySinh'] ) ? sanitize_text_field( $data['NgaySinh'] ) : '';
		if ( empty( $ngaysinh ) ) {
			$errors[] = __( 'Ngày sinh không được để trống.', 'quanlicb' );
		} else {
			$d = DateTime::createFromFormat( 'Y-m-d', $ngaysinh );
			if ( ! $d || $d->format( 'Y-m-d' ) !== $ngaysinh ) {
				$errors[] = __( 'Ngày sinh không hợp lệ (YYYY-MM-DD).', 'quanlicb' );
			} elseif ( $d > new DateTime( 'today' ) ) {
				$errors[] = __( 'Ngày sinh không được ở tương lai.', 'quanlicb' );
			} elseif ( $d < new DateTime( '1900-01-01' ) ) {
				$errors[] = __( 'Ngày sinh không hợp lệ.', 'quanlicb' );
			} else {
				$age = (int) $d->diff( new DateTime( 'today' ) )->y;
				if ( $age < 18 ) {
					$errors[] = __( 'Cán bộ phải đủ 18 tuổi.', 'quanlicb' );
				} elseif ( $age > 80 ) {
					$errors[] = __( 'Ngày sinh vượt quá độ tuổi hợp lệ.', 'quanlicb' );
				}
			}
		}

		$gioitinh = isset( $data['GioiTinh'] ) ? sanitize_text_field( $data['GioiTinh'] ) : '';
		if ( ! in_array( $gioitinh, array( 'Nam', 'Nu', 'Khac' ), true ) ) {
			$errors[] = __( 'Giới tính không hợp lệ.', 'quanlicb' );
		}

		$phongban = isset( $data['PhongBan'] ) ? self::normalize_text( $data['PhongBan'] ) : '';
		if ( empty( $phongban ) ) {
			$errors[] = __( 'Phòng ban không được để trống.', 'quanlicb' );
		} elseif ( mb_strlen( $phongban ) < 2 || mb_strlen( $phongban ) > 100 ) {
			$errors[] = __( 'Phòng ban phải từ 2 đến 100 ký tự.', 'quanlicb' );
		} elseif ( ! preg_match( "/^[\p{L}\p{N}\s&().,-]+$/u", $phongban ) ) {
			$errors[] = __( 'Phòng ban chứa ký tự không hợp lệ.', 'quanlicb' );
		} elseif ( class_exists( 'QuanLiCB_PhongBan' ) && ! in_array( $phongban, QuanLiCB_PhongBan::names(), true ) ) {
			$errors[] = __( 'Phòng ban không tồn tại trong danh mục.', 'quanlicb' );
		}

		$chucvu = isset( $data['ChucVu'] ) ? self::normalize_text( $data['ChucVu'] ) : '';
		if ( empty( $chucvu ) ) {
			$errors[] = __( 'Chức vụ không được để trống.', 'quanlicb' );
		} elseif ( mb_strlen( $chucvu ) < 2 || mb_strlen( $chucvu ) > 100 ) {
			$errors[] = __( 'Chức vụ phải từ 2 đến 100 ký tự.', 'quanlicb' );
		} elseif ( class_exists( 'QuanLiCB_ChucVu' ) && ! in_array( $chucvu, QuanLiCB_ChucVu::names(), true ) ) {
			$errors[] = __( 'Chức vụ không tồn tại trong danh mục.', 'quanlicb' );
		}

		$hesoluong = isset( $data['HeSoLuong'] ) ? (float) $data['HeSoLuong'] : 0;
		if ( $hesoluong <= 0 ) {
			$errors[] = __( 'Hệ số lương phải lớn hơn 0.', 'quanlicb' );
		} elseif ( $hesoluong > 99.99 ) {
			$errors[] = __( 'Hệ số lương quá lớn.', 'quanlicb' );
		} elseif ( round( $hesoluong, 2 ) !== $hesoluong ) {
			$errors[] = __( 'Hệ số lương chỉ được tối đa 2 chữ số thập phân.', 'quanlicb' );
		}

		$luongcoban = isset( $data['LuongCoBan'] ) ? (float) $data['LuongCoBan'] : 0;
		if ( $luongcoban <= 0 ) {
			$errors[] = __( 'Lương cơ bản phải lớn hơn 0.', 'quanlicb' );
		} elseif ( $luongcoban > 999999999999999 ) {
			$errors[] = __( 'Lương cơ bản vượt quá giới hạn cho phép.', 'quanlicb' );
		} elseif ( floor( $luongcoban ) !== $luongcoban ) {
			$errors[] = __( 'Lương cơ bản phải là số nguyên theo VND.', 'quanlicb' );
		}

		$anh_id = isset( $data['AnhDaiDien'] ) ? absint( $data['AnhDaiDien'] ) : 0;
		if ( $anh_id ) {
			$mime = get_post_mime_type( $anh_id );
			if ( empty( $mime ) || 0 !== strpos( $mime, 'image/' ) ) {
				$errors[] = __( 'Ảnh đại diện không hợp lệ.', 'quanlicb' );
			}
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}
}

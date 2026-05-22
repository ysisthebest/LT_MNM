<?php
/**
 * CRUD phòng ban.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuanLiCB_PhongBan {

	/**
	 * Chuẩn hóa chuỗi.
	 */
	protected static function normalize_text( $value ) {
		$value = sanitize_text_field( (string) $value );
		return trim( preg_replace( '/\s+/u', ' ', $value ) );
	}

	/**
	 * Tạo mã phòng ban từ tên.
	 */
	public static function generate_code( $name ) {
		$slug = remove_accents( self::normalize_text( $name ) );
		$slug = strtoupper( preg_replace( '/[^A-Za-z0-9]+/', '_', $slug ) );
		$slug = trim( $slug, '_' );
		return substr( $slug ? $slug : 'PHONG_BAN', 0, 30 );
	}

	/**
	 * Lấy danh sách phòng ban.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function all() {
		global $wpdb;
		$table = QuanLiCB_Database::department_table_name();
		return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY TenPhongBan ASC", ARRAY_A );
	}

	/**
	 * Lấy một phòng ban theo ID.
	 */
	public static function get( $id ) {
		global $wpdb;
		$table = QuanLiCB_Database::department_table_name();
		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$table} WHERE ID = %d", $id ),
			ARRAY_A
		);
	}

	/**
	 * Kiểm tra tồn tại theo tên.
	 */
	public static function exists_name( $name, $exclude_id = 0 ) {
		global $wpdb;
		$table = QuanLiCB_Database::department_table_name();
		$sql   = "SELECT COUNT(*) FROM {$table} WHERE TenPhongBan = %s";
		$args  = array( self::normalize_text( $name ) );
		if ( $exclude_id > 0 ) {
			$sql   .= ' AND ID != %d';
			$args[] = $exclude_id;
		}
		return (int) $wpdb->get_var( $wpdb->prepare( $sql, $args ) ) > 0;
	}

	/**
	 * Danh sách tên phòng ban.
	 *
	 * @return string[]
	 */
	public static function names() {
		global $wpdb;
		$table = QuanLiCB_Database::department_table_name();
		return $wpdb->get_col( "SELECT TenPhongBan FROM {$table} ORDER BY TenPhongBan ASC" );
	}

	/**
	 * Số cán bộ của phòng ban.
	 */
	public static function count_can_bo( $name ) {
		global $wpdb;
		$table = QuanLiCB_Database::table_name();
		return (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE PhongBan = %s", self::normalize_text( $name ) )
		);
	}

	/**
	 * Tạo mới phòng ban.
	 */
	public static function create( $data ) {
		global $wpdb;
		$table = QuanLiCB_Database::department_table_name();
		$name  = self::normalize_text( $data['TenPhongBan'] ?? '' );
		$code  = self::generate_code( $name );
		$desc  = self::normalize_text( $data['MoTa'] ?? '' );

		return $wpdb->insert(
			$table,
			array(
				'MaPhongBan'  => $code,
				'TenPhongBan' => $name,
				'MoTa'        => $desc,
			),
			array( '%s', '%s', '%s' )
		);
	}

	/**
	 * Cập nhật phòng ban và đồng bộ tên qua bảng cán bộ.
	 */
	public static function update( $id, $data ) {
		global $wpdb;
		$table      = QuanLiCB_Database::department_table_name();
		$old        = self::get( $id );
		$name       = self::normalize_text( $data['TenPhongBan'] ?? '' );
		$code       = self::generate_code( $name );
		$desc       = self::normalize_text( $data['MoTa'] ?? '' );
		$department = array(
			'MaPhongBan'  => $code,
			'TenPhongBan' => $name,
			'MoTa'        => $desc,
		);

		$result = $wpdb->update( $table, $department, array( 'ID' => $id ), array( '%s', '%s', '%s' ), array( '%d' ) );
		if ( false === $result || ! $old || $old['TenPhongBan'] === $name ) {
			return $result;
		}

		$wpdb->update(
			QuanLiCB_Database::table_name(),
			array( 'PhongBan' => $name ),
			array( 'PhongBan' => $old['TenPhongBan'] ),
			array( '%s' ),
			array( '%s' )
		);

		return $result;
	}

	/**
	 * Xóa phòng ban nếu chưa có cán bộ gắn vào.
	 */
	public static function delete( $id ) {
		global $wpdb;
		$item = self::get( $id );
		if ( ! $item ) {
			return false;
		}

		if ( self::count_can_bo( $item['TenPhongBan'] ) > 0 ) {
			return new WP_Error( 'department_has_staff', __( 'Không thể xóa phòng ban đang có cán bộ.', 'quanlicb' ) );
		}

		return $wpdb->delete( QuanLiCB_Database::department_table_name(), array( 'ID' => $id ), array( '%d' ) );
	}

	/**
	 * Validate dữ liệu phòng ban.
	 *
	 * @return array{valid: bool, errors: string[]}
	 */
	public static function validate( $data, $exclude_id = 0 ) {
		$errors = array();
		$name   = self::normalize_text( $data['TenPhongBan'] ?? '' );
		$desc   = self::normalize_text( $data['MoTa'] ?? '' );

		if ( '' === $name ) {
			$errors[] = __( 'Tên phòng ban không được để trống.', 'quanlicb' );
		} elseif ( mb_strlen( $name ) < 2 || mb_strlen( $name ) > 100 ) {
			$errors[] = __( 'Tên phòng ban phải từ 2 đến 100 ký tự.', 'quanlicb' );
		} elseif ( self::exists_name( $name, $exclude_id ) ) {
			$errors[] = __( 'Tên phòng ban đã tồn tại.', 'quanlicb' );
		}

		if ( '' !== $desc && mb_strlen( $desc ) > 255 ) {
			$errors[] = __( 'Mô tả phòng ban tối đa 255 ký tự.', 'quanlicb' );
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Seed phòng ban mặc định từ dữ liệu có sẵn.
	 */
	public static function maybe_seed_defaults() {
		$defaults = array( 'Kế toán', 'Nhân sự', 'Kỹ thuật', 'Hành chính' );
		foreach ( $defaults as $name ) {
			if ( ! self::exists_name( $name ) ) {
				self::create(
					array(
						'TenPhongBan' => $name,
						'MoTa'        => '',
					)
				);
			}
		}

		global $wpdb;
		$cb_table = QuanLiCB_Database::table_name();
		$names    = $wpdb->get_col( "SELECT DISTINCT PhongBan FROM {$cb_table} WHERE PhongBan <> '' ORDER BY PhongBan ASC" );
		foreach ( $names as $name ) {
			if ( ! self::exists_name( $name ) ) {
				self::create(
					array(
						'TenPhongBan' => $name,
						'MoTa'        => '',
					)
				);
			}
		}
	}
}

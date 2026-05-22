<?php
/**
 * CRUD chức vụ.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuanLiCB_ChucVu {

	/**
	 * Chuẩn hóa chuỗi.
	 */
	protected static function normalize_text( $value ) {
		$value = sanitize_text_field( (string) $value );
		return trim( preg_replace( '/\s+/u', ' ', $value ) );
	}

	/**
	 * Tạo mã chức vụ từ tên.
	 */
	public static function generate_code( $name ) {
		$slug = remove_accents( self::normalize_text( $name ) );
		$slug = strtoupper( preg_replace( '/[^A-Za-z0-9]+/', '_', $slug ) );
		$slug = trim( $slug, '_' );
		return substr( $slug ? $slug : 'CHUC_VU', 0, 30 );
	}

	/**
	 * Lấy danh sách chức vụ.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function all() {
		global $wpdb;
		$table = QuanLiCB_Database::position_table_name();
		return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY TenChucVu ASC", ARRAY_A );
	}

	/**
	 * Lấy một chức vụ theo ID.
	 */
	public static function get( $id ) {
		global $wpdb;
		$table = QuanLiCB_Database::position_table_name();
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
		$table = QuanLiCB_Database::position_table_name();
		$sql   = "SELECT COUNT(*) FROM {$table} WHERE TenChucVu = %s";
		$args  = array( self::normalize_text( $name ) );
		if ( $exclude_id > 0 ) {
			$sql   .= ' AND ID != %d';
			$args[] = $exclude_id;
		}
		return (int) $wpdb->get_var( $wpdb->prepare( $sql, $args ) ) > 0;
	}

	/**
	 * Danh sách tên chức vụ.
	 *
	 * @return string[]
	 */
	public static function names() {
		global $wpdb;
		$table = QuanLiCB_Database::position_table_name();
		return $wpdb->get_col( "SELECT TenChucVu FROM {$table} ORDER BY TenChucVu ASC" );
	}

	/**
	 * Số cán bộ của chức vụ.
	 */
	public static function count_can_bo( $name ) {
		global $wpdb;
		$table = QuanLiCB_Database::table_name();
		return (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE ChucVu = %s", self::normalize_text( $name ) )
		);
	}

	/**
	 * Tạo mới chức vụ.
	 */
	public static function create( $data ) {
		global $wpdb;
		$table = QuanLiCB_Database::position_table_name();
		$name  = self::normalize_text( $data['TenChucVu'] ?? '' );
		$code  = self::generate_code( $name );
		$desc  = self::normalize_text( $data['MoTa'] ?? '' );

		return $wpdb->insert(
			$table,
			array(
				'MaChucVu'  => $code,
				'TenChucVu' => $name,
				'MoTa'      => $desc,
			),
			array( '%s', '%s', '%s' )
		);
	}

	/**
	 * Cập nhật chức vụ và đồng bộ tên qua bảng cán bộ.
	 */
	public static function update( $id, $data ) {
		global $wpdb;
		$table    = QuanLiCB_Database::position_table_name();
		$old      = self::get( $id );
		$name     = self::normalize_text( $data['TenChucVu'] ?? '' );
		$code     = self::generate_code( $name );
		$desc     = self::normalize_text( $data['MoTa'] ?? '' );
		$position = array(
			'MaChucVu'  => $code,
			'TenChucVu' => $name,
			'MoTa'      => $desc,
		);

		$result = $wpdb->update( $table, $position, array( 'ID' => $id ), array( '%s', '%s', '%s' ), array( '%d' ) );
		if ( false === $result || ! $old || $old['TenChucVu'] === $name ) {
			return $result;
		}

		$wpdb->update(
			QuanLiCB_Database::table_name(),
			array( 'ChucVu' => $name ),
			array( 'ChucVu' => $old['TenChucVu'] ),
			array( '%s' ),
			array( '%s' )
		);

		return $result;
	}

	/**
	 * Xóa chức vụ nếu chưa có cán bộ gắn vào.
	 */
	public static function delete( $id ) {
		global $wpdb;
		$item = self::get( $id );
		if ( ! $item ) {
			return false;
		}

		if ( self::count_can_bo( $item['TenChucVu'] ) > 0 ) {
			return new WP_Error( 'position_has_staff', __( 'Không thể xóa chức vụ đang có cán bộ.', 'quanlicb' ) );
		}

		return $wpdb->delete( QuanLiCB_Database::position_table_name(), array( 'ID' => $id ), array( '%d' ) );
	}

	/**
	 * Validate dữ liệu chức vụ.
	 *
	 * @return array{valid: bool, errors: string[]}
	 */
	public static function validate( $data, $exclude_id = 0 ) {
		$errors = array();
		$name   = self::normalize_text( $data['TenChucVu'] ?? '' );
		$desc   = self::normalize_text( $data['MoTa'] ?? '' );

		if ( '' === $name ) {
			$errors[] = __( 'Tên chức vụ không được để trống.', 'quanlicb' );
		} elseif ( mb_strlen( $name ) < 2 || mb_strlen( $name ) > 100 ) {
			$errors[] = __( 'Tên chức vụ phải từ 2 đến 100 ký tự.', 'quanlicb' );
		} elseif ( self::exists_name( $name, $exclude_id ) ) {
			$errors[] = __( 'Tên chức vụ đã tồn tại.', 'quanlicb' );
		}

		if ( '' !== $desc && mb_strlen( $desc ) > 255 ) {
			$errors[] = __( 'Mô tả chức vụ tối đa 255 ký tự.', 'quanlicb' );
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Seed chức vụ mặc định.
	 */
	public static function maybe_seed_defaults() {
		$defaults = array( 'Nhân viên', 'Phó phòng', 'Trưởng phòng', 'Giám đốc' );
		foreach ( $defaults as $name ) {
			if ( ! self::exists_name( $name ) ) {
				self::create(
					array(
						'TenChucVu' => $name,
						'MoTa'      => '',
					)
				);
			}
		}

		global $wpdb;
		$cb_table = QuanLiCB_Database::table_name();
		$names    = $wpdb->get_col( "SELECT DISTINCT ChucVu FROM {$cb_table} WHERE ChucVu <> '' ORDER BY ChucVu ASC" );
		foreach ( $names as $name ) {
			if ( ! self::exists_name( $name ) ) {
				self::create(
					array(
						'TenChucVu' => $name,
						'MoTa'      => '',
					)
				);
			}
		}
	}
}

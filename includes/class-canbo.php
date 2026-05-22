<?php
/**
 * CRUD cán bộ - TongLuong = HeSoLuong * LuongCoBan.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuanLiCB_CanBo {

	const PER_PAGE = 10;

	/**
	 * Chuan hoa chuoi nhap lieu truoc khi luu.
	 */
	protected static function normalize_text( $value ) {
		$value = sanitize_text_field( (string) $value );
		return trim( preg_replace( '/\s+/u', ' ', $value ) );
	}

	/**
	 * Chuẩn hóa dữ liệu hàng cán bộ.
	 */
	protected static function sanitize_row( $data ) {
		return array(
			'MaCB'       => isset( $data['MaCB'] ) ? strtoupper( self::normalize_text( $data['MaCB'] ) ) : '',
			'HoTen'      => isset( $data['HoTen'] ) ? self::normalize_text( $data['HoTen'] ) : '',
			'NgaySinh'   => isset( $data['NgaySinh'] ) ? sanitize_text_field( $data['NgaySinh'] ) : '',
			'GioiTinh'   => isset( $data['GioiTinh'] ) ? sanitize_text_field( $data['GioiTinh'] ) : '',
			'PhongBan'   => isset( $data['PhongBan'] ) ? self::normalize_text( $data['PhongBan'] ) : '',
			'ChucVu'     => isset( $data['ChucVu'] ) ? self::normalize_text( $data['ChucVu'] ) : '',
			'HeSoLuong'  => isset( $data['HeSoLuong'] ) ? (float) $data['HeSoLuong'] : 0,
			'LuongCoBan' => isset( $data['LuongCoBan'] ) ? (float) $data['LuongCoBan'] : 0,
			'AnhDaiDien' => isset( $data['AnhDaiDien'] ) ? absint( $data['AnhDaiDien'] ) : 0,
		);
	}

	/**
	 * Kiểm tra mã cán bộ đã tồn tại.
	 */
	public static function exists( $macb ) {
		global $wpdb;
		$table = QuanLiCB_Database::table_name();
		return (bool) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE MaCB = %s", $macb )
		);
	}

	/**
	 * Tính tổng lương.
	 */
	public static function calc_tong_luong( $hesoluong, $luongcoban ) {
		return round( floatval( $hesoluong ) * floatval( $luongcoban ), 0 );
	}

	/**
	 * Lấy một cán bộ theo mã.
	 */
	public static function get( $macb ) {
		global $wpdb;
		$table = QuanLiCB_Database::table_name();
		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$table} WHERE MaCB = %s", $macb ),
			ARRAY_A
		);
	}

	/**
	 * Danh sách có tìm kiếm, lọc, phân trang.
	 *
	 * @param array $args macb, hoten, phongban, chucvu, gioitinh, paged.
	 * @return array{items: array, total: int, pages: int}
	 */
	public static function list( $args = array() ) {
		global $wpdb;
		$table = QuanLiCB_Database::table_name();

		$where  = array( '1=1' );
		$params = array();

		if ( ! empty( $args['macb'] ) ) {
			$where[]  = 'MaCB LIKE %s';
			$params[] = '%' . $wpdb->esc_like( sanitize_text_field( $args['macb'] ) ) . '%';
		}
		if ( ! empty( $args['hoten'] ) ) {
			$where[]  = 'HoTen LIKE %s';
			$params[] = '%' . $wpdb->esc_like( sanitize_text_field( $args['hoten'] ) ) . '%';
		}
		if ( ! empty( $args['phongban'] ) ) {
			$where[]  = 'PhongBan = %s';
			$params[] = sanitize_text_field( $args['phongban'] );
		}
		if ( ! empty( $args['chucvu'] ) ) {
			$where[]  = 'ChucVu = %s';
			$params[] = sanitize_text_field( $args['chucvu'] );
		}
		if ( ! empty( $args['gioitinh'] ) ) {
			$where[]  = 'GioiTinh = %s';
			$params[] = sanitize_text_field( $args['gioitinh'] );
		}
		if ( isset( $args['luong_min'] ) && '' !== $args['luong_min'] ) {
			$where[]  = 'TongLuong >= %f';
			$params[] = max( 0, (float) $args['luong_min'] );
		}
		if ( isset( $args['luong_max'] ) && '' !== $args['luong_max'] ) {
			$where[]  = 'TongLuong <= %f';
			$params[] = max( 0, (float) $args['luong_max'] );
		}

		$where_sql = implode( ' AND ', $where );

		$allowed_orderby = array(
			'macb'       => 'MaCB',
			'hoten'      => 'HoTen',
			'ngaysinh'   => 'NgaySinh',
			'phongban'   => 'PhongBan',
			'chucvu'     => 'ChucVu',
			'tongluong'  => 'TongLuong',
		);
		$orderby_key = isset( $args['orderby'] ) ? sanitize_key( $args['orderby'] ) : 'macb';
		$orderby     = isset( $allowed_orderby[ $orderby_key ] ) ? $allowed_orderby[ $orderby_key ] : 'MaCB';
		$order       = ( isset( $args['order'] ) && 'desc' === strtolower( (string) $args['order'] ) ) ? 'DESC' : 'ASC';

		$count_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where_sql}";
		if ( $params ) {
			$count_sql = $wpdb->prepare( $count_sql, $params );
		}
		$total = (int) $wpdb->get_var( $count_sql );

		$sum_sql = "SELECT COALESCE(SUM(TongLuong), 0) FROM {$table} WHERE {$where_sql}";
		if ( $params ) {
			$sum_sql = $wpdb->prepare( $sum_sql, $params );
		}
		$total_luong = (float) $wpdb->get_var( $sum_sql );

		$per_page = isset( $args['per_page'] ) ? max( 1, (int) $args['per_page'] ) : self::PER_PAGE;
		$paged    = isset( $args['paged'] ) ? max( 1, (int) $args['paged'] ) : 1;
		$offset   = ( $paged - 1 ) * $per_page;
		$pages    = max( 1, (int) ceil( $total / $per_page ) );

		$list_sql   = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";
		$all_params = array_merge( $params, array( $per_page, $offset ) );
		$list_sql   = $wpdb->prepare( $list_sql, $all_params );
		$items      = $wpdb->get_results( $list_sql, ARRAY_A );

		return array(
			'items'       => $items ? $items : array(),
			'total'       => $total,
			'pages'       => $pages,
			'paged'       => $paged,
			'total_luong' => $total_luong,
			'orderby'     => $orderby_key,
			'order'       => strtolower( $order ),
		);
	}

	/**
	 * Danh sách phòng ban cho filter.
	 */
	public static function get_phong_ban_list() {
		if ( class_exists( 'QuanLiCB_PhongBan' ) ) {
			return QuanLiCB_PhongBan::names();
		}
		global $wpdb;
		$table = QuanLiCB_Database::table_name();
		return $wpdb->get_col( "SELECT DISTINCT PhongBan FROM {$table} WHERE PhongBan <> '' ORDER BY PhongBan ASC" );
	}

	/**
	 * Danh sách chức vụ cho filter.
	 */
	public static function get_chuc_vu_list() {
		if ( class_exists( 'QuanLiCB_ChucVu' ) ) {
			return QuanLiCB_ChucVu::names();
		}
		global $wpdb;
		$table = QuanLiCB_Database::table_name();
		return $wpdb->get_col( "SELECT DISTINCT ChucVu FROM {$table} WHERE ChucVu <> '' ORDER BY ChucVu ASC" );
	}

	/**
	 * Thêm cán bộ.
	 */
	public static function create( $data ) {
		global $wpdb;
		$table = QuanLiCB_Database::table_name();

		$data = self::sanitize_row( $data );
		$tong = self::calc_tong_luong( $data['HeSoLuong'], $data['LuongCoBan'] );

		$row = array(
			'MaCB'       => $data['MaCB'],
			'HoTen'      => $data['HoTen'],
			'NgaySinh'   => $data['NgaySinh'],
			'GioiTinh'   => $data['GioiTinh'],
			'PhongBan'   => $data['PhongBan'],
			'ChucVu'     => $data['ChucVu'],
			'HeSoLuong'  => $data['HeSoLuong'],
			'LuongCoBan' => $data['LuongCoBan'],
			'TongLuong'  => $tong,
		);
		$fmt = array( '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%f' );
		if ( ! empty( $data['AnhDaiDien'] ) ) {
			$row['AnhDaiDien'] = $data['AnhDaiDien'];
			$fmt[]             = '%d';
		}
		return $wpdb->insert( $table, $row, $fmt );
	}

	/**
	 * Cập nhật cán bộ (có thể đổi MaCB).
	 */
	public static function update( $old_macb, $data ) {
		global $wpdb;
		$table = QuanLiCB_Database::table_name();

		$data = self::sanitize_row( $data );
		$tong = self::calc_tong_luong( $data['HeSoLuong'], $data['LuongCoBan'] );

		$row = array(
			'MaCB'       => $data['MaCB'],
			'HoTen'      => $data['HoTen'],
			'NgaySinh'   => $data['NgaySinh'],
			'GioiTinh'   => $data['GioiTinh'],
			'PhongBan'   => $data['PhongBan'],
			'ChucVu'     => $data['ChucVu'],
			'HeSoLuong'  => $data['HeSoLuong'],
			'LuongCoBan' => $data['LuongCoBan'],
			'TongLuong'  => $tong,
			'AnhDaiDien' => ! empty( $data['AnhDaiDien'] ) ? $data['AnhDaiDien'] : null,
		);
		return $wpdb->update(
			$table,
			$row,
			array( 'MaCB' => $old_macb ),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%f', '%d' ),
			array( '%s' )
		);
	}

	/**
	 * Lay dữ liệu de xuat CSV theo bộ lọc hien tai.
	 *
	 * @param array $args Bỏ lọc nhu ham list().
	 * @return array<int, array<string, mixed>>
	 */
	public static function export_items( $args = array() ) {
		$args['per_page'] = 999999;
		$args['paged']    = 1;
		$result           = self::list( $args );
		return $result['items'];
	}

	/**
	 * Import tu CSV.
	 *
	 * @return array{created:int,updated:int,errors:string[]}
	 */
	public static function import_from_csv( $csv_file_path ) {
		$created = 0;
		$updated = 0;
		$errors  = array();

		if ( ! file_exists( $csv_file_path ) || ! is_readable( $csv_file_path ) ) {
			return array(
				'created' => 0,
				'updated' => 0,
				'errors'  => array( __( 'Không thể đọc tệp CSV.', 'quanlicb' ) ),
			);
		}

		$handle = fopen( $csv_file_path, 'r' );
		if ( false === $handle ) {
			return array(
				'created' => 0,
				'updated' => 0,
				'errors'  => array( __( 'Không thể mở tệp CSV.', 'quanlicb' ) ),
			);
		}

		$header_raw = fgetcsv( $handle );
		if ( ! is_array( $header_raw ) ) {
			fclose( $handle );
			return array(
				'created' => 0,
				'updated' => 0,
				'errors'  => array( __( 'Tệp CSV rỗng hoặc sai định dạng.', 'quanlicb' ) ),
			);
		}

		$header = array();
		foreach ( $header_raw as $col ) {
			$col      = preg_replace( '/^\xEF\xBB\xBF/', '', (string) $col );
			$header[] = strtolower( trim( $col ) );
		}

		$map = array(
			'macb'       => 'MaCB',
			'hoten'      => 'HoTen',
			'ngaysinh'   => 'NgaySinh',
			'gioitinh'   => 'GioiTinh',
			'phongban'   => 'PhongBan',
			'chucvu'     => 'ChucVu',
			'hesoluong'  => 'HeSoLuong',
			'luongcoban' => 'LuongCoBan',
			'anhedaidien'=> 'AnhDaiDien',
		);

		$row_num = 1;
		while ( ( $row = fgetcsv( $handle ) ) !== false ) {
			++$row_num;
			if ( empty( array_filter( $row, static function ( $val ) { return '' !== trim( (string) $val ); } ) ) ) {
				continue;
			}

			$assoc = array();
			foreach ( $header as $index => $key ) {
				if ( isset( $map[ $key ] ) ) {
					$assoc[ $map[ $key ] ] = isset( $row[ $index ] ) ? trim( (string) $row[ $index ] ) : '';
				}
			}

			$assoc['GioiTinh'] = $assoc['GioiTinh'] ?? 'Nam';
			$assoc['ChucVu']   = $assoc['ChucVu'] ?? '';

			$mode    = self::exists( $assoc['MaCB'] ?? '' ) ? 'update' : 'create';
			$old_macb = $assoc['MaCB'] ?? '';
			$valid   = QuanLiCB_Validator::validate( $assoc, $mode, $old_macb );
			if ( ! $valid['valid'] ) {
				$errors[] = sprintf( __( 'Dòng %1$d: %2$s', 'quanlicb' ), $row_num, implode( '; ', $valid['errors'] ) );
				continue;
			}

			if ( 'create' === $mode ) {
				$ok = self::create( $assoc );
				if ( $ok ) {
					++$created;
				} else {
					$errors[] = sprintf( __( 'Dòng %d: không thể thêm dữ liệu.', 'quanlicb' ), $row_num );
				}
			} else {
				$ok = self::update( $old_macb, $assoc );
				if ( false !== $ok ) {
					++$updated;
				} else {
					$errors[] = sprintf( __( 'Dòng %d: không thể cập nhật dữ liệu.', 'quanlicb' ), $row_num );
				}
			}
		}

		fclose( $handle );

		return array(
			'created' => $created,
			'updated' => $updated,
			'errors'  => $errors,
		);
	}

	/**
	 * Xóa cán bộ.
	 */
	public static function delete( $macb ) {
		global $wpdb;
		$table = QuanLiCB_Database::table_name();
		return $wpdb->delete( $table, array( 'MaCB' => sanitize_text_field( $macb ) ), array( '%s' ) );
	}
}

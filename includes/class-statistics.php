<?php
/**
 * Thống kê tổng hợp.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuanLiCB_Statistics {

	/**
	 * Tổng số cán bộ.
	 */
	public static function total_can_bo() {
		global $wpdb;
		$table = QuanLiCB_Database::table_name();
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
	}

	/**
	 * Tổng quỹ lương.
	 */
	public static function total_quy_luong() {
		global $wpdb;
		$table = QuanLiCB_Database::table_name();
		return (float) $wpdb->get_var( "SELECT COALESCE(SUM(TongLuong), 0) FROM {$table}" );
	}

	/**
	 * Số lượng theo phòng ban.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function by_phong_ban() {
		global $wpdb;
		$table = QuanLiCB_Database::table_name();
		return $wpdb->get_results(
			"SELECT PhongBan, COUNT(*) AS so_luong FROM {$table} GROUP BY PhongBan ORDER BY so_luong DESC",
			ARRAY_A
		);
	}

	/**
	 * Số lượng theo chức vụ.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function by_chuc_vu() {
		global $wpdb;
		$table = QuanLiCB_Database::table_name();
		return $wpdb->get_results(
			"SELECT ChucVu, COUNT(*) AS so_luong FROM {$table} GROUP BY ChucVu ORDER BY so_luong DESC",
			ARRAY_A
		);
	}

	/**
	 * Số lượng theo giới tính.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function by_gioi_tinh() {
		global $wpdb;
		$table = QuanLiCB_Database::table_name();
		return $wpdb->get_results(
			"SELECT GioiTinh, COUNT(*) AS so_luong FROM {$table} GROUP BY GioiTinh",
			ARRAY_A
		);
	}

	/**
	 * Lương trung bình.
	 */
	public static function average_tong_luong() {
		global $wpdb;
		$table = QuanLiCB_Database::table_name();
		return (float) $wpdb->get_var( "SELECT COALESCE(AVG(TongLuong), 0) FROM {$table}" );
	}

	/**
	 * Tổng số phòng ban trong danh mục.
	 */
	public static function total_phong_ban() {
		if ( class_exists( 'QuanLiCB_PhongBan' ) ) {
			return count( QuanLiCB_PhongBan::all() );
		}

		return count( self::by_phong_ban() );
	}

	/**
	 * Tổng số chức vụ trong danh mục.
	 */
	public static function total_chuc_vu() {
		if ( class_exists( 'QuanLiCB_ChucVu' ) ) {
			return count( QuanLiCB_ChucVu::all() );
		}

		return count( self::by_chuc_vu() );
	}

	/**
	 * Top cán bộ lương cao.
	 *
	 * @param int $limit Số dòng tối đa.
	 * @return array<int, array<string, mixed>>
	 */
	public static function top_luong( $limit = 5 ) {
		global $wpdb;
		$table = QuanLiCB_Database::table_name();
		$limit = max( 1, (int) $limit );
		$sql   = $wpdb->prepare(
			"SELECT MaCB, HoTen, PhongBan, ChucVu, TongLuong FROM {$table} ORDER BY TongLuong DESC LIMIT %d",
			$limit
		);

		return $wpdb->get_results( $sql, ARRAY_A );
	}

	/**
	 * Dữ liệu theo khoảng thời gian cập nhật.
	 *
	 * @param string $date_from Từ ngày.
	 * @param string $date_to   Đến ngày.
	 * @return array<int, array<string, mixed>>
	 */
	public static function by_updated_range( $date_from = '', $date_to = '' ) {
		global $wpdb;
		$table  = QuanLiCB_Database::table_name();
		$where  = array( '1=1' );
		$params = array();

		if ( '' !== $date_from ) {
			$where[]  = 'DATE(updated_at) >= %s';
			$params[] = sanitize_text_field( $date_from );
		}

		if ( '' !== $date_to ) {
			$where[]  = 'DATE(updated_at) <= %s';
			$params[] = sanitize_text_field( $date_to );
		}

		$where_sql = implode( ' AND ', $where );
		$sql       = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY updated_at DESC, MaCB ASC";
		if ( $params ) {
			$sql = $wpdb->prepare( $sql, $params );
		}

		return $wpdb->get_results( $sql, ARRAY_A );
	}

	/**
	 * Tổng lương theo phòng ban trong khoảng thời gian.
	 *
	 * @param string $date_from Từ ngày.
	 * @param string $date_to   Đến ngày.
	 * @return array<int, array<string, mixed>>
	 */
	public static function salary_by_phong_ban_range( $date_from = '', $date_to = '' ) {
		global $wpdb;
		$table  = QuanLiCB_Database::table_name();
		$where  = array( '1=1' );
		$params = array();

		if ( '' !== $date_from ) {
			$where[]  = 'DATE(updated_at) >= %s';
			$params[] = sanitize_text_field( $date_from );
		}

		if ( '' !== $date_to ) {
			$where[]  = 'DATE(updated_at) <= %s';
			$params[] = sanitize_text_field( $date_to );
		}

		$where_sql = implode( ' AND ', $where );
		$sql       = "SELECT PhongBan, COUNT(*) AS so_luong, COALESCE(SUM(TongLuong), 0) AS tong_luong FROM {$table} WHERE {$where_sql} GROUP BY PhongBan ORDER BY tong_luong DESC";
		if ( $params ) {
			$sql = $wpdb->prepare( $sql, $params );
		}

		return $wpdb->get_results( $sql, ARRAY_A );
	}
}

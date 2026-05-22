<?php
/**
 * Tạo và quản lý các bảng dữ liệu.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuanLiCB_Database {

	const TABLE            = 'tblCanBo';
	const DEPARTMENT_TABLE = 'tblPhongBan';
	const POSITION_TABLE   = 'tblChucVu';
	const LOG_TABLE        = 'tblNhatKy';

	/**
	 * Kích hoạt plugin: tạo bảng + quyền.
	 */
	public static function activate() {
		global $wpdb;
		$table            = self::table_name();
		$department_table = self::department_table_name();
		$position_table   = self::position_table_name();
		$log_table        = self::log_table_name();
		$charset          = $wpdb->get_charset_collate();

		$sql_can_bo = "CREATE TABLE {$table} (
			MaCB varchar(20) NOT NULL,
			HoTen varchar(255) NOT NULL,
			NgaySinh date NOT NULL,
			GioiTinh varchar(10) NOT NULL DEFAULT 'Nam',
			PhongBan varchar(100) NOT NULL,
			ChucVu varchar(100) NOT NULL DEFAULT '',
			HeSoLuong decimal(5,2) NOT NULL DEFAULT 1.00,
			LuongCoBan decimal(15,0) NOT NULL DEFAULT 0,
			TongLuong decimal(15,0) NOT NULL DEFAULT 0,
			AnhDaiDien bigint(20) unsigned DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (MaCB)
		) {$charset};";

		$sql_department = "CREATE TABLE {$department_table} (
			ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			MaPhongBan varchar(30) NOT NULL,
			TenPhongBan varchar(100) NOT NULL,
			MoTa varchar(255) DEFAULT '',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			UNIQUE KEY unique_department_code (MaPhongBan),
			UNIQUE KEY unique_department_name (TenPhongBan)
		) {$charset};";

		$sql_position = "CREATE TABLE {$position_table} (
			ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			MaChucVu varchar(30) NOT NULL,
			TenChucVu varchar(100) NOT NULL,
			MoTa varchar(255) DEFAULT '',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			UNIQUE KEY unique_position_code (MaChucVu),
			UNIQUE KEY unique_position_name (TenChucVu)
		) {$charset};";

		$sql_log = "CREATE TABLE {$log_table} (
			ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL DEFAULT 0,
			username varchar(60) NOT NULL DEFAULT '',
			action varchar(50) NOT NULL,
			object_type varchar(50) NOT NULL,
			object_id varchar(50) NOT NULL DEFAULT '',
			message varchar(255) NOT NULL DEFAULT '',
			payload longtext NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
			KEY idx_action (action),
			KEY idx_object_type (object_type),
			KEY idx_created_at (created_at)
		) {$charset};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql_can_bo );
		dbDelta( $sql_department );
		dbDelta( $sql_position );
		dbDelta( $sql_log );

		self::maybe_add_columns();

		if ( class_exists( 'QuanLiCB_PhongBan' ) ) {
			QuanLiCB_PhongBan::maybe_seed_defaults();
		}
		if ( class_exists( 'QuanLiCB_ChucVu' ) ) {
			QuanLiCB_ChucVu::maybe_seed_defaults();
		}
		if ( class_exists( 'QuanLiCB_Permissions' ) ) {
			QuanLiCB_Permissions::setup_roles();
		}
		update_option( 'quanlicb_db_version', QUANLICB_VERSION );
	}

	/**
	 * Bo sung cot cho he thong cu.
	 */
	protected static function maybe_add_columns() {
		global $wpdb;
		$table = self::table_name();

		$chucvu_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW COLUMNS FROM ' . $table . ' LIKE %s', 'ChucVu' ) );
		if ( ! $chucvu_exists ) {
			$wpdb->query( 'ALTER TABLE ' . $table . " ADD COLUMN ChucVu varchar(100) NOT NULL DEFAULT '' AFTER PhongBan" );
		}

		// Chuẩn hóa giá trị giới tính từ dữ liệu cũ.
		$wpdb->query( "UPDATE {$table} SET GioiTinh = 'Nu' WHERE GioiTinh IN ('Nu', 'Nu')" );
		$wpdb->query( "UPDATE {$table} SET GioiTinh = 'Khac' WHERE GioiTinh IN ('Khac', 'Khac')" );
		$wpdb->query( "UPDATE {$table} SET GioiTinh = 'Nam' WHERE GioiTinh NOT IN ('Nam', 'Nu', 'Khac')" );
	}

	/**
	 * Tên bảng cán bộ có prefix.
	 */
	public static function table_name() {
		global $wpdb;
		return $wpdb->prefix . self::TABLE;
	}

	/**
	 * Ten bang phòng ban co prefix.
	 */
	public static function department_table_name() {
		global $wpdb;
		return $wpdb->prefix . self::DEPARTMENT_TABLE;
	}

	/**
	 * Ten bang chức vụ co prefix.
	 */
	public static function position_table_name() {
		global $wpdb;
		return $wpdb->prefix . self::POSITION_TABLE;
	}

	/**
	 * Ten bang nhat ky co prefix.
	 */
	public static function log_table_name() {
		global $wpdb;
		return $wpdb->prefix . self::LOG_TABLE;
	}
}

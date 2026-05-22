<?php
/**
 * Plugin Name: Quản lý Cán bộ
 * Description: Quản lý cán bộ (CRUD, tìm kiếm, thống kê, phân quyền) trên WordPress/PHP.
 * Version: 1.5.0
 * Author: Báo cáo Lập trình
 * Text Domain: quanlicb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'QUANLICB_VERSION', '1.5.0' );
define( 'QUANLICB_PATH', plugin_dir_path( __FILE__ ) );
define( 'QUANLICB_URL', plugin_dir_url( __FILE__ ) );

require_once QUANLICB_PATH . 'includes/class-database.php';
require_once QUANLICB_PATH . 'includes/class-phongban.php';
require_once QUANLICB_PATH . 'includes/class-chucvu.php';
require_once QUANLICB_PATH . 'includes/class-validator.php';
require_once QUANLICB_PATH . 'includes/class-canbo.php';
require_once QUANLICB_PATH . 'includes/class-permissions.php';
require_once QUANLICB_PATH . 'includes/class-statistics.php';
require_once QUANLICB_PATH . 'includes/class-audit-log.php';
require_once QUANLICB_PATH . 'includes/class-seed.php';
require_once QUANLICB_PATH . 'admin/class-admin.php';

register_activation_hook( __FILE__, 'quanlicb_activate' );

/**
 * Kích hoạt: tạo bảng, quyền, dữ liệu mẫu.
 */
function quanlicb_activate() {
	QuanLiCB_Database::activate();
	QuanLiCB_Seed::maybe_seed();
}
register_deactivation_hook( __FILE__, array( 'QuanLiCB_Permissions', 'deactivate' ) );

add_action( 'plugins_loaded', 'quanlicb_init' );

/**
 * Khởi tạo plugin.
 */
function quanlicb_init() {
	if ( get_option( 'quanlicb_db_version' ) !== QUANLICB_VERSION ) {
		QuanLiCB_Database::activate();
	}

	QuanLiCB_Permissions::init();

	if ( is_admin() ) {
		new QuanLiCB_Admin();
	}
}

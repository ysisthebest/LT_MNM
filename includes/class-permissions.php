<?php
/**
 * Phân quyền WordPress: Admin CRUD, Nhân viên chỉ xem.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuanLiCB_Permissions {
	const CAP_VIEW   = 'quanlicb_view';
	const CAP_EDIT   = 'quanlicb_edit';
	const CAP_DELETE = 'quanlicb_delete';
	const ROLE_NV    = 'quanlicb_nhanvien';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'ensure_caps' ) );
	}

	/**
	 * Tạo role và gán capability khi kích hoạt.
	 */
	public static function setup_roles() {
		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$admin->add_cap( self::CAP_VIEW );
			$admin->add_cap( self::CAP_EDIT );
			$admin->add_cap( self::CAP_DELETE );
		}

		remove_role( self::ROLE_NV );
		add_role(
			self::ROLE_NV,
			__( 'Nhân viên (Quản lý CB)', 'quanlicb' ),
			array(
				'read'           => true,
				self::CAP_VIEW   => true,
				self::CAP_EDIT   => false,
				self::CAP_DELETE => false,
			)
		);
	}

	/**
	 * Đảm bảo admin luôn có quyền sau cập nhật.
	 */
	public static function ensure_caps() {
		$admin = get_role( 'administrator' );
		if ( $admin && ! $admin->has_cap( self::CAP_VIEW ) ) {
			self::setup_roles();
		}
	}

	public static function deactivate() {
		// Giữ role khi deactivate để không mất user gán role.
	}

	public static function can_view() {
		return current_user_can( self::CAP_VIEW ) || current_user_can( 'manage_options' );
	}

	public static function can_edit() {
		return current_user_can( self::CAP_EDIT ) || current_user_can( 'manage_options' );
	}

	public static function can_delete() {
		return current_user_can( self::CAP_DELETE ) || current_user_can( 'manage_options' );
	}
}

<?php
/**
 * Giao diện quản trị WordPress.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuanLiCB_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menus' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_init', array( $this, 'handle_actions' ) );
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
	}

	/**
	 * Class body để scope CSS.
	 */
	public function admin_body_class( $classes ) {
		if ( isset( $_GET['page'] ) && 0 === strpos( sanitize_text_field( wp_unslash( $_GET['page'] ) ), 'quanlicb' ) ) {
			$classes .= ' quanlicb-admin-page';
		}
		return $classes;
	}

	public function register_menus() {
		if ( ! QuanLiCB_Permissions::can_view() ) {
			return;
		}

		add_menu_page(
			__( 'Quản lý Cán bộ', 'quanlicb' ),
			__( 'Quản lý CB', 'quanlicb' ),
			QuanLiCB_Permissions::CAP_VIEW,
			'quanlicb-dashboard',
			array( $this, 'page_dashboard' ),
			'dashicons-groups',
			26
		);

		add_submenu_page(
			'quanlicb-dashboard',
			__( 'Dashboard', 'quanlicb' ),
			__( 'Dashboard', 'quanlicb' ),
			QuanLiCB_Permissions::CAP_VIEW,
			'quanlicb-dashboard',
			array( $this, 'page_dashboard' )
		);

		add_submenu_page(
			'quanlicb-dashboard',
			__( 'Danh sách cán bộ', 'quanlicb' ),
			__( 'Danh sách', 'quanlicb' ),
			QuanLiCB_Permissions::CAP_VIEW,
			'quanlicb',
			array( $this, 'page_list' )
		);

		if ( QuanLiCB_Permissions::can_edit() ) {
			add_submenu_page(
				'quanlicb-dashboard',
				__( 'Thêm cán bộ', 'quanlicb' ),
				__( 'Thêm mới', 'quanlicb' ),
				QuanLiCB_Permissions::CAP_EDIT,
				'quanlicb-add',
				array( $this, 'page_add' )
			);

			add_submenu_page(
				'quanlicb-dashboard',
				__( 'Phòng ban', 'quanlicb' ),
				__( 'Phòng ban', 'quanlicb' ),
				QuanLiCB_Permissions::CAP_EDIT,
				'quanlicb-departments',
				array( $this, 'page_departments' )
			);

			add_submenu_page(
				'quanlicb-dashboard',
				__( 'Chức vụ', 'quanlicb' ),
				__( 'Chức vụ', 'quanlicb' ),
				QuanLiCB_Permissions::CAP_EDIT,
				'quanlicb-positions',
				array( $this, 'page_positions' )
			);

			add_submenu_page(
				'quanlicb-dashboard',
				__( 'Import CSV', 'quanlicb' ),
				__( 'Import CSV', 'quanlicb' ),
				QuanLiCB_Permissions::CAP_EDIT,
				'quanlicb-import',
				array( $this, 'page_import' )
			);
		}

		add_submenu_page(
			'quanlicb-dashboard',
			__( 'Thống kê & Báo cáo', 'quanlicb' ),
			__( 'Thống kê & Báo cáo', 'quanlicb' ),
			QuanLiCB_Permissions::CAP_VIEW,
			'quanlicb-stats',
			array( $this, 'page_stats' )
		);

		add_submenu_page(
			'quanlicb-dashboard',
			__( 'Nhật ký thao tác', 'quanlicb' ),
			__( 'Nhật ký', 'quanlicb' ),
			QuanLiCB_Permissions::CAP_VIEW,
			'quanlicb-logs',
			array( $this, 'page_logs' )
		);
	}

	public function enqueue_assets( $hook ) {
		if ( strpos( $hook, 'quanlicb' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'quanlicb-admin',
			QUANLICB_URL . 'assets/css/admin.css',
			array(),
			QUANLICB_VERSION
		);

		wp_enqueue_media();

		wp_enqueue_script(
			'chart-js',
			'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
			array(),
			'4.4.1',
			true
		);

		wp_enqueue_script(
			'quanlicb-admin',
			QUANLICB_URL . 'assets/js/admin.js',
			array( 'jquery', 'chart-js' ),
			QUANLICB_VERSION,
			true
		);

		wp_localize_script(
			'quanlicb-admin',
			'quanlicbAdmin',
			array(
				'calcLabel' => __( 'Tổng lương (tự động):', 'quanlicb' ),
			)
		);
	}

	/**
	 * Xử lý POST/GET action.
	 */
	public function handle_actions() {
		if ( ! isset( $_GET['page'] ) || strpos( sanitize_text_field( wp_unslash( $_GET['page'] ) ), 'quanlicb' ) !== 0 ) {
			return;
		}

		if ( 'quanlicb-reports' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {
			$redirect_args = array( 'page' => 'quanlicb-stats' );
			if ( isset( $_GET['date_from'] ) ) {
				$redirect_args['date_from'] = sanitize_text_field( wp_unslash( $_GET['date_from'] ) );
			}
			if ( isset( $_GET['date_to'] ) ) {
				$redirect_args['date_to'] = sanitize_text_field( wp_unslash( $_GET['date_to'] ) );
			}
			wp_safe_redirect( add_query_arg( $redirect_args, admin_url( 'admin.php' ) ) );
			exit;
		}

		if ( isset( $_GET['action'] ) && 'export' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) ) {
			$this->handle_export();
			return;
		}

		if ( isset( $_GET['action'] ) && 'print_report' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) ) {
			$this->handle_print_report();
			return;
		}

		if ( isset( $_GET['action'] ) && 'print_advanced_report' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) ) {
			$this->handle_print_advanced_report();
			return;
		}

		if ( isset( $_GET['page'], $_GET['action'], $_GET['department_id'], $_GET['_wpnonce'] ) && 'quanlicb-departments' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) && 'delete_department' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) ) {
			$this->handle_delete_department();
			return;
		}

		if ( isset( $_GET['page'], $_GET['action'], $_GET['position_id'], $_GET['_wpnonce'] ) && 'quanlicb-positions' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) && 'delete_position' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) ) {
			$this->handle_delete_position();
			return;
		}

		if ( isset( $_GET['action'], $_GET['macb'], $_GET['_wpnonce'] ) && 'delete' === $_GET['action'] ) {
			if ( ! QuanLiCB_Permissions::can_delete() ) {
				wp_die( esc_html__( 'Bạn không có quyền xóa.', 'quanlicb' ) );
			}
			$macb = sanitize_text_field( wp_unslash( $_GET['macb'] ) );
			if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'quanlicb_delete_' . $macb ) ) {
				$old = QuanLiCB_CanBo::get( $macb );
				QuanLiCB_CanBo::delete( $macb );
				QuanLiCB_Audit_Log::log( 'delete', 'canbo', $macb, 'Xóa cán bộ', $old ? $old : array() );
				wp_safe_redirect( add_query_arg( array( 'page' => 'quanlicb', 'deleted' => '1' ), admin_url( 'admin.php' ) ) );
				exit;
			}
		}

		if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
			return;
		}

		if ( ! empty( $_POST['quanlicb_department_action'] ) ) {
			$this->handle_department_post();
			return;
		}

		if ( ! empty( $_POST['quanlicb_position_action'] ) ) {
			$this->handle_position_post();
			return;
		}

		if ( ! empty( $_POST['quanlicb_import_action'] ) ) {
			$this->handle_import_post();
			return;
		}

		if ( empty( $_POST['quanlicb_action'] ) ) {
			return;
		}

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'quanlicb_save' ) ) {
			wp_die( esc_html__( 'Nonce không hợp lệ.', 'quanlicb' ) );
		}

		if ( ! QuanLiCB_Permissions::can_edit() ) {
			wp_die( esc_html__( 'Bạn không có quyền thêm/sửa.', 'quanlicb' ) );
		}

		$action   = sanitize_text_field( wp_unslash( $_POST['quanlicb_action'] ) );
		$old_macb = isset( $_POST['old_macb'] ) ? sanitize_text_field( wp_unslash( $_POST['old_macb'] ) ) : '';
		$data     = array(
			'MaCB'       => isset( $_POST['MaCB'] ) ? wp_unslash( $_POST['MaCB'] ) : '',
			'HoTen'      => isset( $_POST['HoTen'] ) ? wp_unslash( $_POST['HoTen'] ) : '',
			'NgaySinh'   => isset( $_POST['NgaySinh'] ) ? wp_unslash( $_POST['NgaySinh'] ) : '',
			'GioiTinh'   => isset( $_POST['GioiTinh'] ) ? wp_unslash( $_POST['GioiTinh'] ) : '',
			'PhongBan'   => isset( $_POST['PhongBan'] ) ? wp_unslash( $_POST['PhongBan'] ) : '',
			'ChucVu'     => isset( $_POST['ChucVu'] ) ? wp_unslash( $_POST['ChucVu'] ) : '',
			'HeSoLuong'  => isset( $_POST['HeSoLuong'] ) ? wp_unslash( $_POST['HeSoLuong'] ) : '',
			'LuongCoBan' => isset( $_POST['LuongCoBan'] ) ? wp_unslash( $_POST['LuongCoBan'] ) : '',
			'AnhDaiDien' => isset( $_POST['AnhDaiDien'] ) ? wp_unslash( $_POST['AnhDaiDien'] ) : '',
		);

		$mode = ( 'update' === $action ) ? 'update' : 'create';
		$val  = QuanLiCB_Validator::validate( $data, $mode, $old_macb );

		if ( ! $val['valid'] ) {
			set_transient( 'quanlicb_errors_' . get_current_user_id(), $val['errors'], 60 );
			set_transient( 'quanlicb_form_' . get_current_user_id(), $data, 60 );
			$redirect = ( 'update' === $action )
				? add_query_arg( array( 'page' => 'quanlicb', 'action' => 'edit', 'macb' => $old_macb ), admin_url( 'admin.php' ) )
				: add_query_arg( array( 'page' => 'quanlicb-add' ), admin_url( 'admin.php' ) );
			wp_safe_redirect( $redirect );
			exit;
		}

		if ( 'create' === $action ) {
			$ok = QuanLiCB_CanBo::create( $data );
			if ( $ok ) {
				QuanLiCB_Audit_Log::log( 'create', 'canbo', $data['MaCB'], 'Thêm cán bộ', $data );
			}
			$param = $ok ? 'created' : 'error';
		} else {
			$before = QuanLiCB_CanBo::get( $old_macb );
			$ok     = QuanLiCB_CanBo::update( $old_macb, $data );
			if ( false !== $ok ) {
				QuanLiCB_Audit_Log::log( 'update', 'canbo', $data['MaCB'], 'Cập nhật cán bộ', array( 'before' => $before, 'after' => $data ) );
			}
			$param = $ok !== false ? 'updated' : 'error';
		}

		wp_safe_redirect( add_query_arg( array( 'page' => 'quanlicb', $param => '1' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Xử lý thêm/sửa phòng ban.
	 */
	protected function handle_department_post() {
		if ( ! QuanLiCB_Permissions::can_edit() ) {
			wp_die( esc_html__( 'Bạn không có quyền quản lý phòng ban.', 'quanlicb' ) );
		}

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'quanlicb_save_department' ) ) {
			wp_die( esc_html__( 'Nonce không hợp lệ.', 'quanlicb' ) );
		}

		$department_id = isset( $_POST['department_id'] ) ? absint( $_POST['department_id'] ) : 0;
		$data          = array(
			'TenPhongBan' => isset( $_POST['TenPhongBan'] ) ? wp_unslash( $_POST['TenPhongBan'] ) : '',
			'MoTa'        => isset( $_POST['MoTa'] ) ? wp_unslash( $_POST['MoTa'] ) : '',
		);
		$validation    = QuanLiCB_PhongBan::validate( $data, $department_id );

		if ( ! $validation['valid'] ) {
			set_transient( 'quanlicb_department_errors_' . get_current_user_id(), $validation['errors'], 60 );
			set_transient( 'quanlicb_department_form_' . get_current_user_id(), $data, 60 );
			$args = array( 'page' => 'quanlicb-departments' );
			if ( $department_id ) {
				$args['edit_department'] = $department_id;
			}
			wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
			exit;
		}

		if ( $department_id ) {
			$old = QuanLiCB_PhongBan::get( $department_id );
			QuanLiCB_PhongBan::update( $department_id, $data );
			QuanLiCB_Audit_Log::log( 'update', 'department', (string) $department_id, 'Cập nhật phòng ban', array( 'before' => $old, 'after' => $data ) );
			$param = 'department_updated';
		} else {
			QuanLiCB_PhongBan::create( $data );
			QuanLiCB_Audit_Log::log( 'create', 'department', $data['TenPhongBan'], 'Thêm phòng ban', $data );
			$param = 'department_created';
		}

		wp_safe_redirect( add_query_arg( array( 'page' => 'quanlicb-departments', $param => 1 ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Xử lý thêm/sửa chức vụ.
	 */
	protected function handle_position_post() {
		if ( ! QuanLiCB_Permissions::can_edit() ) {
			wp_die( esc_html__( 'Bạn không có quyền quản lý chức vụ.', 'quanlicb' ) );
		}

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'quanlicb_save_position' ) ) {
			wp_die( esc_html__( 'Nonce không hợp lệ.', 'quanlicb' ) );
		}

		$position_id = isset( $_POST['position_id'] ) ? absint( $_POST['position_id'] ) : 0;
		$data        = array(
			'TenChucVu' => isset( $_POST['TenChucVu'] ) ? wp_unslash( $_POST['TenChucVu'] ) : '',
			'MoTa'      => isset( $_POST['MoTa'] ) ? wp_unslash( $_POST['MoTa'] ) : '',
		);
		$validation  = QuanLiCB_ChucVu::validate( $data, $position_id );

		if ( ! $validation['valid'] ) {
			set_transient( 'quanlicb_position_errors_' . get_current_user_id(), $validation['errors'], 60 );
			set_transient( 'quanlicb_position_form_' . get_current_user_id(), $data, 60 );
			$args = array( 'page' => 'quanlicb-positions' );
			if ( $position_id ) {
				$args['edit_position'] = $position_id;
			}
			wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
			exit;
		}

		if ( $position_id ) {
			$old = QuanLiCB_ChucVu::get( $position_id );
			QuanLiCB_ChucVu::update( $position_id, $data );
			QuanLiCB_Audit_Log::log( 'update', 'position', (string) $position_id, 'Cập nhật chức vụ', array( 'before' => $old, 'after' => $data ) );
			$param = 'position_updated';
		} else {
			QuanLiCB_ChucVu::create( $data );
			QuanLiCB_Audit_Log::log( 'create', 'position', $data['TenChucVu'], 'Thêm chức vụ', $data );
			$param = 'position_created';
		}

		wp_safe_redirect( add_query_arg( array( 'page' => 'quanlicb-positions', $param => 1 ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Xử lý import CSV.
	 */
	protected function handle_import_post() {
		if ( ! QuanLiCB_Permissions::can_edit() ) {
			wp_die( esc_html__( 'Bạn không có quyền import dữ liệu.', 'quanlicb' ) );
		}

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'quanlicb_import_csv' ) ) {
			wp_die( esc_html__( 'Nonce không hợp lệ.', 'quanlicb' ) );
		}

		if ( empty( $_FILES['quanlicb_csv_file']['tmp_name'] ) || empty( $_FILES['quanlicb_csv_file']['name'] ) ) {
			set_transient( 'quanlicb_import_result_' . get_current_user_id(), array( 'created' => 0, 'updated' => 0, 'errors' => array( __( 'Vui lòng chọn tệp CSV để import.', 'quanlicb' ) ) ), 60 );
			wp_safe_redirect( admin_url( 'admin.php?page=quanlicb-import' ) );
			exit;
		}

		$file_name = sanitize_file_name( wp_unslash( $_FILES['quanlicb_csv_file']['name'] ) );
		if ( 'csv' !== strtolower( pathinfo( $file_name, PATHINFO_EXTENSION ) ) ) {
			set_transient( 'quanlicb_import_result_' . get_current_user_id(), array( 'created' => 0, 'updated' => 0, 'errors' => array( __( 'Chỉ chấp nhận tệp .csv.', 'quanlicb' ) ) ), 60 );
			wp_safe_redirect( admin_url( 'admin.php?page=quanlicb-import' ) );
			exit;
		}

		$result = QuanLiCB_CanBo::import_from_csv( sanitize_text_field( wp_unslash( $_FILES['quanlicb_csv_file']['tmp_name'] ) ) );
		QuanLiCB_Audit_Log::log( 'import', 'canbo', $file_name, 'Import CSV cán bộ', $result );
		set_transient( 'quanlicb_import_result_' . get_current_user_id(), $result, 120 );
		wp_safe_redirect( admin_url( 'admin.php?page=quanlicb-import&imported=1' ) );
		exit;
	}

	/**
	 * Xóa phòng ban.
	 */
	protected function handle_delete_department() {
		if ( ! QuanLiCB_Permissions::can_edit() ) {
			wp_die( esc_html__( 'Bạn không có quyền xóa phòng ban.', 'quanlicb' ) );
		}

		$department_id = absint( $_GET['department_id'] );
		$nonce         = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'quanlicb_delete_department_' . $department_id ) ) {
			wp_die( esc_html__( 'Yêu cầu xóa phòng ban không hợp lệ.', 'quanlicb' ) );
		}

		$item   = QuanLiCB_PhongBan::get( $department_id );
		$result = QuanLiCB_PhongBan::delete( $department_id );
		if ( is_wp_error( $result ) ) {
			wp_safe_redirect( add_query_arg( array( 'page' => 'quanlicb-departments', 'department_error' => rawurlencode( $result->get_error_message() ) ), admin_url( 'admin.php' ) ) );
			exit;
		}

		QuanLiCB_Audit_Log::log( 'delete', 'department', (string) $department_id, 'Xóa phòng ban', $item ? $item : array() );
		wp_safe_redirect( add_query_arg( array( 'page' => 'quanlicb-departments', 'department_deleted' => 1 ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Xóa chức vụ.
	 */
	protected function handle_delete_position() {
		if ( ! QuanLiCB_Permissions::can_edit() ) {
			wp_die( esc_html__( 'Bạn không có quyền xóa chức vụ.', 'quanlicb' ) );
		}

		$position_id = absint( $_GET['position_id'] );
		$nonce       = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'quanlicb_delete_position_' . $position_id ) ) {
			wp_die( esc_html__( 'Yêu cầu xóa chức vụ không hợp lệ.', 'quanlicb' ) );
		}

		$item   = QuanLiCB_ChucVu::get( $position_id );
		$result = QuanLiCB_ChucVu::delete( $position_id );
		if ( is_wp_error( $result ) ) {
			wp_safe_redirect( add_query_arg( array( 'page' => 'quanlicb-positions', 'position_error' => rawurlencode( $result->get_error_message() ) ), admin_url( 'admin.php' ) ) );
			exit;
		}

		QuanLiCB_Audit_Log::log( 'delete', 'position', (string) $position_id, 'Xóa chức vụ', $item ? $item : array() );
		wp_safe_redirect( add_query_arg( array( 'page' => 'quanlicb-positions', 'position_deleted' => 1 ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Xuất CSV theo bộ lọc hiện tại.
	 */
	protected function handle_export() {
		if ( ! QuanLiCB_Permissions::can_view() ) {
			wp_die( esc_html__( 'Bạn không có quyền xuất dữ liệu.', 'quanlicb' ) );
		}

		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'quanlicb_export' ) ) {
			wp_die( esc_html__( 'Yêu cầu xuất dữ liệu không hợp lệ.', 'quanlicb' ) );
		}

		$args = array(
			'macb'      => isset( $_GET['s_macb'] ) ? wp_unslash( $_GET['s_macb'] ) : '',
			'hoten'     => isset( $_GET['s_hoten'] ) ? wp_unslash( $_GET['s_hoten'] ) : '',
			'phongban'  => isset( $_GET['s_phongban'] ) ? wp_unslash( $_GET['s_phongban'] ) : '',
			'chucvu'    => isset( $_GET['s_chucvu'] ) ? wp_unslash( $_GET['s_chucvu'] ) : '',
			'gioitinh'  => isset( $_GET['s_gioitinh'] ) ? wp_unslash( $_GET['s_gioitinh'] ) : '',
			'luong_min' => isset( $_GET['s_luong_min'] ) ? wp_unslash( $_GET['s_luong_min'] ) : '',
			'luong_max' => isset( $_GET['s_luong_max'] ) ? wp_unslash( $_GET['s_luong_max'] ) : '',
			'orderby'   => isset( $_GET['orderby'] ) ? wp_unslash( $_GET['orderby'] ) : 'macb',
			'order'     => isset( $_GET['order'] ) ? wp_unslash( $_GET['order'] ) : 'asc',
		);

		$items    = QuanLiCB_CanBo::export_items( $args );
		$filename = 'quan-ly-can-bo-' . gmdate( 'Ymd-His' ) . '.csv';

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $filename );

		$output = fopen( 'php://output', 'w' );
		if ( false === $output ) {
			exit;
		}

		fprintf( $output, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
		fputcsv( $output, array( 'MaCB', 'HoTen', 'NgaySinh', 'GioiTinh', 'PhongBan', 'ChucVu', 'HeSoLuong', 'LuongCoBan', 'TongLuong' ) );

		foreach ( $items as $row ) {
			fputcsv(
				$output,
				array(
					$row['MaCB'],
					$row['HoTen'],
					$row['NgaySinh'],
					$row['GioiTinh'],
					$row['PhongBan'],
					$row['ChucVu'],
					$row['HeSoLuong'],
					$row['LuongCoBan'],
					$row['TongLuong'],
				)
			);
		}

		fclose( $output );
		exit;
	}

	/**
	 * In báo cáo tổng hợp.
	 */
	protected function handle_print_report() {
		if ( ! QuanLiCB_Permissions::can_view() ) {
			wp_die( esc_html__( 'Bạn không có quyền in báo cáo.', 'quanlicb' ) );
		}

		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'quanlicb_print_report' ) ) {
			wp_die( esc_html__( 'Yêu cầu in báo cáo không hợp lệ.', 'quanlicb' ) );
		}

		$total_cb      = QuanLiCB_Statistics::total_can_bo();
		$total_luong   = QuanLiCB_Statistics::total_quy_luong();
		$average_luong = QuanLiCB_Statistics::average_tong_luong();
		$by_pb         = QuanLiCB_Statistics::by_phong_ban();
		$by_gt         = QuanLiCB_Statistics::by_gioi_tinh();
		$generated_at  = current_time( 'timestamp' );

		include QUANLICB_PATH . 'admin/views/print-report.php';
		exit;
	}

	/**
	 * In báo cáo nâng cao.
	 */
	protected function handle_print_advanced_report() {
		if ( ! QuanLiCB_Permissions::can_view() ) {
			wp_die( esc_html__( 'Bạn không có quyền in báo cáo nâng cao.', 'quanlicb' ) );
		}

		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'quanlicb_print_advanced_report' ) ) {
			wp_die( esc_html__( 'Yêu cầu in báo cáo nâng cao không hợp lệ.', 'quanlicb' ) );
		}

		$date_from = isset( $_GET['date_from'] ) ? sanitize_text_field( wp_unslash( $_GET['date_from'] ) ) : '';
		$date_to   = isset( $_GET['date_to'] ) ? sanitize_text_field( wp_unslash( $_GET['date_to'] ) ) : '';

		$filtered_items   = QuanLiCB_Statistics::by_updated_range( $date_from, $date_to );
		$salary_by_pb     = QuanLiCB_Statistics::salary_by_phong_ban_range( $date_from, $date_to );
		$top_luong        = QuanLiCB_Statistics::top_luong( 10 );
		$generated_at     = current_time( 'timestamp' );
		$total_filtered   = count( $filtered_items );
		$total_filtered_q = 0;
		foreach ( $filtered_items as $item ) {
			$total_filtered_q += (float) $item['TongLuong'];
		}

		include QUANLICB_PATH . 'admin/views/print-report-advanced.php';
		exit;
	}

	public function page_dashboard() {
		if ( ! QuanLiCB_Permissions::can_view() ) {
			wp_die( esc_html__( 'Không có quyền truy cập.', 'quanlicb' ) );
		}

		$total_cb          = QuanLiCB_Statistics::total_can_bo();
		$total_luong       = QuanLiCB_Statistics::total_quy_luong();
		$average_luong     = QuanLiCB_Statistics::average_tong_luong();
		$total_departments = QuanLiCB_Statistics::total_phong_ban();
		$total_positions   = QuanLiCB_Statistics::total_chuc_vu();
		$by_pb             = QuanLiCB_Statistics::by_phong_ban();
		$by_cv             = QuanLiCB_Statistics::by_chuc_vu();
		$top_luong         = QuanLiCB_Statistics::top_luong( 5 );
		$recent_logs       = QuanLiCB_Audit_Log::recent( 8 );

		include QUANLICB_PATH . 'admin/views/dashboard.php';
	}

	public function page_list() {
		if ( ! QuanLiCB_Permissions::can_view() ) {
			wp_die( esc_html__( 'Không có quyền truy cập.', 'quanlicb' ) );
		}

		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
		if ( 'edit' === $action && QuanLiCB_Permissions::can_edit() ) {
			$this->page_edit();
			return;
		}
		if ( 'view' === $action ) {
			$this->page_detail();
			return;
		}

		$args = array(
			'macb'      => isset( $_GET['s_macb'] ) ? wp_unslash( $_GET['s_macb'] ) : '',
			'hoten'     => isset( $_GET['s_hoten'] ) ? wp_unslash( $_GET['s_hoten'] ) : '',
			'phongban'  => isset( $_GET['s_phongban'] ) ? wp_unslash( $_GET['s_phongban'] ) : '',
			'chucvu'    => isset( $_GET['s_chucvu'] ) ? wp_unslash( $_GET['s_chucvu'] ) : '',
			'gioitinh'  => isset( $_GET['s_gioitinh'] ) ? wp_unslash( $_GET['s_gioitinh'] ) : '',
			'luong_min' => isset( $_GET['s_luong_min'] ) ? wp_unslash( $_GET['s_luong_min'] ) : '',
			'luong_max' => isset( $_GET['s_luong_max'] ) ? wp_unslash( $_GET['s_luong_max'] ) : '',
			'orderby'   => isset( $_GET['orderby'] ) ? wp_unslash( $_GET['orderby'] ) : 'macb',
			'order'     => isset( $_GET['order'] ) ? wp_unslash( $_GET['order'] ) : 'asc',
			'paged'     => isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1,
		);

		$result      = QuanLiCB_CanBo::list( $args );
		$phong_bans  = QuanLiCB_CanBo::get_phong_ban_list();
		$chuc_vus    = QuanLiCB_CanBo::get_chuc_vu_list();
		$can_edit    = QuanLiCB_Permissions::can_edit();
		$can_delete  = QuanLiCB_Permissions::can_delete();

		include QUANLICB_PATH . 'admin/views/list.php';
	}

	public function page_add() {
		if ( ! QuanLiCB_Permissions::can_edit() ) {
			wp_die( esc_html__( 'Không có quyền thêm.', 'quanlicb' ) );
		}
		$item   = null;
		$errors = get_transient( 'quanlicb_errors_' . get_current_user_id() );
		$form   = get_transient( 'quanlicb_form_' . get_current_user_id() );
		$phong_bans = QuanLiCB_CanBo::get_phong_ban_list();
		$chuc_vus   = QuanLiCB_CanBo::get_chuc_vu_list();
		if ( $form ) {
			$item = $form;
		}
		delete_transient( 'quanlicb_errors_' . get_current_user_id() );
		delete_transient( 'quanlicb_form_' . get_current_user_id() );
		include QUANLICB_PATH . 'admin/views/form.php';
	}

	public function page_edit() {
		$macb = isset( $_GET['macb'] ) ? sanitize_text_field( wp_unslash( $_GET['macb'] ) ) : '';
		$item = QuanLiCB_CanBo::get( $macb );
		if ( ! $item ) {
			wp_die( esc_html__( 'Không tìm thấy cán bộ.', 'quanlicb' ) );
		}
		$errors = get_transient( 'quanlicb_errors_' . get_current_user_id() );
		$form   = get_transient( 'quanlicb_form_' . get_current_user_id() );
		if ( $form ) {
			$item = array_merge( $item, $form );
		}
		$phong_bans = QuanLiCB_CanBo::get_phong_ban_list();
		$chuc_vus   = QuanLiCB_CanBo::get_chuc_vu_list();
		delete_transient( 'quanlicb_errors_' . get_current_user_id() );
		delete_transient( 'quanlicb_form_' . get_current_user_id() );
		include QUANLICB_PATH . 'admin/views/form.php';
	}

	/**
	 * Xem chi tiết cán bộ.
	 */
	public function page_detail() {
		if ( ! QuanLiCB_Permissions::can_view() ) {
			wp_die( esc_html__( 'Không có quyền truy cập.', 'quanlicb' ) );
		}
		$macb = isset( $_GET['macb'] ) ? sanitize_text_field( wp_unslash( $_GET['macb'] ) ) : '';
		$item = QuanLiCB_CanBo::get( $macb );
		if ( ! $item ) {
			wp_die( esc_html__( 'Không tìm thấy cán bộ.', 'quanlicb' ) );
		}
		include QUANLICB_PATH . 'admin/views/detail.php';
	}

	/**
	 * Quản lý phòng ban.
	 */
	public function page_departments() {
		if ( ! QuanLiCB_Permissions::can_edit() ) {
			wp_die( esc_html__( 'Không có quyền quản lý phòng ban.', 'quanlicb' ) );
		}

		$departments       = QuanLiCB_PhongBan::all();
		$department_errors = get_transient( 'quanlicb_department_errors_' . get_current_user_id() );
		$department_form   = get_transient( 'quanlicb_department_form_' . get_current_user_id() );
		$editing_id        = isset( $_GET['edit_department'] ) ? absint( $_GET['edit_department'] ) : 0;
		$editing_item      = $editing_id ? QuanLiCB_PhongBan::get( $editing_id ) : null;

		if ( $department_form ) {
			$editing_item = array_merge( (array) $editing_item, $department_form );
		}

		delete_transient( 'quanlicb_department_errors_' . get_current_user_id() );
		delete_transient( 'quanlicb_department_form_' . get_current_user_id() );

		include QUANLICB_PATH . 'admin/views/departments.php';
	}

	/**
	 * Quản lý chức vụ.
	 */
	public function page_positions() {
		if ( ! QuanLiCB_Permissions::can_edit() ) {
			wp_die( esc_html__( 'Không có quyền quản lý chức vụ.', 'quanlicb' ) );
		}

		$positions       = QuanLiCB_ChucVu::all();
		$position_errors = get_transient( 'quanlicb_position_errors_' . get_current_user_id() );
		$position_form   = get_transient( 'quanlicb_position_form_' . get_current_user_id() );
		$editing_id      = isset( $_GET['edit_position'] ) ? absint( $_GET['edit_position'] ) : 0;
		$editing_item    = $editing_id ? QuanLiCB_ChucVu::get( $editing_id ) : null;

		if ( $position_form ) {
			$editing_item = array_merge( (array) $editing_item, $position_form );
		}

		delete_transient( 'quanlicb_position_errors_' . get_current_user_id() );
		delete_transient( 'quanlicb_position_form_' . get_current_user_id() );

		include QUANLICB_PATH . 'admin/views/positions.php';
	}

	/**
	 * Trang import CSV.
	 */
	public function page_import() {
		if ( ! QuanLiCB_Permissions::can_edit() ) {
			wp_die( esc_html__( 'Không có quyền import.', 'quanlicb' ) );
		}

		$import_result = get_transient( 'quanlicb_import_result_' . get_current_user_id() );
		delete_transient( 'quanlicb_import_result_' . get_current_user_id() );
		include QUANLICB_PATH . 'admin/views/import.php';
	}

	public function page_stats() {
		if ( ! QuanLiCB_Permissions::can_view() ) {
			wp_die( esc_html__( 'Không có quyền truy cập.', 'quanlicb' ) );
		}

		$date_from = isset( $_GET['date_from'] ) ? sanitize_text_field( wp_unslash( $_GET['date_from'] ) ) : '';
		$date_to   = isset( $_GET['date_to'] ) ? sanitize_text_field( wp_unslash( $_GET['date_to'] ) ) : '';

		$total_cb          = QuanLiCB_Statistics::total_can_bo();
		$total_luong       = QuanLiCB_Statistics::total_quy_luong();
		$average_luong     = QuanLiCB_Statistics::average_tong_luong();
		$total_departments = QuanLiCB_Statistics::total_phong_ban();
		$by_pb             = QuanLiCB_Statistics::by_phong_ban();
		$by_gt             = QuanLiCB_Statistics::by_gioi_tinh();

		$filtered_items = QuanLiCB_Statistics::by_updated_range( $date_from, $date_to );
		$salary_by_pb   = QuanLiCB_Statistics::salary_by_phong_ban_range( $date_from, $date_to );
		$top_luong      = QuanLiCB_Statistics::top_luong( 10 );

		$total_filtered   = count( $filtered_items );
		$total_filtered_q = 0;
		foreach ( $filtered_items as $item ) {
			$total_filtered_q += (float) $item['TongLuong'];
		}

		include QUANLICB_PATH . 'admin/views/statistics.php';
	}

	/**
	 * Nhật ký thao tác.
	 */
	public function page_logs() {
		if ( ! QuanLiCB_Permissions::can_view() ) {
			wp_die( esc_html__( 'Không có quyền truy cập.', 'quanlicb' ) );
		}

		$args = array(
			'action'      => isset( $_GET['s_action'] ) ? wp_unslash( $_GET['s_action'] ) : '',
			'object_type' => isset( $_GET['s_object_type'] ) ? wp_unslash( $_GET['s_object_type'] ) : '',
			'date_from'   => isset( $_GET['date_from'] ) ? wp_unslash( $_GET['date_from'] ) : '',
			'date_to'     => isset( $_GET['date_to'] ) ? wp_unslash( $_GET['date_to'] ) : '',
			'paged'       => isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1,
		);

		$result = QuanLiCB_Audit_Log::query( $args );
		include QUANLICB_PATH . 'admin/views/logs.php';
	}
}

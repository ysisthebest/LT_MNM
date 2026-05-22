<?php
/**
 * Plugin header and compact navigation.
 *
 * @var string $title
 * @var string $actions HTML action buttons.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_page   = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : 'quanlicb-dashboard';
$current_action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';

if ( 'quanlicb' === $current_page && 'edit' === $current_action ) {
	$current_page = 'quanlicb-add';
}

$nav_items = array(
	array(
		'key'   => 'quanlicb-dashboard',
		'label' => __( 'Dashboard', 'quanlicb' ),
		'url'   => admin_url( 'admin.php?page=quanlicb-dashboard' ),
	),
	array(
		'key'   => 'quanlicb',
		'label' => __( 'Danh sách', 'quanlicb' ),
		'url'   => admin_url( 'admin.php?page=quanlicb' ),
	),
	array(
		'key'   => 'quanlicb-stats',
		'label' => __( 'Thống kê & Báo cáo', 'quanlicb' ),
		'url'   => admin_url( 'admin.php?page=quanlicb-stats' ),
	),
	array(
		'key'   => 'quanlicb-logs',
		'label' => __( 'Nhật ký', 'quanlicb' ),
		'url'   => admin_url( 'admin.php?page=quanlicb-logs' ),
	),
);

if ( QuanLiCB_Permissions::can_edit() ) {
	$nav_items[] = array(
		'key'   => 'quanlicb-add',
		'label' => __( 'Thêm mới', 'quanlicb' ),
		'url'   => admin_url( 'admin.php?page=quanlicb-add' ),
	);
	$nav_items[] = array(
		'key'   => 'quanlicb-departments',
		'label' => __( 'Phòng ban', 'quanlicb' ),
		'url'   => admin_url( 'admin.php?page=quanlicb-departments' ),
	);
	$nav_items[] = array(
		'key'   => 'quanlicb-positions',
		'label' => __( 'Chức vụ', 'quanlicb' ),
		'url'   => admin_url( 'admin.php?page=quanlicb-positions' ),
	);
}
?>
<div class="quanlicb-app-shell">
	<div class="quanlicb-app-main">
		<div class="quanlicb-topbar">
			<div class="quanlicb-topbar__brand">
				<span class="quanlicb-topbar__mark">
					<span>QL</span>
				</span>
				<div class="quanlicb-topbar__text">
					<strong><?php esc_html_e( 'QuanLyCB', 'quanlicb' ); ?></strong>
					<span><?php esc_html_e( 'Hệ thống quản lý cán bộ nội bộ', 'quanlicb' ); ?></span>
				</div>
			</div>
			<nav class="quanlicb-topbar__nav" aria-label="<?php esc_attr_e( 'Điều hướng trang', 'quanlicb' ); ?>">
				<?php foreach ( $nav_items as $nav_item ) : ?>
					<a class="quanlicb-topbar__link <?php echo $current_page === $nav_item['key'] ? 'is-active' : ''; ?>" href="<?php echo esc_url( $nav_item['url'] ); ?>">
						<?php echo esc_html( $nav_item['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
		</div>

		<div class="quanlicb-page-head">
			<div class="quanlicb-page-head__content">
				<span class="quanlicb-page-head__eyebrow"><?php esc_html_e( 'Bảng điều khiển', 'quanlicb' ); ?></span>
				<h1><?php echo esc_html( $title ); ?></h1>
				<p class="quanlicb-page-head__desc"><?php esc_html_e( 'Tập trung hồ sơ, danh mục, lương, báo cáo và lịch sử thao tác trên cùng một dashboard.', 'quanlicb' ); ?></p>
			</div>
			<div class="quanlicb-page-head__side">
				<?php if ( ! empty( $actions ) ) : ?>
					<div class="quanlicb-page-head__actions">
						<?php echo $actions; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>
				<div class="quanlicb-hero-card">
					<span class="quanlicb-hero-card__eyebrow"><?php esc_html_e( 'Không gian làm việc', 'quanlicb' ); ?></span>
					<strong><?php esc_html_e( 'Quản trị nhân sự nội bộ', 'quanlicb' ); ?></strong>
					<p><?php esc_html_e( 'Tối ưu nhập liệu, tìm kiếm, báo cáo và truy vết thay đổi cho đề tài quản lý cán bộ.', 'quanlicb' ); ?></p>
				</div>
			</div>
		</div>

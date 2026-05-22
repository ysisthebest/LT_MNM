<?php
/**
 * Chi tiết cán bộ.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title   = __( 'Chi tiết cán bộ', 'quanlicb' );
$actions = '<a href="' . esc_url( admin_url( 'admin.php?page=quanlicb' ) ) . '" class="page-title-action">' . esc_html__( 'Quay lại danh sách', 'quanlicb' ) . '</a>';

if ( QuanLiCB_Permissions::can_edit() ) {
	$actions .= '<a href="' . esc_url( add_query_arg( array( 'page' => 'quanlicb', 'action' => 'edit', 'macb' => $item['MaCB'] ), admin_url( 'admin.php' ) ) ) . '" class="page-title-action">' . esc_html__( 'Chỉnh sửa', 'quanlicb' ) . '</a>';
}
?>
<div class="wrap quanlicb-wrap">
	<?php include QUANLICB_PATH . 'admin/views/partials/header.php'; ?>

	<div class="quanlicb-overview-grid quanlicb-overview-grid--three">
		<div class="quanlicb-overview-card">
			<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Mã cán bộ', 'quanlicb' ); ?></span>
			<strong class="quanlicb-overview-card__value"><?php echo esc_html( $item['MaCB'] ); ?></strong>
			<span class="quanlicb-overview-card__meta"><?php esc_html_e( 'Định danh hồ sơ cán bộ trong hệ thống.', 'quanlicb' ); ?></span>
		</div>
		<div class="quanlicb-overview-card">
			<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Phòng ban / Chức vụ', 'quanlicb' ); ?></span>
			<strong class="quanlicb-overview-card__value"><?php echo esc_html( $item['PhongBan'] . ' / ' . $item['ChucVu'] ); ?></strong>
			<span class="quanlicb-overview-card__meta"><?php esc_html_e( 'Đơn vị và cấp bậc công tác hiện tại.', 'quanlicb' ); ?></span>
		</div>
		<div class="quanlicb-overview-card quanlicb-overview-card--accent">
			<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Tổng lương', 'quanlicb' ); ?></span>
			<strong class="quanlicb-overview-card__value"><?php echo esc_html( number_format( (float) $item['TongLuong'], 0, ',', '.' ) ); ?> đ</strong>
			<span class="quanlicb-overview-card__meta"><?php esc_html_e( 'Được tính từ hệ số lương và lương cơ bản.', 'quanlicb' ); ?></span>
		</div>
	</div>

	<div class="quanlicb-panel-grid">
		<div class="quanlicb-box">
			<div class="quanlicb-box-head">
				<div>
					<p class="quanlicb-box-title"><?php esc_html_e( 'Thông tin hồ sơ', 'quanlicb' ); ?></p>
					<p class="quanlicb-meta"><?php esc_html_e( 'Tổng hợp đầy đủ các trường dữ liệu của cán bộ đang xem.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-box-body">
				<div class="quanlicb-detail-grid">
					<div class="quanlicb-detail-item"><span><?php esc_html_e( 'Họ tên', 'quanlicb' ); ?></span><strong><?php echo esc_html( $item['HoTen'] ); ?></strong></div>
					<div class="quanlicb-detail-item"><span><?php esc_html_e( 'Ngày sinh', 'quanlicb' ); ?></span><strong><?php echo esc_html( date_i18n( 'd/m/Y', strtotime( $item['NgaySinh'] ) ) ); ?></strong></div>
					<div class="quanlicb-detail-item"><span><?php esc_html_e( 'Giới tính', 'quanlicb' ); ?></span><strong><?php echo esc_html( $item['GioiTinh'] ); ?></strong></div>
					<div class="quanlicb-detail-item"><span><?php esc_html_e( 'Phòng ban', 'quanlicb' ); ?></span><strong><?php echo esc_html( $item['PhongBan'] ); ?></strong></div>
					<div class="quanlicb-detail-item"><span><?php esc_html_e( 'Chức vụ', 'quanlicb' ); ?></span><strong><?php echo esc_html( $item['ChucVu'] ); ?></strong></div>
					<div class="quanlicb-detail-item"><span><?php esc_html_e( 'Hệ số lương', 'quanlicb' ); ?></span><strong><?php echo esc_html( number_format( (float) $item['HeSoLuong'], 2 ) ); ?></strong></div>
					<div class="quanlicb-detail-item"><span><?php esc_html_e( 'Lương cơ bản', 'quanlicb' ); ?></span><strong><?php echo esc_html( number_format( (float) $item['LuongCoBan'], 0, ',', '.' ) ); ?> đ</strong></div>
					<div class="quanlicb-detail-item quanlicb-detail-item--full"><span><?php esc_html_e( 'Tổng lương', 'quanlicb' ); ?></span><strong><?php echo esc_html( number_format( (float) $item['TongLuong'], 0, ',', '.' ) ); ?> đ</strong></div>
				</div>
			</div>
		</div>

		<aside class="quanlicb-box quanlicb-box--side">
			<div class="quanlicb-box-head">
				<div>
					<p class="quanlicb-box-title"><?php esc_html_e( 'Ảnh đại diện', 'quanlicb' ); ?></p>
					<p class="quanlicb-meta"><?php esc_html_e( 'Nhận diện nhanh hồ sơ trong danh sách.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-box-body">
				<div class="quanlicb-detail-avatar">
					<?php
					if ( ! empty( $item['AnhDaiDien'] ) ) {
						echo wp_get_attachment_image( (int) $item['AnhDaiDien'], 'medium', false, array( 'class' => 'quanlicb-avatar quanlicb-avatar--large' ) );
					} else {
						echo '<span class="quanlicb-avatar-fallback quanlicb-avatar-fallback--large">N/A</span>';
					}
					?>
				</div>
			</div>
		</aside>
	</div>
</div>
</div>

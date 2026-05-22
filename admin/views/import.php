<?php
/**
 * Import CSV cán bộ.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title   = __( 'Import CSV cán bộ', 'quanlicb' );
$actions = '<a href="' . esc_url( admin_url( 'admin.php?page=quanlicb' ) ) . '" class="page-title-action">' . esc_html__( 'Về danh sách', 'quanlicb' ) . '</a>';
?>
<div class="wrap quanlicb-wrap">
	<?php include QUANLICB_PATH . 'admin/views/partials/header.php'; ?>

	<?php if ( ! empty( $import_result ) ) : ?>
		<div class="notice <?php echo empty( $import_result['errors'] ) ? 'notice-success' : 'notice-warning'; ?> is-dismissible">
			<p>
				<?php
				echo esc_html(
					sprintf(
						__( 'Import xong: thêm %1$d, cập nhật %2$d, lỗi %3$d dòng.', 'quanlicb' ),
						(int) $import_result['created'],
						(int) $import_result['updated'],
						count( $import_result['errors'] )
					)
				);
				?>
			</p>
			<?php if ( ! empty( $import_result['errors'] ) ) : ?>
				<ul>
					<?php foreach ( $import_result['errors'] as $error ) : ?>
						<li><?php echo esc_html( $error ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="quanlicb-panel-grid">
		<div class="quanlicb-box">
			<div class="quanlicb-box-head">
				<div>
					<p class="quanlicb-box-title"><?php esc_html_e( 'Tải tệp CSV', 'quanlicb' ); ?></p>
					<p class="quanlicb-meta"><?php esc_html_e( 'Import hàng loạt cán bộ từ Excel (Save As CSV UTF-8).', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-box-body">
				<form method="post" enctype="multipart/form-data" class="quanlicb-inline-form">
					<?php wp_nonce_field( 'quanlicb_import_csv' ); ?>
					<input type="hidden" name="quanlicb_import_action" value="1" />
					<p>
						<label for="quanlicb_csv_file"><strong><?php esc_html_e( 'Chọn tệp CSV', 'quanlicb' ); ?></strong></label>
						<input type="file" id="quanlicb_csv_file" name="quanlicb_csv_file" accept=".csv,text/csv" required />
					</p>
					<p class="quanlicb-form-actions">
						<button type="submit" class="button button-primary"><?php esc_html_e( 'Bắt đầu import', 'quanlicb' ); ?></button>
					</p>
				</form>
			</div>
		</div>

		<aside class="quanlicb-box quanlicb-box--side">
			<div class="quanlicb-box-head">
				<div>
					<p class="quanlicb-box-title"><?php esc_html_e( 'Mẫu cột bắt buộc', 'quanlicb' ); ?></p>
					<p class="quanlicb-meta"><?php esc_html_e( 'Dòng đầu là tiêu đề cột. Có thể có thêm cột AnhDaiDien.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-box-body">
				<code class="quanlicb-code-line">MaCB,HoTen,NgaySinh,GioiTinh,PhongBan,ChucVu,HeSoLuong,LuongCoBan</code>
				<ul class="quanlicb-summary-list">
					<li><span><?php esc_html_e( 'Ngày sinh', 'quanlicb' ); ?></span><strong>YYYY-MM-DD</strong></li>
					<li><span><?php esc_html_e( 'Giới tính', 'quanlicb' ); ?></span><strong>Nam / Nữ / Khác</strong></li>
					<li><span><?php esc_html_e( 'Chế độ import', 'quanlicb' ); ?></span><strong><?php esc_html_e( 'Trùng MaCB sẽ cập nhật', 'quanlicb' ); ?></strong></li>
				</ul>
			</div>
		</aside>
	</div>
</div>
</div>

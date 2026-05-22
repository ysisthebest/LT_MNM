<?php
/**
 * Quản lý chức vụ.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title   = __( 'Quản lý chức vụ', 'quanlicb' );
$actions = '';
?>
<div class="wrap quanlicb-wrap">
	<?php include QUANLICB_PATH . 'admin/views/partials/header.php'; ?>

	<?php if ( isset( $_GET['position_created'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Đã thêm chức vụ mới.', 'quanlicb' ); ?></p></div>
	<?php endif; ?>
	<?php if ( isset( $_GET['position_updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Đã cập nhật chức vụ.', 'quanlicb' ); ?></p></div>
	<?php endif; ?>
	<?php if ( isset( $_GET['position_deleted'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Đã xóa chức vụ.', 'quanlicb' ); ?></p></div>
	<?php endif; ?>
	<?php if ( isset( $_GET['position_error'] ) ) : ?>
		<div class="notice notice-error is-dismissible"><p><?php echo esc_html( wp_unslash( $_GET['position_error'] ) ); ?></p></div>
	<?php endif; ?>
	<?php if ( ! empty( $position_errors ) && is_array( $position_errors ) ) : ?>
		<div class="notice notice-error"><ul><?php foreach ( $position_errors as $error ) : ?><li><?php echo esc_html( $error ); ?></li><?php endforeach; ?></ul></div>
	<?php endif; ?>

	<div class="quanlicb-panel-grid">
		<div class="quanlicb-box">
			<div class="quanlicb-box-head">
				<div>
					<p class="quanlicb-box-title"><?php esc_html_e( 'Danh mục chức vụ', 'quanlicb' ); ?></p>
					<p class="quanlicb-meta"><?php esc_html_e( 'Chuẩn hóa chức vụ để báo cáo theo cấp bậc chính xác.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-table-wrap">
				<table class="wp-list-table widefat striped quanlicb-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Mã chức vụ', 'quanlicb' ); ?></th>
							<th><?php esc_html_e( 'Tên chức vụ', 'quanlicb' ); ?></th>
							<th><?php esc_html_e( 'Mô tả', 'quanlicb' ); ?></th>
							<th><?php esc_html_e( 'Số cán bộ', 'quanlicb' ); ?></th>
							<th><?php esc_html_e( 'Thao tác', 'quanlicb' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $positions ) ) : ?>
							<tr><td colspan="5" class="quanlicb-empty"><?php esc_html_e( 'Chưa có chức vụ nào.', 'quanlicb' ); ?></td></tr>
						<?php else : ?>
							<?php foreach ( $positions as $position ) : ?>
								<tr>
									<td><strong><?php echo esc_html( $position['MaChucVu'] ); ?></strong></td>
									<td><?php echo esc_html( $position['TenChucVu'] ); ?></td>
									<td><?php echo esc_html( $position['MoTa'] ? $position['MoTa'] : '-' ); ?></td>
									<td><?php echo esc_html( number_format_i18n( QuanLiCB_ChucVu::count_can_bo( $position['TenChucVu'] ) ) ); ?></td>
									<td>
										<div class="quanlicb-actions">
											<a class="quanlicb-act-edit" href="<?php echo esc_url( add_query_arg( array( 'page' => 'quanlicb-positions', 'edit_position' => $position['ID'] ), admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Sửa', 'quanlicb' ); ?></a>
											<a class="quanlicb-act-delete" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'page' => 'quanlicb-positions', 'action' => 'delete_position', 'position_id' => $position['ID'] ), admin_url( 'admin.php' ) ), 'quanlicb_delete_position_' . $position['ID'] ) ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Xóa chức vụ này?', 'quanlicb' ) ); ?>');"><?php esc_html_e( 'Xóa', 'quanlicb' ); ?></a>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>

		<aside class="quanlicb-box quanlicb-box--side">
			<div class="quanlicb-box-head">
				<div>
					<p class="quanlicb-box-title"><?php echo $editing_item ? esc_html__( 'Chỉnh sửa chức vụ', 'quanlicb' ) : esc_html__( 'Thêm chức vụ', 'quanlicb' ); ?></p>
					<p class="quanlicb-meta"><?php esc_html_e( 'Danh mục này được dùng trong hồ sơ cán bộ.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-box-body">
				<form method="post" class="quanlicb-inline-form">
					<?php wp_nonce_field( 'quanlicb_save_position' ); ?>
					<input type="hidden" name="quanlicb_position_action" value="<?php echo $editing_item ? 'update' : 'create'; ?>" />
					<input type="hidden" name="position_id" value="<?php echo esc_attr( $editing_item['ID'] ?? 0 ); ?>" />

					<p>
						<label for="TenChucVu"><strong><?php esc_html_e( 'Tên chức vụ', 'quanlicb' ); ?></strong></label>
						<input type="text" id="TenChucVu" name="TenChucVu" value="<?php echo esc_attr( $editing_item['TenChucVu'] ?? '' ); ?>" required maxlength="100" />
					</p>
					<p>
						<label for="MoTa"><strong><?php esc_html_e( 'Mô tả ngan', 'quanlicb' ); ?></strong></label>
						<input type="text" id="MoTa" name="MoTa" value="<?php echo esc_attr( $editing_item['MoTa'] ?? '' ); ?>" maxlength="255" />
					</p>
					<p class="quanlicb-form-actions">
						<button type="submit" class="button button-primary"><?php echo $editing_item ? esc_html__( 'Cập nhật chức vụ', 'quanlicb' ) : esc_html__( 'Thêm chức vụ', 'quanlicb' ); ?></button>
						<?php if ( $editing_item ) : ?>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=quanlicb-positions' ) ); ?>" class="button"><?php esc_html_e( 'Hủy chỉnh sửa', 'quanlicb' ); ?></a>
						<?php endif; ?>
					</p>
				</form>
			</div>
		</aside>
	</div>
</div>
</div>

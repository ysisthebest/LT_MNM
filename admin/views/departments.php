<?php
/**
 * Quản lý phòng ban.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title   = __( 'Quản lý phòng ban', 'quanlicb' );
$actions = '';
?>
<div class="wrap quanlicb-wrap">
	<?php include QUANLICB_PATH . 'admin/views/partials/header.php'; ?>

	<?php if ( isset( $_GET['department_created'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Đã thêm phòng ban mới.', 'quanlicb' ); ?></p></div>
	<?php endif; ?>
	<?php if ( isset( $_GET['department_updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Đã cập nhật phòng ban.', 'quanlicb' ); ?></p></div>
	<?php endif; ?>
	<?php if ( isset( $_GET['department_deleted'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Đã xóa phòng ban.', 'quanlicb' ); ?></p></div>
	<?php endif; ?>
	<?php if ( isset( $_GET['department_error'] ) ) : ?>
		<div class="notice notice-error is-dismissible"><p><?php echo esc_html( wp_unslash( $_GET['department_error'] ) ); ?></p></div>
	<?php endif; ?>
	<?php if ( ! empty( $department_errors ) && is_array( $department_errors ) ) : ?>
		<div class="notice notice-error"><ul><?php foreach ( $department_errors as $error ) : ?><li><?php echo esc_html( $error ); ?></li><?php endforeach; ?></ul></div>
	<?php endif; ?>

	<div class="quanlicb-panel-grid">
		<div class="quanlicb-box">
			<div class="quanlicb-box-head">
				<div>
					<p class="quanlicb-box-title"><?php esc_html_e( 'Danh mục phòng ban', 'quanlicb' ); ?></p>
					<p class="quanlicb-meta"><?php esc_html_e( 'Quản lý tập trung danh mục phòng ban để tránh nhập liệu rời rạc.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-table-wrap">
				<table class="wp-list-table widefat striped quanlicb-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Mã phòng ban', 'quanlicb' ); ?></th>
							<th><?php esc_html_e( 'Tên phòng ban', 'quanlicb' ); ?></th>
							<th><?php esc_html_e( 'Mô tả', 'quanlicb' ); ?></th>
							<th><?php esc_html_e( 'Số cán bộ', 'quanlicb' ); ?></th>
							<th><?php esc_html_e( 'Thao tác', 'quanlicb' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $departments ) ) : ?>
							<tr><td colspan="5" class="quanlicb-empty"><?php esc_html_e( 'Chưa có phòng ban nào.', 'quanlicb' ); ?></td></tr>
						<?php else : ?>
							<?php foreach ( $departments as $department ) : ?>
								<tr>
									<td><strong><?php echo esc_html( $department['MaPhongBan'] ); ?></strong></td>
									<td><?php echo esc_html( $department['TenPhongBan'] ); ?></td>
									<td><?php echo esc_html( $department['MoTa'] ? $department['MoTa'] : '-' ); ?></td>
									<td><?php echo esc_html( number_format_i18n( QuanLiCB_PhongBan::count_can_bo( $department['TenPhongBan'] ) ) ); ?></td>
									<td>
										<div class="quanlicb-actions">
											<a class="quanlicb-act-edit" href="<?php echo esc_url( add_query_arg( array( 'page' => 'quanlicb-departments', 'edit_department' => $department['ID'] ), admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Sửa', 'quanlicb' ); ?></a>
											<a class="quanlicb-act-delete" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'page' => 'quanlicb-departments', 'action' => 'delete_department', 'department_id' => $department['ID'] ), admin_url( 'admin.php' ) ), 'quanlicb_delete_department_' . $department['ID'] ) ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Xóa phòng ban này?', 'quanlicb' ) ); ?>');"><?php esc_html_e( 'Xóa', 'quanlicb' ); ?></a>
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
					<p class="quanlicb-box-title"><?php echo $editing_item ? esc_html__( 'Chỉnh sửa phòng ban', 'quanlicb' ) : esc_html__( 'Thêm phòng ban', 'quanlicb' ); ?></p>
					<p class="quanlicb-meta"><?php esc_html_e( 'Danh mục này sẽ được dùng trực tiếp trong biểu mẫu cán bộ.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-box-body">
				<form method="post" class="quanlicb-inline-form">
					<?php wp_nonce_field( 'quanlicb_save_department' ); ?>
					<input type="hidden" name="quanlicb_department_action" value="<?php echo $editing_item ? 'update' : 'create'; ?>" />
					<input type="hidden" name="department_id" value="<?php echo esc_attr( $editing_item['ID'] ?? 0 ); ?>" />

					<p>
						<label for="TenPhongBan"><strong><?php esc_html_e( 'Tên phòng ban', 'quanlicb' ); ?></strong></label>
						<input type="text" id="TenPhongBan" name="TenPhongBan" value="<?php echo esc_attr( $editing_item['TenPhongBan'] ?? '' ); ?>" required maxlength="100" />
					</p>
					<p>
						<label for="MoTa"><strong><?php esc_html_e( 'Mô tả ngan', 'quanlicb' ); ?></strong></label>
						<input type="text" id="MoTa" name="MoTa" value="<?php echo esc_attr( $editing_item['MoTa'] ?? '' ); ?>" maxlength="255" />
					</p>
					<p class="quanlicb-form-actions">
						<button type="submit" class="button button-primary"><?php echo $editing_item ? esc_html__( 'Cập nhật phòng ban', 'quanlicb' ) : esc_html__( 'Thêm phòng ban', 'quanlicb' ); ?></button>
						<?php if ( $editing_item ) : ?>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=quanlicb-departments' ) ); ?>" class="button"><?php esc_html_e( 'Hủy chỉnh sửa', 'quanlicb' ); ?></a>
						<?php endif; ?>
					</p>
				</form>
			</div>
		</aside>
	</div>
</div>
</div>

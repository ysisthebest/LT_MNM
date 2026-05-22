<?php
/**
 * Form thêm / sửa.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_edit = ! empty( $item );
$action  = $is_edit ? 'update' : 'create';
$v       = function ( $key, $default = '' ) use ( $item ) {
	return ( $item && isset( $item[ $key ] ) ) ? $item[ $key ] : $default;
};
$anh_id     = (int) $v( 'AnhDaiDien', 0 );
$anh_url    = $anh_id ? wp_get_attachment_image_url( $anh_id, 'thumbnail' ) : '';
$tong       = QuanLiCB_CanBo::calc_tong_luong( (float) $v( 'HeSoLuong', 1 ), (float) $v( 'LuongCoBan', 0 ) );
$phong_bans = isset( $phong_bans ) && is_array( $phong_bans ) ? $phong_bans : QuanLiCB_CanBo::get_phong_ban_list();
$chuc_vus   = isset( $chuc_vus ) && is_array( $chuc_vus ) ? $chuc_vus : QuanLiCB_CanBo::get_chuc_vu_list();

$title   = $is_edit ? __( 'Sửa cán bộ', 'quanlicb' ) : __( 'Thêm cán bộ', 'quanlicb' );
$actions = '';
?>
<div class="wrap quanlicb-wrap">
	<?php include QUANLICB_PATH . 'admin/views/partials/header.php'; ?>

	<?php if ( ! empty( $errors ) && is_array( $errors ) ) : ?>
		<div class="notice notice-error"><ul><?php foreach ( $errors as $err ) : ?><li><?php echo esc_html( $err ); ?></li><?php endforeach; ?></ul></div>
	<?php endif; ?>

	<?php if ( $is_edit ) : ?>
		<div class="quanlicb-overview-grid quanlicb-overview-grid--three">
			<div class="quanlicb-overview-card">
				<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Trạng thái biểu mẫu', 'quanlicb' ); ?></span>
				<strong class="quanlicb-overview-card__value"><?php esc_html_e( 'Đang chỉnh sửa', 'quanlicb' ); ?></strong>
				<span class="quanlicb-overview-card__meta"><?php esc_html_e( 'Dùng chung cho thêm và cập nhật hồ sơ cán bộ.', 'quanlicb' ); ?></span>
			</div>
			<div class="quanlicb-overview-card">
				<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Mã hồ sơ', 'quanlicb' ); ?></span>
				<strong id="quanlicb-summary-macb-card" class="quanlicb-overview-card__value"><?php echo esc_html( $v( 'MaCB', '---' ) ); ?></strong>
				<span class="quanlicb-overview-card__meta"><?php esc_html_e( 'Cập nhật ngay khi bạn thay đổi trường mã.', 'quanlicb' ); ?></span>
			</div>
			<div class="quanlicb-overview-card quanlicb-overview-card--accent">
				<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Tổng lương tạm tính', 'quanlicb' ); ?></span>
				<strong class="quanlicb-overview-card__value"><?php echo esc_html( number_format( $tong, 0, ',', '.' ) ); ?> đ</strong>
				<span class="quanlicb-overview-card__meta"><?php esc_html_e( 'Tự động cập nhật theo hệ số và lương cơ bản.', 'quanlicb' ); ?></span>
			</div>
		</div>
	<?php endif; ?>

	<form method="post" class="quanlicb-form quanlicb-box">
		<?php wp_nonce_field( 'quanlicb_save' ); ?>
		<input type="hidden" name="quanlicb_action" value="<?php echo esc_attr( $action ); ?>" />
		<?php if ( $is_edit ) : ?>
			<input type="hidden" name="old_macb" value="<?php echo esc_attr( $item['MaCB'] ); ?>" />
		<?php endif; ?>

		<div class="quanlicb-form-layout">
			<div class="quanlicb-form-panel">
				<div class="quanlicb-form-panel__head">
					<h2><?php esc_html_e( 'Thông tin cá nhân', 'quanlicb' ); ?></h2>
					<p><?php esc_html_e( 'Điền đầy đủ các trường bắt buộc để hoàn tất hồ sơ nội bộ.', 'quanlicb' ); ?></p>
				</div>
				<table class="form-table" role="presentation">
					<tr>
						<th><label for="MaCB"><?php esc_html_e( 'Mã cán bộ', 'quanlicb' ); ?> *</label></th>
						<td><input type="text" id="MaCB" name="MaCB" value="<?php echo esc_attr( $v( 'MaCB' ) ); ?>" class="regular-text quanlicb-summary-input" data-summary-target="#quanlicb-summary-macb, #quanlicb-summary-macb-card" required maxlength="20" pattern="[A-Za-z0-9_-]{2,20}" /></td>
					</tr>
					<tr>
						<th><label for="HoTen"><?php esc_html_e( 'Họ tên', 'quanlicb' ); ?> *</label></th>
						<td><input type="text" id="HoTen" name="HoTen" value="<?php echo esc_attr( $v( 'HoTen' ) ); ?>" class="regular-text" required maxlength="100" /></td>
					</tr>
					<tr>
						<th><label for="NgaySinh"><?php esc_html_e( 'Ngày sinh', 'quanlicb' ); ?> *</label></th>
						<td><input type="date" id="NgaySinh" name="NgaySinh" value="<?php echo esc_attr( $v( 'NgaySinh' ) ); ?>" required /></td>
					</tr>
					<tr>
						<th><label for="GioiTinh"><?php esc_html_e( 'Giới tính', 'quanlicb' ); ?> *</label></th>
						<td>
							<div class="quanlicb-select-wrap">
								<select id="GioiTinh" name="GioiTinh" class="quanlicb-summary-input" data-summary-target="#quanlicb-summary-gioitinh">
									<?php foreach ( array( 'Nam', 'Nu', 'Khac' ) as $gt ) : ?>
										<option value="<?php echo esc_attr( $gt ); ?>" <?php selected( $v( 'GioiTinh', 'Nam' ), $gt ); ?>><?php echo esc_html( $gt ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</td>
					</tr>
					<tr>
						<th><label for="PhongBan"><?php esc_html_e( 'Phòng ban', 'quanlicb' ); ?> *</label></th>
						<td>
							<div class="quanlicb-select-wrap">
								<select id="PhongBan" name="PhongBan" class="quanlicb-summary-input" data-summary-target="#quanlicb-summary-phongban" required>
									<option value=""><?php esc_html_e( 'Chọn phòng ban', 'quanlicb' ); ?></option>
									<?php foreach ( $phong_bans as $pb ) : ?>
										<option value="<?php echo esc_attr( $pb ); ?>" <?php selected( $v( 'PhongBan' ), $pb ); ?>><?php echo esc_html( $pb ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</td>
					</tr>
					<tr>
						<th><label for="ChucVu"><?php esc_html_e( 'Chức vụ', 'quanlicb' ); ?> *</label></th>
						<td>
							<div class="quanlicb-select-wrap">
								<select id="ChucVu" name="ChucVu" class="quanlicb-summary-input" data-summary-target="#quanlicb-summary-chucvu" required>
									<option value=""><?php esc_html_e( 'Chọn chức vụ', 'quanlicb' ); ?></option>
									<?php foreach ( $chuc_vus as $cv ) : ?>
										<option value="<?php echo esc_attr( $cv ); ?>" <?php selected( $v( 'ChucVu' ), $cv ); ?>><?php echo esc_html( $cv ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</td>
					</tr>
					<tr>
						<th><label for="HeSoLuong"><?php esc_html_e( 'Hệ số lương', 'quanlicb' ); ?> *</label></th>
						<td><input type="number" id="HeSoLuong" name="HeSoLuong" value="<?php echo esc_attr( $v( 'HeSoLuong', '1' ) ); ?>" step="0.01" min="0.01" required class="quanlicb-calc-input" /></td>
					</tr>
					<tr>
						<th><label for="LuongCoBan"><?php esc_html_e( 'Lương cơ bản', 'quanlicb' ); ?> *</label></th>
						<td><input type="number" id="LuongCoBan" name="LuongCoBan" value="<?php echo esc_attr( $v( 'LuongCoBan', '0' ) ); ?>" step="1000" min="0" required class="quanlicb-calc-input" /></td>
					</tr>
					<tr class="quanlicb-salary-row">
						<th><?php esc_html_e( 'Tổng lương', 'quanlicb' ); ?></th>
						<td>
							<span id="quanlicb-tong-luong-preview" class="quanlicb-tongluong"><?php echo esc_html( number_format( $tong, 0, ',', '.' ) ); ?></span>
							<p class="description"><?php esc_html_e( 'HeSoLuong x LuongCoBan', 'quanlicb' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Ảnh đại diện', 'quanlicb' ); ?></th>
						<td>
							<input type="hidden" id="AnhDaiDien" name="AnhDaiDien" value="<?php echo esc_attr( $anh_id ); ?>" />
							<div id="quanlicb-preview" class="quanlicb-media-preview">
								<?php if ( $anh_url ) : ?><img src="<?php echo esc_url( $anh_url ); ?>" alt="" /><?php endif; ?>
							</div>
							<div class="quanlicb-media-actions">
								<button type="button" class="button" id="quanlicb-upload-btn"><?php esc_html_e( 'Chọn ảnh', 'quanlicb' ); ?></button>
								<button type="button" class="button" id="quanlicb-remove-img" <?php echo $anh_id ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'Xóa ảnh', 'quanlicb' ); ?></button>
							</div>
						</td>
					</tr>
				</table>
			</div>

			<aside class="quanlicb-form-aside">
				<?php if ( $is_edit ) : ?>
					<div class="quanlicb-form-panel quanlicb-form-panel--accent">
						<div class="quanlicb-form-panel__head">
							<h2><?php esc_html_e( 'Tóm tắt nhanh', 'quanlicb' ); ?></h2>
							<p><?php esc_html_e( 'Kiểm tra các trường quan trọng trước khi lưu.', 'quanlicb' ); ?></p>
						</div>
						<ul class="quanlicb-summary-list">
							<li><span><?php esc_html_e( 'Mã hồ sơ', 'quanlicb' ); ?></span><strong id="quanlicb-summary-macb"><?php echo esc_html( $v( 'MaCB', '---' ) ); ?></strong></li>
							<li><span><?php esc_html_e( 'Phòng ban', 'quanlicb' ); ?></span><strong id="quanlicb-summary-phongban"><?php echo esc_html( $v( 'PhongBan', '---' ) ); ?></strong></li>
							<li><span><?php esc_html_e( 'Chức vụ', 'quanlicb' ); ?></span><strong id="quanlicb-summary-chucvu"><?php echo esc_html( $v( 'ChucVu', '---' ) ); ?></strong></li>
							<li><span><?php esc_html_e( 'Giới tính', 'quanlicb' ); ?></span><strong id="quanlicb-summary-gioitinh"><?php echo esc_html( $v( 'GioiTinh', 'Nam' ) ); ?></strong></li>
							<li><span><?php esc_html_e( 'Tổng lương', 'quanlicb' ); ?></span><strong id="quanlicb-aside-tong-luong"><?php echo esc_html( number_format( $tong, 0, ',', '.' ) ); ?> đ</strong></li>
						</ul>
					</div>
				<?php endif; ?>

				<div class="quanlicb-form-panel">
					<div class="quanlicb-form-panel__head">
						<h2><?php esc_html_e( 'Gợi ý nhập liệu', 'quanlicb' ); ?></h2>
						<p><?php esc_html_e( 'Giữ định dạng thống nhất để tìm kiếm và thống kê chính xác.', 'quanlicb' ); ?></p>
					</div>
					<ul class="quanlicb-summary-list">
						<li><span><?php esc_html_e( 'Mã cán bộ', 'quanlicb' ); ?></span><strong><?php esc_html_e( 'CB001, CB002...', 'quanlicb' ); ?></strong></li>
						<li><span><?php esc_html_e( 'Phòng ban / Chức vụ', 'quanlicb' ); ?></span><strong><?php esc_html_e( 'Chọn từ danh mục có sẵn', 'quanlicb' ); ?></strong></li>
						<li><span><?php esc_html_e( 'Lương cơ bản', 'quanlicb' ); ?></span><strong><?php esc_html_e( 'Nhập theo VND', 'quanlicb' ); ?></strong></li>
					</ul>
				</div>
			</aside>
		</div>

		<p class="quanlicb-form-actions">
			<button type="submit" class="button button-primary"><?php echo $is_edit ? esc_html__( 'Cập nhật', 'quanlicb' ) : esc_html__( 'Thêm mới', 'quanlicb' ); ?></button>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=quanlicb' ) ); ?>" class="button"><?php esc_html_e( 'Hủy', 'quanlicb' ); ?></a>
		</p>
	</form>
</div>
</div>

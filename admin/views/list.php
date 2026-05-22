<?php
/**
 * Danh sách cán bộ.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$base_url = admin_url( 'admin.php?page=quanlicb' );
$title    = __( 'Danh sách cán bộ', 'quanlicb' );
$actions  = '';

$active_filters = count(
	array_filter(
		array(
			$args['macb'],
			$args['hoten'],
			$args['phongban'],
			$args['chucvu'],
			$args['gioitinh'],
			$args['luong_min'],
			$args['luong_max'],
		),
		static function ( $value ) {
			return '' !== $value;
		}
	)
);
?>
<div class="wrap quanlicb-wrap">
	<?php include QUANLICB_PATH . 'admin/views/partials/header.php'; ?>

	<?php if ( isset( $_GET['created'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Đã thêm cán bộ thành công.', 'quanlicb' ); ?></p></div>
	<?php endif; ?>
	<?php if ( isset( $_GET['updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Đã cập nhật cán bộ thành công.', 'quanlicb' ); ?></p></div>
	<?php endif; ?>
	<?php if ( isset( $_GET['deleted'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Đã xóa cán bộ.', 'quanlicb' ); ?></p></div>
	<?php endif; ?>
	<?php if ( isset( $_GET['error'] ) ) : ?>
		<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Không thể lưu dữ liệu. Vui lòng kiểm tra lại thông tin nhập.', 'quanlicb' ); ?></p></div>
	<?php endif; ?>

	<div class="quanlicb-panel-grid">
		<div class="quanlicb-box quanlicb-box--filter">
			<div class="quanlicb-box-head">
				<div>
					<p class="quanlicb-box-title"><?php esc_html_e( 'Tìm kiếm và bộ lọc', 'quanlicb' ); ?></p>
					<p class="quanlicb-meta"><?php esc_html_e( 'Thu hẹp dữ liệu theo mã, tên, phòng ban, chức vụ, giới tính và khoảng lương.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-box-body">
				<form method="get">
					<input type="hidden" name="page" value="quanlicb" />
					<div class="quanlicb-filter-row">
						<div>
							<label><?php esc_html_e( 'Mã CB', 'quanlicb' ); ?></label>
							<input type="text" name="s_macb" value="<?php echo esc_attr( $args['macb'] ); ?>" placeholder="CB001" />
						</div>
						<div>
							<label><?php esc_html_e( 'Họ tên', 'quanlicb' ); ?></label>
							<input type="text" name="s_hoten" value="<?php echo esc_attr( $args['hoten'] ); ?>" placeholder="Nhập tên cán bộ" />
						</div>
						<div>
							<label><?php esc_html_e( 'Phòng ban', 'quanlicb' ); ?></label>
							<div class="quanlicb-select-wrap">
								<select name="s_phongban">
									<option value=""><?php esc_html_e( 'Tất cả', 'quanlicb' ); ?></option>
									<?php foreach ( $phong_bans as $pb ) : ?>
										<option value="<?php echo esc_attr( $pb ); ?>" <?php selected( $args['phongban'], $pb ); ?>><?php echo esc_html( $pb ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div>
							<label><?php esc_html_e( 'Chức vụ', 'quanlicb' ); ?></label>
							<div class="quanlicb-select-wrap">
								<select name="s_chucvu">
									<option value=""><?php esc_html_e( 'Tất cả', 'quanlicb' ); ?></option>
									<?php foreach ( $chuc_vus as $cv ) : ?>
										<option value="<?php echo esc_attr( $cv ); ?>" <?php selected( $args['chucvu'], $cv ); ?>><?php echo esc_html( $cv ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div>
							<label><?php esc_html_e( 'Giới tính', 'quanlicb' ); ?></label>
							<div class="quanlicb-select-wrap">
								<select name="s_gioitinh">
									<option value=""><?php esc_html_e( 'Tất cả', 'quanlicb' ); ?></option>
									<option value="Nam" <?php selected( $args['gioitinh'], 'Nam' ); ?>><?php esc_html_e( 'Nam', 'quanlicb' ); ?></option>
									<option value="Nu" <?php selected( $args['gioitinh'], 'Nu' ); ?>><?php esc_html_e( 'Nữ', 'quanlicb' ); ?></option>
									<option value="Khac" <?php selected( $args['gioitinh'], 'Khac' ); ?>><?php esc_html_e( 'Khác', 'quanlicb' ); ?></option>
								</select>
							</div>
						</div>
						<div>
							<label><?php esc_html_e( 'Lương từ', 'quanlicb' ); ?></label>
							<input type="number" min="0" step="1000" name="s_luong_min" value="<?php echo esc_attr( $args['luong_min'] ); ?>" placeholder="5000000" />
						</div>
						<div>
							<label><?php esc_html_e( 'Lương đến', 'quanlicb' ); ?></label>
							<input type="number" min="0" step="1000" name="s_luong_max" value="<?php echo esc_attr( $args['luong_max'] ); ?>" placeholder="15000000" />
						</div>
						<div>
							<label><?php esc_html_e( 'Sắp xếp theo', 'quanlicb' ); ?></label>
							<div class="quanlicb-select-wrap">
								<select name="orderby">
									<option value="macb" <?php selected( $result['orderby'], 'macb' ); ?>><?php esc_html_e( 'Mã cán bộ', 'quanlicb' ); ?></option>
									<option value="hoten" <?php selected( $result['orderby'], 'hoten' ); ?>><?php esc_html_e( 'Họ tên', 'quanlicb' ); ?></option>
									<option value="ngaysinh" <?php selected( $result['orderby'], 'ngaysinh' ); ?>><?php esc_html_e( 'Ngày sinh', 'quanlicb' ); ?></option>
									<option value="phongban" <?php selected( $result['orderby'], 'phongban' ); ?>><?php esc_html_e( 'Phòng ban', 'quanlicb' ); ?></option>
									<option value="chucvu" <?php selected( $result['orderby'], 'chucvu' ); ?>><?php esc_html_e( 'Chức vụ', 'quanlicb' ); ?></option>
									<option value="tongluong" <?php selected( $result['orderby'], 'tongluong' ); ?>><?php esc_html_e( 'Tổng lương', 'quanlicb' ); ?></option>
								</select>
							</div>
						</div>
						<div>
							<label><?php esc_html_e( 'Thứ tự', 'quanlicb' ); ?></label>
							<div class="quanlicb-select-wrap">
								<select name="order">
									<option value="asc" <?php selected( $result['order'], 'asc' ); ?>><?php esc_html_e( 'Tăng dần', 'quanlicb' ); ?></option>
									<option value="desc" <?php selected( $result['order'], 'desc' ); ?>><?php esc_html_e( 'Giảm dần', 'quanlicb' ); ?></option>
								</select>
							</div>
						</div>
					</div>
					<div class="quanlicb-filter-actions">
						<button type="submit" class="button button-primary"><?php esc_html_e( 'Lọc dữ liệu', 'quanlicb' ); ?></button>
						<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array_filter( array( 'page' => 'quanlicb', 'action' => 'export', 's_macb' => $args['macb'], 's_hoten' => $args['hoten'], 's_phongban' => $args['phongban'], 's_chucvu' => $args['chucvu'], 's_gioitinh' => $args['gioitinh'], 's_luong_min' => $args['luong_min'], 's_luong_max' => $args['luong_max'], 'orderby' => $result['orderby'], 'order' => $result['order'] ) ), admin_url( 'admin.php' ) ), 'quanlicb_export' ) ); ?>" class="button"><?php esc_html_e( 'Xuất CSV', 'quanlicb' ); ?></a>
						<a href="<?php echo esc_url( $base_url ); ?>" class="button"><?php esc_html_e( 'Bỏ lọc', 'quanlicb' ); ?></a>
					</div>
				</form>
			</div>
		</div>

		<aside class="quanlicb-box quanlicb-box--side">
			<div class="quanlicb-box-head">
				<div>
					<p class="quanlicb-box-title"><?php esc_html_e( 'Tổng quan nhanh', 'quanlicb' ); ?></p>
					<p class="quanlicb-meta"><?php esc_html_e( 'Nhìn nhanh trạng thái dữ liệu hiện tại.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-box-body">
				<ul class="quanlicb-summary-list">
					<li><span><?php esc_html_e( 'Tổng bản ghi hiển thị', 'quanlicb' ); ?></span><strong><?php echo esc_html( number_format_i18n( (int) $result['total'] ) ); ?></strong></li>
					<li><span><?php esc_html_e( 'Phòng ban có dữ liệu', 'quanlicb' ); ?></span><strong><?php echo esc_html( number_format_i18n( count( $phong_bans ) ) ); ?></strong></li>
					<li><span><?php esc_html_e( 'Chức vụ có dữ liệu', 'quanlicb' ); ?></span><strong><?php echo esc_html( number_format_i18n( count( $chuc_vus ) ) ); ?></strong></li>
					<li><span><?php esc_html_e( 'Bộ lọc đang bật', 'quanlicb' ); ?></span><strong><?php echo esc_html( number_format_i18n( $active_filters ) ); ?></strong></li>
					<li><span><?php esc_html_e( 'Tổng quỹ lương lọc được', 'quanlicb' ); ?></span><strong><?php echo esc_html( number_format( (float) $result['total_luong'], 0, ',', '.' ) ); ?> đ</strong></li>
				</ul>
			</div>
		</aside>
	</div>

	<div class="quanlicb-box">
		<div class="quanlicb-section-head">
			<div>
				<p class="quanlicb-section-head__eyebrow"><?php esc_html_e( 'Danh sách dữ liệu', 'quanlicb' ); ?></p>
				<p class="quanlicb-meta">
					<strong><?php echo esc_html( (string) (int) $result['total'] ); ?></strong>
					<?php
					printf(
						esc_html__( ' cán bộ - trang %1$d/%2$d', 'quanlicb' ),
						(int) $result['paged'],
						(int) $result['pages']
					);
					?>
				</p>
			</div>
			<div class="quanlicb-section-head__badge"><?php esc_html_e( 'Cập nhật theo bộ lọc', 'quanlicb' ); ?></div>
		</div>
		<div class="quanlicb-table-wrap">
			<table class="wp-list-table widefat striped quanlicb-table">
				<thead>
					<tr>
						<th class="col-img"><?php esc_html_e( 'Ảnh', 'quanlicb' ); ?></th>
						<th><?php esc_html_e( 'Mã', 'quanlicb' ); ?></th>
						<th><?php esc_html_e( 'Họ tên', 'quanlicb' ); ?></th>
						<th><?php esc_html_e( 'Ngày sinh', 'quanlicb' ); ?></th>
						<th><?php esc_html_e( 'GT', 'quanlicb' ); ?></th>
						<th><?php esc_html_e( 'Phòng ban', 'quanlicb' ); ?></th>
						<th><?php esc_html_e( 'Chức vụ', 'quanlicb' ); ?></th>
						<th><?php esc_html_e( 'Hệ số', 'quanlicb' ); ?></th>
						<th><?php esc_html_e( 'Lương CB', 'quanlicb' ); ?></th>
						<th><?php esc_html_e( 'Tổng lương', 'quanlicb' ); ?></th>
						<?php if ( $can_edit || $can_delete ) : ?>
							<th><?php esc_html_e( 'Thao tác', 'quanlicb' ); ?></th>
						<?php endif; ?>
						<?php if ( ! $can_edit && ! $can_delete ) : ?>
							<th><?php esc_html_e( 'Chi tiết', 'quanlicb' ); ?></th>
						<?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $result['items'] ) ) : ?>
						<tr><td colspan="11" class="quanlicb-empty"><?php esc_html_e( 'Không có dữ liệu phù hợp.', 'quanlicb' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $result['items'] as $row ) : ?>
							<tr>
								<td class="col-img">
									<?php
									if ( ! empty( $row['AnhDaiDien'] ) ) {
										echo wp_get_attachment_image( (int) $row['AnhDaiDien'], array( 40, 40 ), false, array( 'class' => 'quanlicb-avatar' ) );
									} else {
										echo '<span class="quanlicb-avatar-fallback">N/A</span>';
									}
									?>
								</td>
								<td><strong><?php echo esc_html( $row['MaCB'] ); ?></strong></td>
								<td>
									<div class="quanlicb-person">
										<strong><a class="quanlicb-row-link" href="<?php echo esc_url( add_query_arg( array( 'action' => 'view', 'macb' => $row['MaCB'] ), $base_url ) ); ?>"><?php echo esc_html( $row['HoTen'] ); ?></a></strong>
										<span><?php esc_html_e( 'Hồ sơ nội bộ', 'quanlicb' ); ?></span>
									</div>
								</td>
								<td><?php echo esc_html( date_i18n( 'd/m/Y', strtotime( $row['NgaySinh'] ) ) ); ?></td>
								<td><span class="quanlicb-inline-badge"><?php echo esc_html( $row['GioiTinh'] ); ?></span></td>
								<td><span class="quanlicb-inline-badge quanlicb-inline-badge--soft"><?php echo esc_html( $row['PhongBan'] ); ?></span></td>
								<td><span class="quanlicb-inline-badge quanlicb-inline-badge--soft"><?php echo esc_html( $row['ChucVu'] ); ?></span></td>
								<td><?php echo esc_html( number_format( (float) $row['HeSoLuong'], 2 ) ); ?></td>
								<td><?php echo esc_html( number_format( (float) $row['LuongCoBan'], 0, ',', '.' ) ); ?> đ</td>
								<td class="tong-luong"><?php echo esc_html( number_format( (float) $row['TongLuong'], 0, ',', '.' ) ); ?> đ</td>
								<?php if ( $can_edit || $can_delete ) : ?>
									<td class="quanlicb-actions-cell">
										<div class="quanlicb-actions">
											<a class="quanlicb-act-view" href="<?php echo esc_url( add_query_arg( array( 'action' => 'view', 'macb' => $row['MaCB'] ), $base_url ) ); ?>"><?php esc_html_e( 'Xem', 'quanlicb' ); ?></a>
											<?php if ( $can_edit ) : ?>
												<a class="quanlicb-act-edit" href="<?php echo esc_url( add_query_arg( array( 'action' => 'edit', 'macb' => $row['MaCB'] ), $base_url ) ); ?>"><?php esc_html_e( 'Sửa', 'quanlicb' ); ?></a>
											<?php endif; ?>
											<?php if ( $can_delete ) : ?>
												<?php $del_url = wp_nonce_url( add_query_arg( array( 'action' => 'delete', 'macb' => $row['MaCB'] ), $base_url ), 'quanlicb_delete_' . $row['MaCB'] ); ?>
												<a class="quanlicb-act-delete" href="<?php echo esc_url( $del_url ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Xóa?', 'quanlicb' ) ); ?>');"><?php esc_html_e( 'Xóa', 'quanlicb' ); ?></a>
											<?php endif; ?>
										</div>
									</td>
								<?php else : ?>
									<td>
										<div class="quanlicb-actions">
											<a class="quanlicb-act-view" href="<?php echo esc_url( add_query_arg( array( 'action' => 'view', 'macb' => $row['MaCB'] ), $base_url ) ); ?>"><?php esc_html_e( 'Xem', 'quanlicb' ); ?></a>
										</div>
									</td>
								<?php endif; ?>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

		<?php if ( $result['pages'] > 1 ) : ?>
			<div class="quanlicb-pager">
				<?php
				$filter_args = array_filter(
					array(
						'page'        => 'quanlicb',
						's_macb'      => $args['macb'],
						's_hoten'     => $args['hoten'],
						's_phongban'  => $args['phongban'],
						's_chucvu'    => $args['chucvu'],
						's_gioitinh'  => $args['gioitinh'],
						's_luong_min' => $args['luong_min'],
						's_luong_max' => $args['luong_max'],
						'orderby'     => $result['orderby'],
						'order'       => $result['order'],
					)
				);
				if ( $result['paged'] > 1 ) {
					echo '<a class="button" href="' . esc_url( add_query_arg( array_merge( $filter_args, array( 'paged' => $result['paged'] - 1 ) ), admin_url( 'admin.php' ) ) ) . '">Prev</a>';
				}
				echo '<span class="page-num">' . esc_html( $result['paged'] ) . ' / ' . esc_html( $result['pages'] ) . '</span>';
				if ( $result['paged'] < $result['pages'] ) {
					echo '<a class="button" href="' . esc_url( add_query_arg( array_merge( $filter_args, array( 'paged' => $result['paged'] + 1 ) ), admin_url( 'admin.php' ) ) ) . '">Next</a>';
				}
				?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( ! $can_edit && ! $can_delete ) : ?>
		<p class="quanlicb-hint"><?php esc_html_e( 'Tài khoản nhân viên chỉ có quyền xem.', 'quanlicb' ); ?></p>
	<?php endif; ?>
</div>
</div>

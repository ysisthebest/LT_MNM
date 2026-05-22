<?php
/**
 * Dashboard tổng quan.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title   = __( 'Dashboard tổng quan', 'quanlicb' );
$actions = '';

$labels_pb = array();
$data_pb   = array();
foreach ( $by_pb as $row ) {
	$labels_pb[] = $row['PhongBan'];
	$data_pb[]   = (int) $row['so_luong'];
}

$labels_cv = array();
$data_cv   = array();
foreach ( $by_cv as $row ) {
	$labels_cv[] = $row['ChucVu'];
	$data_cv[]   = (int) $row['so_luong'];
}
?>
<div class="wrap quanlicb-wrap">
	<?php include QUANLICB_PATH . 'admin/views/partials/header.php'; ?>

	<div class="quanlicb-overview-grid quanlicb-overview-grid--four">
		<div class="quanlicb-overview-card">
			<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Tổng cán bộ', 'quanlicb' ); ?></span>
			<strong class="quanlicb-overview-card__value"><?php echo esc_html( number_format_i18n( $total_cb ) ); ?></strong>
			<span class="quanlicb-overview-card__meta"><?php esc_html_e( 'Tổng số hồ sơ cán bộ hiện có.', 'quanlicb' ); ?></span>
		</div>
		<div class="quanlicb-overview-card quanlicb-overview-card--accent">
			<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Tổng quỹ lương', 'quanlicb' ); ?></span>
			<strong class="quanlicb-overview-card__value"><?php echo esc_html( number_format( $total_luong, 0, ',', '.' ) ); ?> đ</strong>
			<span class="quanlicb-overview-card__meta"><?php esc_html_e( 'Tổng TongLuong của toàn bộ cán bộ.', 'quanlicb' ); ?></span>
		</div>
		<div class="quanlicb-overview-card">
			<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Phòng ban / Chức vụ', 'quanlicb' ); ?></span>
			<strong class="quanlicb-overview-card__value"><?php echo esc_html( $total_departments . ' / ' . $total_positions ); ?></strong>
			<span class="quanlicb-overview-card__meta"><?php esc_html_e( 'Số lượng danh mục nhân sự.', 'quanlicb' ); ?></span>
		</div>
		<div class="quanlicb-overview-card">
			<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Lương trung bình', 'quanlicb' ); ?></span>
			<strong class="quanlicb-overview-card__value"><?php echo esc_html( number_format( $average_luong, 0, ',', '.' ) ); ?> đ</strong>
			<span class="quanlicb-overview-card__meta"><?php esc_html_e( 'Mức trung bình trên mỗi cán bộ.', 'quanlicb' ); ?></span>
		</div>
	</div>

	<div class="quanlicb-charts-row">
		<div class="quanlicb-chart-box">
			<div class="quanlicb-chart-box__head">
				<div>
					<h3><?php esc_html_e( 'Nhân sự theo phòng ban', 'quanlicb' ); ?></h3>
					<p><?php esc_html_e( 'So sánh quy mô nhân sự giữa các phòng ban.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-chart-canvas quanlicb-chart-canvas--bar">
				<canvas id="chartDashboardPhongBan"></canvas>
			</div>
		</div>
		<div class="quanlicb-chart-box">
			<div class="quanlicb-chart-box__head">
				<div>
					<h3><?php esc_html_e( 'Nhân sự theo chức vụ', 'quanlicb' ); ?></h3>
					<p><?php esc_html_e( 'Cơ cấu nhân sự theo cấp bậc chức vụ.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-chart-canvas quanlicb-chart-canvas--pie">
				<canvas id="chartDashboardChucVu"></canvas>
			</div>
		</div>
	</div>

	<div class="quanlicb-panel-grid">
		<div class="quanlicb-box">
			<div class="quanlicb-box-head">
				<div>
					<p class="quanlicb-box-title"><?php esc_html_e( 'Top lương cao nhất', 'quanlicb' ); ?></p>
					<p class="quanlicb-meta"><?php esc_html_e( 'Danh sách cán bộ có TongLuong cao nhất hiện tại.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-table-wrap">
				<table class="wp-list-table widefat striped quanlicb-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Mã CB', 'quanlicb' ); ?></th>
							<th><?php esc_html_e( 'Họ tên', 'quanlicb' ); ?></th>
							<th><?php esc_html_e( 'Phòng ban', 'quanlicb' ); ?></th>
							<th><?php esc_html_e( 'Chức vụ', 'quanlicb' ); ?></th>
							<th><?php esc_html_e( 'Tổng lương', 'quanlicb' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $top_luong ) ) : ?>
							<tr><td colspan="5" class="quanlicb-empty"><?php esc_html_e( 'Chưa có dữ liệu.', 'quanlicb' ); ?></td></tr>
						<?php else : ?>
							<?php foreach ( $top_luong as $row ) : ?>
								<tr>
									<td><strong><?php echo esc_html( $row['MaCB'] ); ?></strong></td>
									<td><?php echo esc_html( $row['HoTen'] ); ?></td>
									<td><?php echo esc_html( $row['PhongBan'] ); ?></td>
									<td><?php echo esc_html( $row['ChucVu'] ); ?></td>
									<td class="tong-luong"><?php echo esc_html( number_format( (float) $row['TongLuong'], 0, ',', '.' ) ); ?> đ</td>
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
					<p class="quanlicb-box-title"><?php esc_html_e( 'Nhật ký gần đây', 'quanlicb' ); ?></p>
					<p class="quanlicb-meta"><?php esc_html_e( 'Truy vết các thao tác tạo/sửa/xóa/import.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-box-body">
				<ul class="quanlicb-summary-list">
					<?php if ( empty( $recent_logs ) ) : ?>
						<li><span><?php esc_html_e( 'Chưa có log', 'quanlicb' ); ?></span><strong>-</strong></li>
					<?php else : ?>
						<?php foreach ( $recent_logs as $log ) : ?>
							<li>
								<span><?php echo esc_html( '[' . $log['action'] . '] ' . $log['message'] ); ?></span>
								<strong><?php echo esc_html( wp_date( 'd/m H:i', strtotime( $log['created_at'] ) ) ); ?></strong>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
		</aside>
	</div>

	<script type="application/json" id="quanlicb-dashboard-chart-data">
		<?php
		echo wp_json_encode(
			array(
				'phongBan' => array( 'labels' => $labels_pb, 'data' => $data_pb ),
				'chucVu'   => array( 'labels' => $labels_cv, 'data' => $data_cv ),
			)
		);
		?>
	</script>
</div>
</div>

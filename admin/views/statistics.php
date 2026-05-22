<?php
/**
 * Thống kê & báo cáo (gộp tổng quan + báo cáo theo kỳ).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$labels_pb = array();
$data_pb   = array();
if ( $by_pb ) {
	foreach ( $by_pb as $row ) {
		$labels_pb[] = $row['PhongBan'];
		$data_pb[]   = (int) $row['so_luong'];
	}
}

$labels_gt = array();
$data_gt   = array();
if ( $by_gt ) {
	foreach ( $by_gt as $row ) {
		$labels_gt[] = $row['GioiTinh'];
		$data_gt[]   = (int) $row['so_luong'];
	}
}

$labels_report_pb = array();
$data_report_pb   = array();
if ( $salary_by_pb ) {
	foreach ( $salary_by_pb as $row ) {
		$labels_report_pb[] = $row['PhongBan'];
		$data_report_pb[]   = (float) $row['tong_luong'];
	}
}

$print_stats_url = wp_nonce_url(
	add_query_arg(
		array(
			'page'   => 'quanlicb-stats',
			'action' => 'print_report',
		),
		admin_url( 'admin.php' )
	),
	'quanlicb_print_report'
);

$print_period_url = wp_nonce_url(
	add_query_arg(
		array(
			'page'      => 'quanlicb-stats',
			'action'    => 'print_advanced_report',
			'date_from' => $date_from,
			'date_to'   => $date_to,
		),
		admin_url( 'admin.php' )
	),
	'quanlicb_print_advanced_report'
);

$title   = __( 'Thống kê & Báo cáo', 'quanlicb' );
$actions = '<a href="' . esc_url( $print_stats_url ) . '" class="page-title-action" target="_blank" rel="noopener">' . esc_html__( 'In tổng quan', 'quanlicb' ) . '</a>';
$actions .= '<a href="' . esc_url( $print_period_url ) . '" class="page-title-action" target="_blank" rel="noopener">' . esc_html__( 'In theo kỳ', 'quanlicb' ) . '</a>';
?>
<div class="wrap quanlicb-wrap">
	<?php include QUANLICB_PATH . 'admin/views/partials/header.php'; ?>

	<div class="quanlicb-section-head quanlicb-section-head--page">
		<div>
			<p class="quanlicb-section-head__eyebrow"><?php esc_html_e( 'Tổng quan', 'quanlicb' ); ?></p>
			<p class="quanlicb-meta"><?php esc_html_e( 'Số liệu toàn hệ thống và biểu đồ nhân sự hiện tại.', 'quanlicb' ); ?></p>
		</div>
	</div>

	<div class="quanlicb-stats-row">
		<div class="quanlicb-stat-item">
			<span class="label"><?php esc_html_e( 'Tổng cán bộ', 'quanlicb' ); ?></span>
			<span class="value"><?php echo esc_html( number_format( $total_cb ) ); ?></span>
		</div>
		<div class="quanlicb-stat-item">
			<span class="label"><?php esc_html_e( 'Tổng quỹ lương', 'quanlicb' ); ?></span>
			<span class="value"><?php echo esc_html( number_format( $total_luong, 0, ',', '.' ) ); ?> đ</span>
		</div>
	</div>

	<div class="quanlicb-overview-grid quanlicb-overview-grid--three">
		<div class="quanlicb-overview-card">
			<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Bộ dữ liệu phòng ban', 'quanlicb' ); ?></span>
			<strong class="quanlicb-overview-card__value"><?php echo esc_html( $total_departments ); ?></strong>
			<span class="quanlicb-overview-card__meta"><?php esc_html_e( 'Số nhóm đang hiển thị trên biểu đồ cột.', 'quanlicb' ); ?></span>
		</div>
		<div class="quanlicb-overview-card">
			<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Cơ cấu giới tính', 'quanlicb' ); ?></span>
			<strong class="quanlicb-overview-card__value"><?php echo esc_html( count( $labels_gt ) ); ?></strong>
			<span class="quanlicb-overview-card__meta"><?php esc_html_e( 'Số nhóm đang hiển thị trên biểu đồ tròn.', 'quanlicb' ); ?></span>
		</div>
		<div class="quanlicb-overview-card quanlicb-overview-card--accent">
			<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Mức lương trung bình', 'quanlicb' ); ?></span>
			<strong class="quanlicb-overview-card__value"><?php echo esc_html( number_format( $average_luong, 0, ',', '.' ) ); ?> đ</strong>
			<span class="quanlicb-overview-card__meta"><?php esc_html_e( 'Được tính theo tổng quỹ lương và số cán bộ hiện có.', 'quanlicb' ); ?></span>
		</div>
	</div>

	<div class="quanlicb-charts-row">
		<div class="quanlicb-chart-box">
			<div class="quanlicb-chart-box__head">
				<div>
					<h3><?php esc_html_e( 'Theo phòng ban', 'quanlicb' ); ?></h3>
					<p><?php esc_html_e( 'So sánh quy mô nhân sự giữa các đơn vị.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-chart-canvas quanlicb-chart-canvas--bar">
				<canvas id="chartPhongBan"></canvas>
			</div>
		</div>
		<div class="quanlicb-chart-box">
			<div class="quanlicb-chart-box__head">
				<div>
					<h3><?php esc_html_e( 'Theo giới tính', 'quanlicb' ); ?></h3>
					<p><?php esc_html_e( 'Phân bố nhân sự theo nhóm giới tính đã khai báo.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-chart-canvas quanlicb-chart-canvas--pie">
				<canvas id="chartGioiTinh"></canvas>
			</div>
		</div>
	</div>

	<div class="quanlicb-panel-grid">
		<div class="quanlicb-box">
			<div class="quanlicb-box-head">
				<div>
					<p class="quanlicb-box-title"><?php esc_html_e( 'Chi tiết phòng ban', 'quanlicb' ); ?></p>
					<p class="quanlicb-meta"><?php esc_html_e( 'Bảng tổng hợp số lượng cán bộ theo từng đơn vị.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-table-wrap">
				<table class="wp-list-table widefat striped quanlicb-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Phòng ban', 'quanlicb' ); ?></th>
							<th style="width:120px"><?php esc_html_e( 'Số lượng', 'quanlicb' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $by_pb ) ) : ?>
							<tr><td colspan="2" class="quanlicb-empty"><?php esc_html_e( 'Chưa có dữ liệu.', 'quanlicb' ); ?></td></tr>
						<?php else : ?>
							<?php foreach ( $by_pb as $row ) : ?>
								<tr>
									<td><span class="quanlicb-inline-badge quanlicb-inline-badge--soft"><?php echo esc_html( $row['PhongBan'] ); ?></span></td>
									<td><strong><?php echo esc_html( $row['so_luong'] ); ?></strong></td>
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
					<p class="quanlicb-box-title"><?php esc_html_e( 'Nhận định nhanh', 'quanlicb' ); ?></p>
					<p class="quanlicb-meta"><?php esc_html_e( 'Các chỉ số rút gọn để đọc báo cáo nhanh hơn.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-box-body">
				<ul class="quanlicb-summary-list">
					<li><span><?php esc_html_e( 'Tổng phòng ban', 'quanlicb' ); ?></span><strong><?php echo esc_html( $total_departments ); ?></strong></li>
					<li><span><?php esc_html_e( 'Tổng nhóm giới tính', 'quanlicb' ); ?></span><strong><?php echo esc_html( count( $labels_gt ) ); ?></strong></li>
					<li><span><?php esc_html_e( 'Tổng quỹ lương', 'quanlicb' ); ?></span><strong><?php echo esc_html( number_format( $total_luong, 0, ',', '.' ) ); ?> đ</strong></li>
					<li><span><?php esc_html_e( 'Bình quân / cán bộ', 'quanlicb' ); ?></span><strong><?php echo esc_html( number_format( $average_luong, 0, ',', '.' ) ); ?> đ</strong></li>
				</ul>
			</div>
		</aside>
	</div>

	<div class="quanlicb-section-head quanlicb-section-head--page quanlicb-section-head--spaced">
		<div>
			<p class="quanlicb-section-head__eyebrow"><?php esc_html_e( 'Báo cáo theo kỳ', 'quanlicb' ); ?></p>
			<p class="quanlicb-meta"><?php esc_html_e( 'Lọc theo ngày cập nhật hồ sơ, xem lương và danh sách cán bộ trong khoảng thời gian.', 'quanlicb' ); ?></p>
		</div>
	</div>

	<div class="quanlicb-box quanlicb-box--filter">
		<div class="quanlicb-box-body">
			<form method="get">
				<input type="hidden" name="page" value="quanlicb-stats" />
				<div class="quanlicb-filter-row quanlicb-filter-row--report">
					<div>
						<label><?php esc_html_e( 'Từ ngày', 'quanlicb' ); ?></label>
						<input type="date" name="date_from" value="<?php echo esc_attr( $date_from ); ?>" />
					</div>
					<div>
						<label><?php esc_html_e( 'Đến ngày', 'quanlicb' ); ?></label>
						<input type="date" name="date_to" value="<?php echo esc_attr( $date_to ); ?>" />
					</div>
				</div>
				<div class="quanlicb-filter-actions">
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Áp dụng', 'quanlicb' ); ?></button>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=quanlicb-stats' ) ); ?>" class="button"><?php esc_html_e( 'Bỏ lọc', 'quanlicb' ); ?></a>
				</div>
			</form>
		</div>
	</div>

	<div class="quanlicb-overview-grid quanlicb-overview-grid--three">
		<div class="quanlicb-overview-card">
			<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Bản ghi trong kỳ', 'quanlicb' ); ?></span>
			<strong class="quanlicb-overview-card__value"><?php echo esc_html( number_format_i18n( $total_filtered ) ); ?></strong>
		</div>
		<div class="quanlicb-overview-card quanlicb-overview-card--accent">
			<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Tổng quỹ lương trong kỳ', 'quanlicb' ); ?></span>
			<strong class="quanlicb-overview-card__value"><?php echo esc_html( number_format( $total_filtered_q, 0, ',', '.' ) ); ?> đ</strong>
		</div>
		<div class="quanlicb-overview-card">
			<span class="quanlicb-overview-card__label"><?php esc_html_e( 'Phòng ban có dữ liệu', 'quanlicb' ); ?></span>
			<strong class="quanlicb-overview-card__value"><?php echo esc_html( number_format_i18n( count( $salary_by_pb ) ) ); ?></strong>
		</div>
	</div>

	<div class="quanlicb-charts-row">
		<div class="quanlicb-chart-box">
			<div class="quanlicb-chart-box__head">
				<div>
					<h3><?php esc_html_e( 'Tổng lương theo phòng ban', 'quanlicb' ); ?></h3>
					<p><?php esc_html_e( 'Tổng TongLuong của các phòng ban trong khoảng lọc.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-chart-canvas quanlicb-chart-canvas--bar">
				<canvas id="chartReportPhongBan"></canvas>
			</div>
		</div>
		<div class="quanlicb-chart-box">
			<div class="quanlicb-chart-box__head">
				<div>
					<h3><?php esc_html_e( 'Top 10 lương cao', 'quanlicb' ); ?></h3>
					<p><?php esc_html_e( 'Xếp hạng cán bộ có TongLuong cao nhất.', 'quanlicb' ); ?></p>
				</div>
			</div>
			<div class="quanlicb-table-wrap">
				<table class="wp-list-table widefat striped quanlicb-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Mã CB', 'quanlicb' ); ?></th>
							<th><?php esc_html_e( 'Họ tên', 'quanlicb' ); ?></th>
							<th><?php esc_html_e( 'Phòng ban', 'quanlicb' ); ?></th>
							<th><?php esc_html_e( 'Tổng lương', 'quanlicb' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $top_luong ) ) : ?>
							<tr><td colspan="4" class="quanlicb-empty"><?php esc_html_e( 'Chưa có dữ liệu.', 'quanlicb' ); ?></td></tr>
						<?php else : ?>
							<?php foreach ( $top_luong as $row ) : ?>
								<tr>
									<td><strong><?php echo esc_html( $row['MaCB'] ); ?></strong></td>
									<td><?php echo esc_html( $row['HoTen'] ); ?></td>
									<td><?php echo esc_html( $row['PhongBan'] ); ?></td>
									<td class="tong-luong"><?php echo esc_html( number_format( (float) $row['TongLuong'], 0, ',', '.' ) ); ?> đ</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="quanlicb-box">
		<div class="quanlicb-section-head">
			<div>
				<p class="quanlicb-section-head__eyebrow"><?php esc_html_e( 'Danh sách cán bộ trong kỳ', 'quanlicb' ); ?></p>
				<p class="quanlicb-meta"><?php esc_html_e( 'Dữ liệu theo updated_at trong khoảng lọc.', 'quanlicb' ); ?></p>
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
						<th><?php esc_html_e( 'Cập nhật lúc', 'quanlicb' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $filtered_items ) ) : ?>
						<tr><td colspan="6" class="quanlicb-empty"><?php esc_html_e( 'Không có dữ liệu trong khoảng lọc.', 'quanlicb' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $filtered_items as $row ) : ?>
							<tr>
								<td><strong><?php echo esc_html( $row['MaCB'] ); ?></strong></td>
								<td><?php echo esc_html( $row['HoTen'] ); ?></td>
								<td><?php echo esc_html( $row['PhongBan'] ); ?></td>
								<td><?php echo esc_html( $row['ChucVu'] ); ?></td>
								<td class="tong-luong"><?php echo esc_html( number_format( (float) $row['TongLuong'], 0, ',', '.' ) ); ?> đ</td>
								<td><?php echo esc_html( wp_date( 'd/m/Y H:i:s', strtotime( $row['updated_at'] ) ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>

	<script type="application/json" id="quanlicb-chart-data">
		<?php
		echo wp_json_encode(
			array(
				'phongBan'       => array( 'labels' => $labels_pb, 'data' => $data_pb ),
				'gioiTinh'       => array( 'labels' => $labels_gt, 'data' => $data_gt ),
				'reportPhongBan' => array( 'labels' => $labels_report_pb, 'data' => $data_report_pb ),
			)
		);
		?>
	</script>
</div>
</div>

<?php
/**
 * Bản in báo cáo nang cao.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html lang="vi">
<head>
	<meta charset="utf-8" />
	<title><?php esc_html_e( 'Báo cáo nâng cao - quản lý cán bộ', 'quanlicb' ); ?></title>
	<style>
		body { font-family: Arial, sans-serif; margin: 24px; color: #111827; }
		h1, h2 { margin: 0 0 12px; }
		p { margin: 0 0 8px; }
		.report-meta { margin-bottom: 18px; }
		.report-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin: 20px 0; }
		.report-card { border: 1px solid #d1d5db; border-radius: 12px; padding: 14px; }
		.report-card span { display: block; font-size: 12px; text-transform: uppercase; color: #6b7280; margin-bottom: 8px; }
		.report-card strong { font-size: 24px; }
		table { width: 100%; border-collapse: collapse; margin-top: 18px; }
		th, td { border: 1px solid #d1d5db; padding: 10px; text-align: left; }
		th { background: #f3f4f6; }
		.section { margin-top: 26px; }
		@media print { .no-print { display: none; } body { margin: 0; } }
	</style>
</head>
<body onload="window.print()">
	<div class="no-print" style="margin-bottom:16px;">
		<button onclick="window.print()"><?php esc_html_e( 'In lại', 'quanlicb' ); ?></button>
		<button onclick="window.close()"><?php esc_html_e( 'Đóng', 'quanlicb' ); ?></button>
	</div>

	<h1><?php esc_html_e( 'Báo cáo nâng cao quản lý cán bộ', 'quanlicb' ); ?></h1>
	<div class="report-meta">
		<p><?php esc_html_e( 'Bộ lọc theo ngày cập nhật hồ sơ cán bộ', 'quanlicb' ); ?></p>
		<p><?php echo esc_html( sprintf( __( 'Từ ngày: %1$s - Đến ngày: %2$s', 'quanlicb' ), $date_from ? $date_from : '-', $date_to ? $date_to : '-' ) ); ?></p>
		<p><?php echo esc_html( sprintf( __( 'Thời gian in báo cáo: %s', 'quanlicb' ), wp_date( 'd/m/Y H:i', $generated_at ) ) ); ?></p>
	</div>

	<div class="report-grid">
		<div class="report-card"><span><?php esc_html_e( 'Bản ghi trong kỳ', 'quanlicb' ); ?></span><strong><?php echo esc_html( number_format_i18n( $total_filtered ) ); ?></strong></div>
		<div class="report-card"><span><?php esc_html_e( 'Tổng quỹ lương trong kỳ', 'quanlicb' ); ?></span><strong><?php echo esc_html( number_format( $total_filtered_q, 0, ',', '.' ) ); ?> đ</strong></div>
		<div class="report-card"><span><?php esc_html_e( 'Phòng ban có dữ liệu', 'quanlicb' ); ?></span><strong><?php echo esc_html( number_format_i18n( count( $salary_by_pb ) ) ); ?></strong></div>
	</div>

	<div class="section">
		<h2><?php esc_html_e( 'Tổng lương theo phòng ban', 'quanlicb' ); ?></h2>
		<table>
			<thead>
				<tr>
					<th><?php esc_html_e( 'Phòng ban', 'quanlicb' ); ?></th>
					<th><?php esc_html_e( 'Số cán bộ', 'quanlicb' ); ?></th>
					<th><?php esc_html_e( 'Tổng lương', 'quanlicb' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $salary_by_pb ) ) : ?>
					<tr><td colspan="3"><?php esc_html_e( 'Không có dữ liệu.', 'quanlicb' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $salary_by_pb as $row ) : ?>
						<tr>
							<td><?php echo esc_html( $row['PhongBan'] ); ?></td>
							<td><?php echo esc_html( $row['so_luong'] ); ?></td>
							<td><?php echo esc_html( number_format( (float) $row['tong_luong'], 0, ',', '.' ) ); ?> đ</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

	<div class="section">
		<h2><?php esc_html_e( 'Top lương cao', 'quanlicb' ); ?></h2>
		<table>
			<thead>
				<tr>
					<th><?php esc_html_e( 'MaCB', 'quanlicb' ); ?></th>
					<th><?php esc_html_e( 'Họ tên', 'quanlicb' ); ?></th>
					<th><?php esc_html_e( 'Phòng ban', 'quanlicb' ); ?></th>
					<th><?php esc_html_e( 'Tổng lương', 'quanlicb' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $top_luong as $row ) : ?>
					<tr>
						<td><?php echo esc_html( $row['MaCB'] ); ?></td>
						<td><?php echo esc_html( $row['HoTen'] ); ?></td>
						<td><?php echo esc_html( $row['PhongBan'] ); ?></td>
						<td><?php echo esc_html( number_format( (float) $row['TongLuong'], 0, ',', '.' ) ); ?> đ</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</body>
</html>

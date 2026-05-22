<?php
/**
 * Nhật ký thao tác.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title   = __( 'Nhật ký thao tác', 'quanlicb' );
$actions = '';
?>
<div class="wrap quanlicb-wrap">
	<?php include QUANLICB_PATH . 'admin/views/partials/header.php'; ?>

	<div class="quanlicb-box quanlicb-box--filter">
		<div class="quanlicb-box-head">
			<div>
				<p class="quanlicb-box-title"><?php esc_html_e( 'Lọc nhật ký', 'quanlicb' ); ?></p>
				<p class="quanlicb-meta"><?php esc_html_e( 'Theo hành động, đối tượng và khoảng ngày.', 'quanlicb' ); ?></p>
			</div>
		</div>
		<div class="quanlicb-box-body">
			<form method="get">
				<input type="hidden" name="page" value="quanlicb-logs" />
				<div class="quanlicb-filter-row quanlicb-filter-row--logs">
					<div>
						<label><?php esc_html_e( 'Hành động', 'quanlicb' ); ?></label>
						<div class="quanlicb-select-wrap">
							<select name="s_action">
								<option value=""><?php esc_html_e( 'Tất cả', 'quanlicb' ); ?></option>
								<?php foreach ( array( 'create', 'update', 'delete', 'import' ) as $action ) : ?>
									<option value="<?php echo esc_attr( $action ); ?>" <?php selected( $args['action'], $action ); ?>><?php echo esc_html( strtoupper( $action ) ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div>
						<label><?php esc_html_e( 'Đối tượng', 'quanlicb' ); ?></label>
						<div class="quanlicb-select-wrap">
							<select name="s_object_type">
								<option value=""><?php esc_html_e( 'Tất cả', 'quanlicb' ); ?></option>
								<?php foreach ( array( 'canbo', 'department', 'position' ) as $object_type ) : ?>
									<option value="<?php echo esc_attr( $object_type ); ?>" <?php selected( $args['object_type'], $object_type ); ?>><?php echo esc_html( $object_type ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div>
						<label><?php esc_html_e( 'Từ ngày', 'quanlicb' ); ?></label>
						<input type="date" name="date_from" value="<?php echo esc_attr( $args['date_from'] ); ?>" />
					</div>
					<div>
						<label><?php esc_html_e( 'Đến ngày', 'quanlicb' ); ?></label>
						<input type="date" name="date_to" value="<?php echo esc_attr( $args['date_to'] ); ?>" />
					</div>
				</div>
				<div class="quanlicb-filter-actions">
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Lọc', 'quanlicb' ); ?></button>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=quanlicb-logs' ) ); ?>" class="button"><?php esc_html_e( 'Bỏ lọc', 'quanlicb' ); ?></a>
				</div>
			</form>
		</div>
	</div>

	<div class="quanlicb-box">
		<div class="quanlicb-section-head">
			<div>
				<p class="quanlicb-section-head__eyebrow"><?php esc_html_e( 'Lịch sử thao tác', 'quanlicb' ); ?></p>
				<p class="quanlicb-meta"><?php echo esc_html( sprintf( __( 'Tổng %d bản ghi', 'quanlicb' ), (int) $result['total'] ) ); ?></p>
			</div>
		</div>
		<div class="quanlicb-table-wrap">
			<table class="wp-list-table widefat striped quanlicb-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Thời gian', 'quanlicb' ); ?></th>
						<th><?php esc_html_e( 'User', 'quanlicb' ); ?></th>
						<th><?php esc_html_e( 'Hành động', 'quanlicb' ); ?></th>
						<th><?php esc_html_e( 'Đối tượng', 'quanlicb' ); ?></th>
						<th><?php esc_html_e( 'Đối tượng ID', 'quanlicb' ); ?></th>
						<th><?php esc_html_e( 'Nội dung', 'quanlicb' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $result['items'] ) ) : ?>
						<tr><td colspan="6" class="quanlicb-empty"><?php esc_html_e( 'Chưa có nhật ký.', 'quanlicb' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $result['items'] as $row ) : ?>
							<tr>
								<td><?php echo esc_html( wp_date( 'd/m/Y H:i:s', strtotime( $row['created_at'] ) ) ); ?></td>
								<td><?php echo esc_html( $row['username'] ? $row['username'] : '-' ); ?></td>
								<td><span class="quanlicb-inline-badge"><?php echo esc_html( strtoupper( $row['action'] ) ); ?></span></td>
								<td><?php echo esc_html( $row['object_type'] ); ?></td>
								<td><?php echo esc_html( $row['object_id'] ); ?></td>
								<td><?php echo esc_html( $row['message'] ); ?></td>
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
						'page'          => 'quanlicb-logs',
						's_action'      => $args['action'],
						's_object_type' => $args['object_type'],
						'date_from'     => $args['date_from'],
						'date_to'       => $args['date_to'],
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
</div>
</div>

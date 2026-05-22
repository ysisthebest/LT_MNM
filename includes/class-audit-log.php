<?php
/**
 * Nhật ký thao tác.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuanLiCB_Audit_Log {

	const PER_PAGE = 20;

	/**
	 * Ghi log hanh dong.
	 */
	public static function log( $action, $object_type, $object_id, $message = '', $payload = array() ) {
		global $wpdb;
		$table = QuanLiCB_Database::log_table_name();
		$user  = wp_get_current_user();

		$row = array(
			'user_id'     => get_current_user_id(),
			'username'    => $user instanceof WP_User ? $user->user_login : '',
			'action'      => sanitize_key( $action ),
			'object_type' => sanitize_key( $object_type ),
			'object_id'   => sanitize_text_field( (string) $object_id ),
			'message'     => sanitize_text_field( $message ),
			'payload'     => ! empty( $payload ) ? wp_json_encode( $payload ) : null,
			'created_at'  => current_time( 'mysql' ),
		);

		$wpdb->insert(
			$table,
			$row,
			array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);
	}

	/**
	 * Danh sách log co loc va phan trang.
	 *
	 * @return array{items: array, total: int, pages: int, paged: int}
	 */
	public static function query( $args = array() ) {
		global $wpdb;
		$table = QuanLiCB_Database::log_table_name();

		$where  = array( '1=1' );
		$params = array();

		if ( ! empty( $args['action'] ) ) {
			$where[]  = 'action = %s';
			$params[] = sanitize_key( $args['action'] );
		}
		if ( ! empty( $args['object_type'] ) ) {
			$where[]  = 'object_type = %s';
			$params[] = sanitize_key( $args['object_type'] );
		}
		if ( ! empty( $args['date_from'] ) ) {
			$where[]  = 'DATE(created_at) >= %s';
			$params[] = sanitize_text_field( $args['date_from'] );
		}
		if ( ! empty( $args['date_to'] ) ) {
			$where[]  = 'DATE(created_at) <= %s';
			$params[] = sanitize_text_field( $args['date_to'] );
		}

		$where_sql = implode( ' AND ', $where );
		$count_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where_sql}";
		if ( $params ) {
			$count_sql = $wpdb->prepare( $count_sql, $params );
		}
		$total = (int) $wpdb->get_var( $count_sql );

		$per_page = isset( $args['per_page'] ) ? max( 1, (int) $args['per_page'] ) : self::PER_PAGE;
		$paged    = isset( $args['paged'] ) ? max( 1, (int) $args['paged'] ) : 1;
		$offset   = ( $paged - 1 ) * $per_page;
		$pages    = max( 1, (int) ceil( $total / $per_page ) );

		$list_sql   = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY ID DESC LIMIT %d OFFSET %d";
		$all_params = array_merge( $params, array( $per_page, $offset ) );
		$list_sql   = $wpdb->prepare( $list_sql, $all_params );
		$items      = $wpdb->get_results( $list_sql, ARRAY_A );

		return array(
			'items' => $items ? $items : array(),
			'total' => $total,
			'pages' => $pages,
			'paged' => $paged,
		);
	}

	/**
	 * Log gan day cho dashboard.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function recent( $limit = 8 ) {
		global $wpdb;
		$table = QuanLiCB_Database::log_table_name();
		$limit = max( 1, (int) $limit );
		$sql   = $wpdb->prepare( "SELECT * FROM {$table} ORDER BY ID DESC LIMIT %d", $limit );
		return $wpdb->get_results( $sql, ARRAY_A );
	}
}

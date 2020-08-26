<?php

class Lovat_Deactivator {

	public static function deactivate() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'lovat_api_keys';
		$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
	}
}

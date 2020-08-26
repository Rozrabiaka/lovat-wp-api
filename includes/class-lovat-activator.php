<?php

class Lovat_Activator
{
	public static function activate()
	{
		global $wpdb;

		$table = $wpdb->prefix . "lovat_api_keys";
		$charset_collate = $wpdb->get_charset_collate();

		if ($wpdb->get_var("show tables like '" . $table . "'") != $table) {
			$sql = "CREATE TABLE $table (
					  id int(9) NOT NULL AUTO_INCREMENT,
					  token VARCHAR(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
					  user_id INT(4) NOT NULL,
					  created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  PRIMARY KEY  (id)
					) $charset_collate;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}
}

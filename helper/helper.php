<?php

class Lovat_Helper
{
	public function get_lovat_option_value()
	{
		global $wpdb;

		$issetRow = $wpdb->get_row("SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'lovat_departure_country'");
		if (empty($issetRow)) return null;

		return $issetRow->option_value;
	}
}
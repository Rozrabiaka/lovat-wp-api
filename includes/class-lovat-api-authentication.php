<?php
/**
 * Lovat API Authentication Class
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Lovat_Api_Authentication
{

	public function __construct()
	{
		add_filter('lovat_api_check_authentication', array($this, 'authenticate'), 0);
	}

	public function authenticate()
	{
		$result = $this->perform_oauth_authentication();
		return $result;
	}

	public function perform_oauth_authentication()
	{
		$header = $this->get_authorization_header();

		if (!empty($header)) {
			// Trim leading spaces.
			$header = trim($header);
			$bearerToken = $this->parse_header($header);

			if (is_null($bearerToken)) return false;

			$key = $this->get_data_by_bearer_token($bearerToken);
			if (is_null($key)) return false;

			return true;
		}

		return false;
	}

	public function get_authorization_header()
	{
		if (function_exists('getallheaders')) {
			$headers = getallheaders();
			// Check for the authoization header case-insensitively.
			foreach ($headers as $key => $value) {
				if ('authorization' === strtolower($key)) {
					return $value;
				}
			}
		}

		return null;
	}

	public function parse_header($header)
	{
		if ('Bearer' !== substr($header, 0, 6)) {
			return null;
		}

		if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
			return $matches[1];
		}

		return null;
	}

	public function get_data_by_bearer_token($token)
	{
		global $wpdb;

		$key = $wpdb->get_row($wpdb->prepare("
			SELECT *
			FROM {$wpdb->prefix}lovat_api_keys
			WHERE token = '%s'
		", $token), ARRAY_A);

		return $key;
	}
}
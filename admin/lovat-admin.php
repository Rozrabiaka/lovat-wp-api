<?php

class Lovat_Admin
{
	private static $errors;

	private static $success;

	private static $warning;

	public static function init()
	{
		// Add menu
		add_action('admin_menu', array(__class__, 'add_menu'));
		// Enqueue the scripts and styles
		if (self::get_current_admin_url()) {
			add_action('admin_enqueue_scripts', array(__class__, 'enqueue_scripts'));
			add_action('admin_init', array(__class__, 'save_settings'));
		}
	}

	public static function add_menu()
	{
		add_options_page('Lovat', 'Api Settings', 'manage_options', 'icon_title', array(__class__, 'lovat_settings_page'));
		add_menu_page('Lovat', 'Api Settings', 'administrator', __FILE__, array(__class__, 'lovat_settings_page'), LOVAT_API_URL . 'admin/images/logo_lovat.png');
	}

	public static function enqueue_scripts()
	{
		wp_enqueue_script('admin-jquery-js', LOVAT_API_URL . 'admin/js/jquery-2.0.3.min.js', array('jquery'), LOVAT_API_PLUGIN_VERSION, false);
		wp_enqueue_style('admin-datatables', LOVAT_API_URL . 'admin/css/datatables.css', array(), LOVAT_API_PLUGIN_VERSION);
		wp_enqueue_style('admin-style', LOVAT_API_URL . 'admin/css/style.css', array(), LOVAT_API_PLUGIN_VERSION);
		wp_enqueue_script('admin-datatables-js', LOVAT_API_URL . 'admin/js/datatables.js', array('jquery'), LOVAT_API_PLUGIN_VERSION, true);
		wp_enqueue_script('admin-datatables-call-js', LOVAT_API_URL . 'admin/js/datatables-call.js', array('jquery'), LOVAT_API_PLUGIN_VERSION, true);
	}

	public static function lovat_settings_page()
	{
		$user = wp_get_current_user();
		$helper = new Lovat_Helper();

		$arrayKeys = self::generated_keys();
		$arrayCountries = require LOVAT_API_PLUGIN_DIR . '/includes/countries.php';
		$issetCountry = $helper->get_lovat_option_value();
		if (!is_null(self::isset_token_by_user($user->ID))) self::add_warning('Вы уже сгенерировали токен. При нажатие на кнопку "Сгенерировать ключь" вы ОБНОВИТЕ его.');
		include(LOVAT_API_PLUGIN_DIR . '/admin/views/api_settings.php');
	}

	public static function save_settings()
	{
		global $wpdb;

		if (!empty($_POST['generate-key'])) {
			if (wc_current_user_has_role('administrator')) {
				$bearerToken = self::create_key();
				$user = wp_get_current_user();
				$issetUserToken = self::isset_token_by_user($user->ID);

				if (!is_null($issetUserToken)) {
					$wpdb->update(
						$wpdb->prefix . 'lovat_api_keys',
						array('token' => $bearerToken),
						array('user_id' => $user->ID)
					);

					self::add_success('Ключь был успешно обновлен. Новый ключь : ' . $bearerToken);
				} else {
					$wpdb->insert(
						$wpdb->prefix . 'lovat_api_keys',
						array('user_id' => $user->ID, 'token' => $bearerToken),
						array('%s', '%s',)
					);

					self::add_success('Ключь был успешно сгенерирован. Ключь : ' . $bearerToken);
				}
			} else self::add_error('Только пользователь с ролью администратор может генерировать ключь.');
		}

		if (!empty($_POST['save-departure-country'])) {
			$country = $_POST['departure-select-country'];

			$helper = new Lovat_Helper();
			$issetRow = $helper->get_lovat_option_value();

			if (!empty($issetRow)) {
				$wpdb->update(
					$wpdb->prefix . 'options',
					array('option_value' => $country),
					array('option_name' => 'lovat_departure_country')
				);
			} else {
				$wpdb->insert(
					$wpdb->prefix . 'options',
					array('option_name' => 'lovat_departure_country', 'option_value' => $country),
					array('%s', '%s',)
				);
			}

			self::add_success('Страна отправки успешно сохранена');
		}
	}

	public static function add_error($text)
	{
		self::$errors = $text;
	}

	public static function add_success($text)
	{
		self::$success = $text;
	}

	public static function add_warning($text)
	{
		self::$warning = $text;
	}

	public static function show_error_message()
	{
		if (!is_null(self::$errors))
			return '<div class="lovat-alert danger-alert" role="alert">' . self::$errors . '
				<a class="close-lovat-alert">&times;</a>
			</div>';
	}

	public static function show_success_message()
	{
		if (!is_null(self::$success))
			return '<div class="lovat-alert success-alert" role="alert">' . self::$success . '
				<a class="close-lovat-alert">&times;</a>
			</div>';
	}

	public static function show_warning_message()
	{
		if (!is_null(self::$warning))
			return '<div class="lovat-alert warning-alert" role="alert">' . self::$warning . '
				<a class="close-lovat-alert">&times;</a>
  			</div>';
	}

	public static function create_key()
	{
		return 'lt_' . wc_rand_hash();
	}

	public static function isset_token_by_user(int $userId)
	{
		global $wpdb;
		$issetUserToken = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}lovat_api_keys WHERE user_id = {$userId}");
		return $issetUserToken;
	}

	public static function generated_keys()
	{
		global $wpdb;
		$result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}lovat_api_keys");
		return $result;
	}

	public static function get_current_admin_url()
	{
		$uri = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';
		$uri = preg_replace('|^.*/wp-admin/|i', '', $uri);

		if (!$uri) return false;
		if (strpos($uri, 'lovat-admin.php')) return true;

		return false;
	}
}

Lovat_Admin::init();
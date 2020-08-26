<?php

class Lovat
{
	protected static $_instance = null;

	public $authentication;

	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct()
	{
		$this->includes();
	}

	public function includes()
	{
		//includes
		include_once(LOVAT_API_PLUGIN_DIR . '/includes/class-server.php');
		include_once( LOVAT_API_PLUGIN_DIR . '/includes/class-lovat-api-authentication.php' );
		$this->authentication = new Lovat_Api_Authentication();

		//admin
		include_once(LOVAT_API_PLUGIN_DIR . '/admin/lovat-admin.php');
	}
}
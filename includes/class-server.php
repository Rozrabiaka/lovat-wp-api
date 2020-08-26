<?php

class Server
{
	protected $controllers = [];

	public static function get_instance()
	{
		static $instance = null;

		if (is_null($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 */
	public function __construct()
	{
		add_action('rest_api_init', array($this, 'register_rest_routes'), 10);
	}

	// Includes
	public function register_rest_routes()
	{
		foreach ($this->get_rest_namespaces() as $namespace => $controllers) {
			foreach ($controllers as $controller_name => $controller_class) {
				$this->controllers[$namespace][$controller_name] = new $controller_class();
				$this->controllers[$namespace][$controller_name]->register_routes();
			}
		}
	}

	public function get_rest_namespaces()
	{
		return apply_filters(
			'lovat_rest_api_get_rest_namespaces',
			[
				'v1' => $this->get_v1_controllers(),
			]
		);
	}

	public function get_v1_controllers()
	{
		return [
			'woocommerce-orders' => 'Orders_Controller'
		];
	}
}

server::get_instance();
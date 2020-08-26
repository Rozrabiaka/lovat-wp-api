<?php
spl_autoload_register(function () {
	/* Include Controllers */
	include_once ('includes/class-orders-controller.php');
	include_once ('helper/helper.php');
});
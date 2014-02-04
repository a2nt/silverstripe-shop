<?php 
class OrderStatus extends AccountEditor {
	private static $allowed_actions = array(
		'ActionsForm',
		'order'
	);
	private static $extensions = array(
		'OrderManipulation'
	);

	public static function require_styled_type($type = null){
		CheckoutPage_Controller::require_styled_type();
	}

	public static function require_js_type($type = null){
		CheckoutPage_Controller::require_js_type();
	}
}
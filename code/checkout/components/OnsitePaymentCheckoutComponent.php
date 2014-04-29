<?php

use Omnipay\Common\Helper;

/**
 *
 * This component should only ever be used on SSL encrypted pages!
 */
class OnsitePaymentCheckoutComponent extends CheckoutComponent {

	function getFormFields(Order $order) {
		$gateway = Checkout::get($order)->getSelectedPaymentMethod();
		$gatewayfieldsfactory = new GatewayFieldsFactory($gateway,array('Card'));
		$fields = $gatewayfieldsfactory->getCardFields();
		$fields->unshift(
			LiteralField::create(
				'TotalOutstanding',
				'<div class="alert alert-block">'
					.'<strong>'._t('Cart.TOTALOUTSTANDING','Total outstanding').':</strong>'
					.' <strong>'.$order->TotalOutstanding().' '.$order->Currency().'</strong>'
				.'</div>'
			)
		);
		if($gateway === "Dummy"){
			$fields->unshift(LiteralField::create(
				'dummypaymentmessage',
				'<div class="message good alert"><strong>Warning!</strong> Dummy data has been added to the form for testing convenience.</div>'
			));
		}
		if($gateway === 'PayPal_Pro' && Director::isDev()){
			$fields->unshift(LiteralField::create(
				'dummypaymentmessage',
				'<div class="message good alert"><strong>Warning!</strong> Dummy data has been added to the form for testing convenience.</div>'
			));
		}

		return $fields;
	}

	public function getRequiredFields(Order $order){
		return GatewayInfo::required_fields(Checkout::get($order)->getSelectedPaymentMethod());
	}

	public function validateData(Order $order, array $data){
		$result = new ValidationResult();
		//TODO: validate credit card data
		// validates only live card numbers
		if(Director::isLive() && !Helper::validateLuhn($data['number'])){
			$result->error('Credit card is invalid');
			throw new ValidationException($result);
		}
	}

	public function getData(Order $order){
		$data = array();
		$gateway = Checkout::get($order)->getSelectedPaymentMethod();
		// provide valid dummy credit card data
		if($gateway === "Dummy"){
			$data = array_merge(array(
				'name' => 'Joe Bloggs',
				'number' => '4242424242424242',
				'cvv' => 123
			), $data);
		}
		if($gateway === 'PayPal_Pro' && Director::isDev()){
			$data = array_merge(array(
				'name' => 'Tony Air',
				'number' => '4682-1706-9657-7343',
				'EXPDATE' => '07/2018',
				'cvv' => '000'
			), $data);
		}
		return $data;
	}

	public function setData(Order $order, array $data){
		//create payment?
	}

}
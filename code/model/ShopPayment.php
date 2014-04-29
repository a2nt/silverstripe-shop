<?php
/**
 * Customisations to {@link Payment} specifically for the shop module.
 *
 * @package shop
 */
class ShopPayment extends DataExtension {
	
	private static $has_one = array(
		'Order' => 'Order'
	);

	function onCaptured($response){
		$order = $this->owner->Order();
		if($order->exists()){
			OrderProcessor::create($order)->completePayment();
		}
	}

	public function updateSummaryFields(&$fields){
		$fields = array(
				'Money' => _t('Payment.AMOUNT','Amount'),
				'GatewayTitle' => _t('Payment.PAYMENTTYPE','Gateway'),
				'Status' => _t('Payment.db_Status','Status'),
				'Created' => _t('Payment.db_Created','Created')
		);
	}
}

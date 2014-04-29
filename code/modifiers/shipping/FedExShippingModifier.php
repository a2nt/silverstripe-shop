<?php
/**
 * FedEx Shipping Modifier
 *
 * @package shop
 * @subpackage modifiers
 */
class FedExShippingModifier extends ShippingModifier {
	protected $error_msg = '';
	private static $has_one = array(
		'Parcel' => 'Parcel',
	);

	function value($subtotal = null){
		$order = $this->Order();
		
		// get Parcel
		$parcel = $this->Parcel();
		if(!$parcel){
			$parcel = new Parcel();
			$parcel->OrderID = $order->ID;
			$parcel->TrackingService = 'Fedex';
			$parcel->write();

			$this->ParcelID = $parcel->ID;
		}
		
		/*$sequence = $parcel->ParcelSequence();
		if($sequence->count() == 0){
			$sequenceItem = new ParcelSequenceItem();
			$sequenceItem->Length = 10;
			$sequenceItem->Width = 10;
			$sequenceItem->Height = 10;
			$sequenceItem->Weight = 10;
			
			$sequenceItem->ParcelID = $parcel->ID;
			$sequence->add($sequenceItem);
			$sequenceItem->write();
		}*/
		$this->extend('updateParcelSequence',$this);
		//

		return $parcel->getFedexRate()->Amount;
	}

	function ShowInTable(){
		return true;
	}
	
	function TableTitle() {
		if($this->value() > 0){
			return _t('FedExShippingModifier.TITLE','FedEx Delivery');
		}else{
			return _t(
				'FedExShippingModifier.TITLEADDRESS',
				'FedEx Delivery (Please, specify address to calculate delivery price) '
				.Session::get('FedExMessage')
			);
		}
	}
}
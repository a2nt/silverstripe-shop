<?php

class Parcel extends DataObject {
	private static $weight_metric = ' lbs.';
	private static $db = array(
		'TrackingNumber' => 'Varchar(255)',
		'TrackingService' => 'Enum("Fedex,Alternative","Fedex")',
		'AlternativeServiceName' => 'Varchar(255)',
		'Price' => 'Currency',
		'Insurance' => 'Currency',
	);
	private static $has_one = array(
		'Order' => 'Order',
	);
	private static $has_many = array(
		'ParcelSequence' => 'ParcelSequenceItem',
	);
	private static $belongs_to = array(
		'Order' => 'Order',
	);
	
	public function Calculate(){
		$this->Price = $this->getFedexRate();
		$this->extend('updateCalculate',$this);
		return $this->Price;
	}
	public function DeliveryDetails(){
		$ar = $this->getFedexRate();
		$data = new ArrayData(array(
			'Main' => $ar,
			'RateDetails' => new ArrayData($ar['RateReplyDetails']['RatedShipmentDetails'][0]['ShipmentRateDetail'])//[0]['ShipmentRateDetail'];
		));
		return $data->renderWith('Fedex_Rate');
	}

	public function HumanServiceName(){
		if(!$this->getField('AlternativeServiceName')){
			return $this->getField('TrackingService');
		}
		return $this->getField('AlternativeServiceName');
	}

	public function Size(){
		$size = '';
		if($this->getField('Length')){
			$size .= $this->getField('Length').'x'.$this->getField('Width').'x'.$this->getField('Height');
		}
		if($this->getField('Weight')){
			$size .= ' '._t('Parcel.db_Weight','Weight').': '.$this->getField('Weight').' '.self::$weight_metric;
		}
		return $size;
	}

	public function summaryFields(){
		return array(
			'HumanServiceName' => array(
				'title' => _t('Parcel.db_ServiceName','Service Name')
			),
			'TrackingNumber' => array(
				'title' => _t('Parcel.db_TrackingNumber','Tracking Number')
			),
			'Size' => array(
				'title' => _t('Parcel.Size','Size')
			),
		);
	}

	public function getTitle(){
		return '#'.$this->getField('TrackingNumber');
	}
	public function getStatus(){
		if(
			$this->getField('TrackingService') === 'Fedex'
			&& $this->getField('TrackingNumber')
			&& $this->Order()->Status == 'Sent'
		){
			$track = $this->getFedexTracking();
			if(is_array($track)){
				$data = new ArrayData(array(
					'Tracks' => new ArrayList(
						$track
					),
				));
				return $data->renderWith('Fedex_Tracks');
			}
			
			return $track;
		}else{
			$this->Order()->Status;
		}
	}

	protected function getFedexTracking(){
		$track = new FedexTrackService($this->getField('TrackingNumber'));
		return $track->getTrackingDetails();
	}

	public function getFedexRateDetails(){
		$cart = ShoppingCart::curr();
		$shipTo = $cart->ShippingAddress();
		
		$countries = SiteConfig::current_site_config()->getCountriesList();
		if(count($countries) == 1){
			$shipTo->Country = array_keys($countries)[0];
		}
		//var_dump($cart);
		//die();
		if($cart->FirstName && $shipTo->Country && $shipTo->City && $shipTo->Address){
			$sequence = $this->ParcelSequence();

			// Multi-packages
			$count = $sequence->count();
			if($count > 1){
				$package = array();
				$i = 1;
				foreach($sequence as $sequenceItem){
					$package[] = array(
						'SequenceNumber' => $i,
						'GroupPackageCount' => $count,
						'Weight' => array(
							'Value' => $sequenceItem->Weight,
							'Units' => 'LB'
						),
						'Dimensions' => array(
							'Length' => $sequenceItem->Length,
							'Width' => $sequenceItem->Width,
							'Height' => $sequenceItem->Height,
							'Units' => 'IN'
						)
					);
					$i++;
				}
			// One package
			}else{
				$sequenceItem = $sequence->First();
				$package = array(
					'SequenceNumber' => 1,
					'GroupPackageCount' => 1,
					'Weight' => array(
						'Value' => $sequenceItem->Weight,
						'Units' => 'LB'
					),
					'Dimensions' => array(
						'Length' => $sequenceItem->Length,
						'Width' => $sequenceItem->Width,
						'Height' => $sequenceItem->Height,
						'Units' => 'IN'
					)
				);
			}

			$recipient = array(
				'Contact' => array(
					'PersonName' => $cart->FirstName.' '.$cart->LastName,
					'CompanyName' => 'Company Name',
					'PhoneNumber' => $shipTo->Phone
				),
				'Address' => array(
					'StreetLines' => array($shipTo->Address,$shipTo->AddressLine2),
					'City' => $shipTo->City,
					'StateOrProvinceCode' => $shipTo->State,
					'PostalCode' => $shipTo->PostalCode,
					'CountryCode' => $shipTo->Country,
					'Residential' => false
				)
			);

			$track = new FedexRateService($recipient,$package);
			$track->request();
			$this->details = $track->getDetails();
			if($this->details['HighestSeverity'] !== 'ERROR'){
				return $this->details;
			}
		}
		return false;
	}

	public function getFedexRate(){
		$total = 0;
		$currency = 'USD';
		
		$details = $this->getFedexRateDetails();
		if($details){
			$currency = $this->details['RateReplyDetails']['RatedShipmentDetails'][0]['ShipmentRateDetail']['TotalNetFedExCharge']['Currency'];
			// sum all parcels
			foreach($this->details['RateReplyDetails']['RatedShipmentDetails'] as $details){
				$total = $total + $details['ShipmentRateDetail']['TotalNetFedExCharge']['Amount'];
			}
			Session::clear('FedExMessage');
		}else{
			Session::set(
				'FedExMessage',
				$this->details['Notifications']['Message']
			);
		}

		$field = new Money('Price');
		$field->setAmount($total);
		$field->setCurrency($currency);
		return $field;
	}
}

class ParcelSequenceItem extends DataObject {
	private static $db = array(
		'Length' => 'Float',
		'Width' => 'Float',
		'Height' => 'Float',
		'Weight' => 'Float',
	);

	private static $has_one = array(
		'Parcel' => 'Parcel',
	);

	private static $belongs_to = array(
		'Parcel' => 'Parcel',
	);
}
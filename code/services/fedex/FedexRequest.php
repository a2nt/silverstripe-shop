<?php

/*
 * Fedex request
 * @author: Tony Air TWDA New Castle LLC http://twma.pro
 * @package: SilverStripe
 * test tracking numbers:
 * 123456789012
 * 111111111111
 * 797843158299
 */

abstract class FedexRequest {
	protected static $_wsdl, $_wsdl_test, $parameters, $_method;
	protected $_messages = '', $_details = '', $request = array();

	public function getDefaultParameters(){
		return array(
			'key'						=>	'XfeUfe9UobMzOnKR',
			'password'					=>	'Lae9I7A7zclHLmUHEPkGwsKKe',
			'account_number'			=>	'510087046',
			'meter_number'				=>	'118586581',
			'ship_account'				=>	'510087020',
			'bill_account'				=>	'510051408',
			'tracking_number'			=>	'123456789012',
			'office_integrator_id'		=>	'123',
			'client_product_id'			=>	'TEST',
			'client_product_version'	=>	'9999',
			'shipper' => array(
				'Contact' => array(
					'PersonName' => 'Sender Name',
					'CompanyName' => 'Sender Company Name',
					'PhoneNumber' => '9012638716'
				),
				'Address' => array(
					'StreetLines' => array(
						'1202 Chalet Ln',
						'Do Not Delete - Test Account'
					),
					'City' => 'Harrison',
					'StateOrProvinceCode' => 'AR',
					'PostalCode' => '72601',
					'CountryCode' => 'US'
				)
			)
		);
	}

	public function setParam($key,$value){
		$class = get_class($this);
		$class::$parameters[$key] = $value;
		return $this;
	}

	public function getParam($key){
		$class = get_class($this);
		return $class::$parameters[$key];
	}

	public function __construct(array $parameters = array()){
		// set default parameters
		foreach($this->getDefaultParameters() as $key => $value){
			$this->setParam($key,$value);
		}
		// set config parametrs
		$conf = Config::inst()->forClass('Fedex');
		foreach($conf as $key => $value){
			$this->setParam($key,$value);
		}
		// set passed parametrs
		foreach($parameters as $key => $value){
			$this->setParam($key,$value);
		}
		//

		$this->request['WebAuthenticationDetail'] = array(
			'UserCredential' =>array(
				'Key' => $this->getParam('key'), 
				'Password' => $this->getParam('password')
			)
		);

		$this->request['ClientDetail'] = array(
			'AccountNumber' => $this->getParam('account_number'),//$Fedex->getParam('ship_account'), //account_number
			'MeterNumber' => $this->getParam('meter_number')
		);
	}

	public function request(){
		$class = get_class($this);
		ini_set('soap.wsdl_cache_enabled','0');
		$wsdl = $class::$_wsdl;
		if(Director::isDev()){
			$wsdl = $class::$_wsdl_test;
		}
		$client = new SoapClient(
			dirname(__FILE__).'/'.$wsdl,
			array('trace' => 1)
		);
		try {
			$request_method = $class::$_method;
			$this->response = $client->$request_method($this->request);
		} catch(SoapFault $exception) {
			var_dump($exception);
			die('aaaa');
		}
	}

	public function getMessages(){
		return $this->_messages;
	}

	public function getDetails(){
		return $this->formatTracking($this->response);
	}

	public static function formatTracking($d){
		if(is_object($d) && get_class($d) === 'stdClass'){
			// format date
			if(isset($d->ShipTimestamp)){
				$d->ShipTimestamp = self::make_date($d->ShipTimestamp);
			}
			if(isset($d->ActualDeliveryTimestamp)){
				$d->ActualDeliveryTimestamp = self::make_date($d->ActualDeliveryTimestamp);
			}
			if(isset($d->Timestamp)){
				$d->Timestamp = self::make_date($d->Timestamp);
			}
			//

			// format location
			if(isset($d->Location)){
				$str = '';
				$str .= isset($d->Location->StreetLines) ? trim($d->Location->StreetLines,', ').', ' : '';
				$str .= isset($d->Location->City) ? trim($d->Location->City,', ').', ' : '';
				$str .= isset($d->Location->StateOrProvinceCode) ? trim($d->Location->StateOrProvinceCode,', ').', ' : '';
				$str .= isset($d->Location->CountryCode) ? trim($d->Location->CountryCode,', ').', ' : '';
				if(strlen($str)>0){
					$d->Location = ucwords($str);
				}
			}elseif(isset($d->Address)){
				$str = '';
				$str .= isset($d->Address->StreetLines) ? trim($d->Address->StreetLines,', ').', ' : '';
				$str .= isset($d->Address->City) ? trim($d->Address->City,', ').', ' : '';
				$str .= isset($d->Address->StateOrProvinceCode) ? trim($d->Address->StateOrProvinceCode,', ').', ' : '';
				$str .= isset($d->Address->CountryCode) ? trim($d->Address->CountryCode,', ').', ' : '';
				if(strlen($str)>0){
					$d->Location = ucwords($str);
				}
			}elseif(isset($d->ActualDeliveryAddress)){
				$str = '';
				$str .= isset($d->ActualDeliveryAddress->StreetLines) ? trim($d->ActualDeliveryAddress->StreetLines,', ').', ' : '';
				$str .= isset($d->ActualDeliveryAddress->City) ? trim($d->ActualDeliveryAddress->City,', ').', ' : '';
				$str .= isset($d->ActualDeliveryAddress->StateOrProvinceCode) ? trim($d->ActualDeliveryAddress->StateOrProvinceCode,', ').', ' : '';
				$str .= isset($d->ActualDeliveryAddress->CountryCode) ? trim($d->ActualDeliveryAddress->CountryCode,', ').', ' : '';
				if(strlen($str)>0){
					$d->Location = ucwords($str);
				}
			}elseif(isset($d->DestinationAddress)){
				$str = '';
				$str .= isset($d->DestinationAddress->StreetLines) ? trim($d->DestinationAddress->StreetLines,', ').', ' : '';
				$str .= isset($d->DestinationAddress->City) ? trim($d->DestinationAddress->City,', ').', ' : '';
				$str .= isset($d->DestinationAddress->StateOrProvinceCode) ? trim($d->DestinationAddress->StateOrProvinceCode,', ').', ' : '';
				$str .= isset($d->DestinationAddress->CountryCode) ? trim($d->DestinationAddress->CountryCode,', ').', ' : '';
				if(strlen($str)>0){
					$d->Location = ucwords($str);
				}
			}
			//

			$d = get_object_vars($d);
		}
		if(is_array($d)){
			return array_map(array('FedexRequest','formatTracking'), $d);
		}else{
			// Return array
			if(is_string($d)){
				return ucwords(trim($d,', '));
			}else{
				return $d;
			}
		}
	}
	
	protected static function make_date($timestamp){
		$date = Date::create();
		$date->setValue($timestamp);
		return $date;
	}
}
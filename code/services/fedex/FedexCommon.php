<?php

/* 
 * FedEx Service 
 * Author: Tony Air <tony@twma.pro>
 *
 * Required for All Web Services
 * Developer Test Key: XfeUfe9UobMzOnKR
 * Required for FedEx Web Services for Intra Country Shipping in US and Global
 * Test Account Number:	510087046
 * Test	 Meter Number: 118586581
 * Required for FedEx Web Services for Office and Print
 * Test FedEx Office Integrator ID: 123
 * Test Client Product ID: TEST
 * Test Client Product Version: 9999
*/

class FedexCommon {

	// Transactions log file
	private static $_logfile = '../fedextransactions.log';
	/*private static $key = 'XfeUfe9UobMzOnKR';
	private static $password = 'Lae9I7A7zclHLmUHEPkGwsKKe';

	private static $accountNumber = '510087046';
	private static $meterNumber = '118586581';

	private static $shipaccount = '510087020';
	private static $billaccount = '510051408';

	private static $test_TrackingNumber = '123456789012';*/

	private static $parameters = array(
	);
	private static $_messages = '';

	public function getDefaultParameters(){
		return array(
			'key'						=>	'XfeUfe9UobMzOnKR',
			'password'					=>	'mzXWBoTi5SQrTij3i07EhHiYY',//'Lae9I7A7zclHLmUHEPkGwsKKe',
			'account_number'			=>	'361488717',//'510087046',
			'meter_number'				=>	'106393144',//'118586581',
			'ship_account'				=>	'106393144',//'510087020',
			'bill_account'				=>	'106393144',//'510051408',
			'tracking_number'			=>	'106393144',//'123456789012',
			'office_integrator_id'		=>	'106393144',//'123',
			'client_product_id'			=>	'106393144',//'TEST',
			'client_product_version'	=>	'106393144',//'9999',
			'shipper' => array(
				'Contact' => array(
					'PersonName' => 'Shipping Department',//'Sender Name',
					'CompanyName' => 'Furnace Filter Pro',//'Sender Company Name',
					'PhoneNumber' => '7176835415'//'9012638716'
				),
				'Address' => array(
					'StreetLines' => array(
						'3430 Woodbridge Ct.',//'1202 Chalet Ln',
						''//'Do Not Delete - Test Account'
					),
					'City' => 'York',//'Harrison',
					'StateOrProvinceCode' => 'PA',//'AR',
					'PostalCode' => '17406',//'72601',
					'CountryCode' => 'US'
				)
			)
		);
	}

	public function initialize(array $parameters = array()){
		// set default parameters
		foreach($this->getDefaultParameters() as $key => $value){
			self::$parameters[$key] = $value;
		}
		foreach($parameters as $key => $value){
			self::$parameters[$key] = $value;
		}
		return $this;
	}

	public function setParam($key,$value){
		self::$parameters[$key] = $value;
		return $this;
	}

	public function getParam($key){
		return self::$parameters[$key];
	}

	/**
	 * This section provides a convenient place to setup many commonly used variables
	 * needed for the php sample code to public function.
	 */
	/*public function getProperty($var){
		if($var == 'key') return self::$key;
		if($var == 'password') return self::$password;
			
		if($var == 'shipaccount') return self::$shipaccount;
		if($var == 'billaccount') return self::$billaccount;
		if($var == 'dutyaccount') return self::$accountNumber;
		if($var == 'freightaccount') return self::$accountNumber;
		if($var == 'trackaccount') return self::$accountNumber;

		if($var == 'meter') return self::$meterNumber; 
			
		if($var == 'shiptimestamp') return mktime(10, 0, 0, date("m"), date("d")+1, date("Y"));

		if($var == 'spodshipdate') return '2013-05-21';
		if($var == 'serviceshipdate') return '2013-04-26';

		if($var == 'readydate') return '2010-05-31T08:44:07';
		if($var == 'closedate') return date("Y-m-d");

		if($var == 'pickupdate') return date("Y-m-d", mktime(8, 0, 0, date("m")  , date("d")+1, date("Y")));
		if($var == 'pickuptimestamp') return mktime(8, 0, 0, date("m")  , date("d")+1, date("Y"));
		if($var == 'pickuplocationid') return 'XXX';
		if($var == 'pickupconfirmationnumber') return 'XXX';

		if($var == 'dispatchdate') return date("Y-m-d", mktime(8, 0, 0, date("m")  , date("d")+1, date("Y")));
		if($var == 'dispatchlocationid') return 'XXX';
		if($var == 'dispatchconfirmationnumber') return 'XXX';		
		
		if($var == 'tag_readytimestamp') return mktime(10, 0, 0, date("m"), date("d")+1, date("Y"));
		if($var == 'tag_latesttimestamp') return mktime(20, 0, 0, date("m"), date("d")+1, date("Y"));	

		if($var == 'expirationdate') return '2013-05-24';
		if($var == 'begindate') return '2013-04-22';
		if($var == 'enddate') return '2013-04-25';	

		if($var == 'trackingnumber') return self::$test_TrackingNumber;

		if($var == 'hubid') return 'XXX';
		
		if($var == 'jobid') return 'XXX';

		if($var == 'searchlocationphonenumber') return '5555555555';
				
		if($var == 'shipper') return array(
			'Contact' => array(
				'PersonName' => 'Sender Name',
				'CompanyName' => 'Sender Company Name',
				'PhoneNumber' => '1234567890'
			),
			'Address' => array(
				'StreetLines' => array('Address Line 1'),
				'City' => 'Collierville',
				'StateOrProvinceCode' => 'TN',
				'PostalCode' => '38017',
				'CountryCode' => 'US',
				'Residential' => 1
			)
		);
		if($var == 'recipient') return array(
			'Contact' => array(
				'PersonName' => 'Recipient Name',
				'CompanyName' => 'Recipient Company Name',
				'PhoneNumber' => '1234567890'
			),
			'Address' => array(
				'StreetLines' => array('Address Line 1'),
				'City' => 'Herndon',
				'StateOrProvinceCode' => 'VA',
				'PostalCode' => '20171',
				'CountryCode' => 'US',
				'Residential' => 1
			)
		);	

		if($var == 'address1') return array(
			'StreetLines' => array('10 Fed Ex Pkwy'),
			'City' => 'Memphis',
			'StateOrProvinceCode' => 'TN',
			'PostalCode' => '38115',
			'CountryCode' => 'US'
		);
		if($var == 'address2') return array(
			'StreetLines' => array('13450 Farmcrest Ct'),
			'City' => 'Herndon',
			'StateOrProvinceCode' => 'VA',
			'PostalCode' => '20171',
			'CountryCode' => 'US'
		);					  
		if($var == 'searchlocationsaddress') return array(
			'StreetLines'=> array('240 Central Park S'),
			'City'=>'Austin',
			'StateOrProvinceCode'=>'TX',
			'PostalCode'=>'78701',
			'CountryCode'=>'US'
		);
										  
		if($var == 'shippingchargespayment') return array(
			'PaymentType' => 'SENDER',
			'Payor' => array(
				'ResponsibleParty' => array(
					'AccountNumber' => $this->getProperty('billaccount'),
					'Contact' => null,
					'Address' => array('CountryCode' => 'US')
				)
			)
		);	
		if($var == 'freightbilling') return array(
			'Contact'=>array(
				'ContactId' => 'freight1',
				'PersonName' => 'Big Shipper',
				'Title' => 'Manager',
				'CompanyName' => 'Freight Shipper Co',
				'PhoneNumber' => '1234567890'
			),
			'Address'=>array(
				'StreetLines'=>array(
					'1202 Chalet Ln', 
					'Do Not Delete - Test Account'
				),
				'City' =>'Harrison',
				'StateOrProvinceCode' => 'AR',
				'PostalCode' => '72601-6353',
				'CountryCode' => 'US'
				)
		);
	}*/

	public function setEndpoint($var){
		if($var == 'changeEndpoint') return false;
	}

	public function trackDetails($details){//, $spacer){
		return $this->formatTracking($details);
		/*foreach($details as $key => $value){
			if(is_array($value) || is_object($value)){
				$newSpacer = $spacer. '&nbsp;&nbsp;&nbsp;&nbsp;';
				self::$_messages .= '<tr><td>'. $spacer . $key.'</td><td>&nbsp;</td></tr>';
				$this->trackDetails($value, $newSpacer);
			}elseif(empty($value)){
				self::$_messages .= '<tr><td>'.$spacer. $key .'</td><td>'.$value.'</td></tr>';
			}else{
				self::$_messages .= '<tr><td>'.$spacer. $key .'</td><td>'.$value.'</td></tr>';
			}
		}
		return self::$_messages;*/
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
			return array_map(array('FedExCommon','formatTracking'), $d);
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

	public function getMessages(){
		return self::$_messages;
	}

	/**
	 *  Print SOAP request and response
	 */

	public function printSuccess($client, $response) {
		self::$_messages .= '<h2>Transaction Successful</h2>';  
		self::$_messages .= "\n";
		//$this->printRequestResponse($client);
		//return self::$_messages;
	}

	public function printRequestResponse($client){
		self::$_messages .= '<h2>Request</h2>' . "\n";
		self::$_messages .= '<pre>'.htmlspecialchars($client->__getLastRequest()).'</pre>';  
		self::$_messages .= "\n";
		
		self::$_messages .= '<h2>Response</h2>'. "\n";
		self::$_messages .= '<pre>'.htmlspecialchars($client->__getLastResponse()).'</pre>';
		self::$_messages .= "\n";
		return self::$_messages;
	}

	/**
	 *  Print SOAP Fault
	 */  
	public function printFault($exception, $client) {
		self::$_messages .= '<h2>Fault</h2>' . "<br>\n";	
		self::$_messages .= "<b>Code:</b>{$exception->faultcode}<br>\n";
		self::$_messages .= "<b>String:</b>{$exception->faultstring}<br>\n";
		$this->writeToLog($client);
		
		self::$_messages .= '<h2>Request</h2>' . "\n";
		self::$_messages .= '<pre>'.htmlspecialchars($client->__getLastRequest()).'</pre>';  
		self::$_messages .= "\n";
		return self::$_messages;
	}

	/**
	 * SOAP request/response logging to a file
	 */								  
	public function writeToLog($client){  
		if (!$logfile = fopen(self::$_logfile, "a")){
			error_func('Cannot open '.self::$_logfile.' file.\n', 0);
			exit(1);
		}
		fwrite($logfile, sprintf("\r%s:- %s",date("D M j G:i:s T Y"), $client->__getLastRequest(). "\n\n" . $client->__getLastResponse()));
	}

	public function printNotifications($notes){
		foreach($notes as $noteKey => $note){
			if(is_string($note)){	
				self::$_messages .= $noteKey.': '.$note.'<br/>';
			}else{
				$this->printNotifications($note);
			}
		}
		self::$_messages .= '<br/>';
		return self::$_messages;
	}

	public function printError($client, $response){
		self::$_messages .= '<h2>Error returned in processing transaction</h2>';
		self::$_messages .= "\n";
		$this->printNotifications($response->Notifications);
		$this->printRequestResponse($client, $response);
		return self::$_messages;
	}
}
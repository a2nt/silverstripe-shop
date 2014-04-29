<?php

/*
 * Fedex tracking service
 * @author: Tony Air TWDA New Castle LLC http://twma.pro
 * @package: SilverStripe
 * test tracking numbers:
 * 123456789012
 * 111111111111
 * 797843158299
 */

class FedexRateService extends FedexRequest {
	protected static $_wsdl = 'wsdl/RateService_v14.wsdl';
	protected static $_wsdl_test = 'wsdl/RateService_v14Test.wsdl';
	protected static $_method = 'getRates';
	protected static $responseClass = 'FedexRateResponse';

	public function getParameters(){
		return array(
			'TransactionDetail' => array('CustomerTransactionId' => ' *** Rate Request v14 using PHP ***'),
			'Version' => array(
				'ServiceId' => 'crs', 
				'Major' => '14', 
				'Intermediate' => '0', 
				'Minor' => '0'
			),
			'ReturnTransitAndCommit' => true,
		);
	}
	public function __construct(array $recipient, array $package){
		parent::__construct();
		
		$request = $this->getParameters();

		$request['RequestedShipment'] = array(
			// who sends
			'Shipper' => $this->getParam('shipper'),
			// who pays
			'ShippingChargesPayment' => array(
				'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
				'Payor' => array(
					'ResponsibleParty' => array(
						'AccountNumber' => $this->getParam('account_number'), //$Fedex->getParam('bill_account'),
						'CountryCode' => 'US'
					)
				)
			),
			//

			'DropoffType' => 'REGULAR_PICKUP', // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
			'ShipTimestamp' => date('c'),
			'ServiceType' => 'FEDEX_GROUND', // valid values INTERNATIONAL_PRIORITY, STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
			'PackagingType' => 'YOUR_PACKAGING', // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
			'TotalInsuredValue' => array(
				'Ammount' => 100,
				'Currency' => 'USD'
			),
			'RateRequestTypes' => 'ACCOUNT',
			'RateRequestTypes' => 'LIST',
			'PackageCount' => '1',

			// who will receive
			'Recipient' => $recipient,
			// items
			'RequestedPackageLineItems' => $package
		);
		//
		
		// set passed parametrs
		//foreach($parameters as $key => $value){
		//	$this->setParam($key,$value);
		//}
		//
		//var_dump($request);die();
		$this->request = array_merge($this->request,$request);
	}
}
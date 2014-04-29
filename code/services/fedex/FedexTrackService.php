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

class FedexTrackService {
	//The WSDL is not included with the sample code.
	//Please include and reference in $path_to_wsdl variable.
	private static $_wsdl = 'wsdl/TrackService_v8.wsdl';
	private static $_wsdl_test = 'wsdl/TrackService_v8Test.wsdl';
	private static $_messages = '';
	private static $_tracking_details;

	public function __construct($tracking_number){
		$Fedex = new FedexCommon();
		$Fedex->initialize(array(
			'tracking_number' => $tracking_number
		));

		ini_set('soap.wsdl_cache_enabled','0');

		$wsdl = self::$_wsdl;
		if(Director::isDev()){
			$wsdl = self::$_wsdl_test;
		}

		$client = new SoapClient(
			dirname(__FILE__).'/'.$wsdl,
			array('trace' => 1)
		);

		$request['WebAuthenticationDetail'] = array(
			'UserCredential' =>array(
				'Key' => $Fedex->getParam('key'), 
				'Password' => $Fedex->getParam('password')
			)
		);
		$request['ClientDetail'] = array(
			'AccountNumber' => $Fedex->getParam('ship_account'), 
			'MeterNumber' => $Fedex->getParam('meter_number')
		);
		$request['TransactionDetail'] = array('CustomerTransactionId' => '*** Track Request v8 using PHP ***');
		$request['Version'] = array(
			'ServiceId' => 'trck', 
			'Major' => '8', 
			'Intermediate' => '0', 
			'Minor' => '0'
		);
		$request['SelectionDetails'] = array(
			'CarrierCode' => 'FDXE',
			'PackageIdentifier' => array(
				'Type' => 'TRACKING_NUMBER_OR_DOORTAG',
				'Value' => $Fedex->getParam('tracking_number') // Replace 'XXX' with a valid tracking identifier
			)
		);



		try {
			if($Fedex->setEndpoint('changeEndpoint')){
				$newLocation = $client->__setLocation($Fedex->setEndpoint('endpoint'));
			}
			
			$response = $client->track($request);
			
			if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR'){
				if($response->HighestSeverity != 'SUCCESS'){
					self::$_tracking_details = $Fedex->trackDetails($response->Notifications);
					die('mmm1');
					/*self::$_messages .= '<table border="1">';
					self::$_messages .= '<tr><th>Track Reply</th><th>&nbsp;</th></tr>';
					self::$_messages .= $Fedex->trackDetails($response->Notifications, '');
					self::$_messages .= '</table>';*/
				}else{
					if ($response->CompletedTrackDetails->HighestSeverity != 'SUCCESS'){
						die('fff');
						self::$_tracking_details = $Fedex->trackDetails($response->CompletedTrackDetails);
						/*self::$_messages .= '<table border="1">';
						self::$_messages .= '<tr><th>Shipment Level Tracking Details</th><th>&nbsp;</th></tr>';
						self::$_messages .= $Fedex->trackDetails($response->CompletedTrackDetails, '');
						self::$_messages .= '</table>';*/
					}else{
						$track = $response->CompletedTrackDetails->TrackDetails;
						
						// wrong track
						if(isset($track->Notification) && $track->Notification->Severity === 'ERROR'){
							self::$_tracking_details = $track->Notification->Message;
							return;
						}
						//
						
						// single tracking
						if(!is_array($track)){
							$track = array($track);
						}
						//
						
						self::$_tracking_details = $Fedex->trackDetails($track);
						/*self::$_messages .= '<table border="1">';
						self::$_messages .= '<tr><th>Package Level Tracking Details</th><th>&nbsp;</th></tr>';
						self::$_messages .= $Fedex->trackDetails($response->CompletedTrackDetails->TrackDetails, '');
						self::$_messages .= '</table>';*/
					}
				}
				//self::$_messages .= $Fedex->printSuccess($client, $response);
			}else{
				self::$_messages .= $Fedex->printError($client, $response);
			} 
			
			//self::$_messages .= $Fedex->writeToLog($client); // Write to log file   
		} catch (SoapFault $exception) {
			self::$_messages .= $Fedex->printFault($exception,$client);
		}
	}

	public function getTrackingDetails(){
		return self::$_tracking_details?self::$_tracking_details:false;
	}
	public function getMessages(){
		return self::$_messages;
	}
}
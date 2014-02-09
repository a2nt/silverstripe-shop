<?php
/**
 * Handles tasks to be performed on orders, particularly placing and processing/fulfilment.
 * Placing, Emailing Reciepts, Status Updates, Printing, Payments - things you do with a completed order.
 * 
 * @package shop
 * @todo split into different classes relating to individual concerns.
 * @todo bring over status updating code
 * @todo figure out reference issues ...if you store a reference to order in here, it can get stale.
 */
class OrderProcessor{

	protected $order;
	protected $error;

	/**
	 * Static way to create the order processor.
	 * Makes creating a processor easier.
	 * @param Order $order
	 */
	static function create(Order $order) {		
		return new OrderProcessor($order);
	}
	/**
	 * Assign the order to a local variable
	 * @param Order $order
	 */
	private function __construct(Order $order) {
		$this->order = $order;
	}

	/**
	 * Takes an order from being a cart to awaiting payment.
	 * @param Member $member - assign a member to the order
	 * @return boolean - success/failure
	 */
	function placeOrder() {
		if(!$this->order){
			$this->error(_t("OrderProcessor.NULL", "A new order has not yet been started."));
			return false;
		}
		if(!$this->canPlace($this->order)){ //final cart validation
			return false;
		}
		//do a final calculation
		$this->order->calculate();
		//update status
		if($this->order->TotalOutstanding()){
			$this->order->Status = 'Unpaid';
		}else{
			$this->order->Status = 'Processing';
		}
		if(!$this->order->Placed){
			$this->order->Placed = SS_Datetime::now()->Rfc2822(); //record placed order datetime
			if($request = Controller::curr()->getRequest()){
				$this->order->IPAddress = $request->getIP(); //record client IP
			}
		}
		//re-write all attributes and modifiers to make sure they are up-to-date before they can't be changed again
		$items = $this->order->Items();
		if($items->exists()){
			foreach($items as $item){
				$item->onPlacement();
				$item->write();
			}
		}
		$modifiers = $this->order->Modifiers();
		if($modifiers->exists()){
			foreach($modifiers as $modifier){
				$modifier->write();
			}
		}
		//add member to customers group
		$member = $this->order->Member();
		if($member->exists()){
			$cgroup = ShopConfig::current()->CustomerGroup();
			if($cgroup->exists()){
				$member->Groups()->add($cgroup);
			}
		}
		//save order reference to session
		OrderManipulation::add_session_order($this->order);
		//allow decorators to do stuff when order is saved.
		$this->order->extend('onPlaceOrder');
		$this->order->write();
		return true; //report success
	}

	/**
	 * Determine if an order can be placed.
	 * @param unknown_type $order
	 */
	function canPlace(Order $order) {
		if(!$order){
			$this->error(_t("OrderProcessor.NULL", "Order does not exist."));
			return false;
		}
		//order status is applicable	
		if(!$order->IsCart()){
			$this->error(_t("OrderProcessor.NOTCART", "Order is not a cart."));
			return false;
		}
		//order has products
		if($order->Items()->Count() <= 0){
			$this->error(_t("OrderProcessor.NOITEMS", "Order has no items."));
			return false;
		}
		//if total > 0, then payment has been made / started
		//shipping has been selected (if required)
		//modifiers have been calculated
		return true;
	}
	
	/**
	 * Create a payment model, and provide link to redirect to external gateway,
	 * or redirect to order link.
	 * @return string - url for redirection after payment has been made
	 */
	function makePayment($gateway, $gatewaydata = array()) {
		//create payment
		$payment = $this->createPayment($gateway);
		if(!$payment){
			return false;
		}
		//map shop data to omnipay fields
		$shipping = $this->order->getShippingAddress();
		$billing = $this->order->getBillingAddress();
		$data = array_merge($gatewaydata, array(
			'reference' => $this->order->Reference,
			'firstName' => $this->order->FirstName,
			'lastName' => $this->order->Surname,
			'email' => $this->order->Email,
			'company' => $this->order->Company,
			'billingAddress1' => $billing->Address,
			'billingAddress2' => $billing->AddressLine2,
			'billingCity' => $billing->City,
			'billingPostcode' => $billing->PostalCode,
			'billingState' => $billing->State,
			'billingCountry' => $billing->Country,
			'billingPhone' => $billing->Phone,
			'shippingAddress1' => $shipping->Address,
			'shippingAddress2' => $shipping->AddressLine2,
			'shippingCity' => $shipping->City,
			'shippingPostcode' => $shipping->PostalCode,
			'shippingState' => $shipping->State,
			'shippingCountry' => $shipping->Country,
			'shippingPhone' => $shipping->Phone,
		));

		// Process payment, get the result back
		$response = $payment->purchase($data);
		if($response->isSuccessful()) {
			$this->completePayment();
		}
		return $response;
	}

	/**
	 * Create a new payment for an order
	 */
	function createPayment($gateway) {
		if(!GatewayInfo::is_supported($gateway)) {
			$this->error(_t("PaymentProcessor.INVALIDGATEWAY", "`$gateway` isn't a valid payment gateway."));
			return false;
		}
		if(!$this->order->canPay(Member::currentUser())){
			$this->error(_t("PaymentProcessor.CANTPAY", "Order can't be paid for."));
			return false;
		}
		$payment = Payment::create()
			->init($gateway, $this->order->TotalOutstanding(), $currency = "NZD")
			->setReturnUrl($this->order->Link());
		$this->order->Payments()->add($payment);
		return $payment;
	}

	/**
	 * Complete payment processing
	 *    - send receipt
	 * 	- update order status accordingling
	 * 	- fire event hooks
	 */
	function completePayment() {
		if(!$this->order->Paid){
			if(!$this->order->ReceiptSent){
				$this->sendReceipt();
			}
			$this->order->extend('onPayment'); //a payment has been made
			//place the order, if not already placed
			if($this->canPlace($this->order)){
				$this->placeOrder();
			}
			if($this->order->GrandTotal() > 0 && $this->order->TotalOutstanding() <= 0){
				//set order as paid
				$this->order->Status = 'Paid';
				$this->order->Paid = SS_Datetime::now()->Rfc2822();
				$this->order->write();
				foreach($this->order->Items() as $item){
					$item->onPayment();
				}
				$this->order->extend('onPaid'); //all payment is settled
			}
		}
	}	

	/**
	* Send a mail of the order to the client (and another to the admin).
	*
	* @param $emailClass - the class name of the email you wish to send
	* @param $copyToAdmin - true by default, whether it should send a copy to the admin
	*/
	function sendEmail($emailClass, $copyToAdmin = true) {
		$sitecconfig = SiteConfig::current_site_config();
		if(!$sitecconfig->Email){
			$from = ShopConfig::config()->email_from ? ShopConfig::config()->email_from : Config::inst()->get('Email','admin_email');
		}else{
			$from = $sitecconfig->Email;
		}

		$to = $this->order->getLatestEmail();
		$subject = _t('Order.EMAILSUBJECT','Shop Sale Information #{reference}',array('reference' => $this->order->Reference));
		$purchaseCompleteMessage = DataObject::get_one('CheckoutPage')->PurchaseComplete;
		$email = new $emailClass();
		$email->setFrom($from);
		$email->setTo($to);
		$email->setSubject($subject);
		if($copyToAdmin){
			$email->setBcc($from);//Config::inst()->get('Email','admin_email'));
		}
		$email->populateTemplate(array(
			'PurchaseCompleteMessage' => $purchaseCompleteMessage,
			'Order' => $this->order
		));
		return $email->send();
	}

	/**
	* Send the receipt of the order by mail.
	* Precondition: The order payment has been successful
	*/
	function sendReceipt() {
		$this->sendEmail('Order_ReceiptEmail');
		$this->order->ReceiptSent = SS_Datetime::now()->Rfc2822();
		$this->order->write();
	}

	/**
	* Send a message to the client containing the latest
	* note of {@link OrderStatusLog} and the current status.
	*
	* Used in {@link OrderReport}.
	*
	* @param string $note Optional note-content (instead of using the OrderStatusLog)
	*/
	function sendStatusChange($title, $note = null) {
		if(!$note) {
			$logs = OrderStatusLog::get()
				->filter("OrderID", $this->order->ID)
				->filter("SentToCustomer", 1);
			if($logs) {
				$latestLog = $logs->First();
				$note = $latestLog->Note;
				$title = $latestLog->Title;
			}
		}
		$member = $this->order->Member();
		if(self::$receipt_email) {
			$adminEmail = self::$receipt_email;
		}else {
			$adminEmail = Config::inst()->get('Email','admin_email');
		}
		$e = new Order_statusEmail();
		$e->populateTemplate($this);
		$e->populateTemplate(array(
			"Order" => $this->order,
			"Member" => $member,
			"Note" => $note
		));
		$e->setFrom($adminEmail);
		$e->setSubject($title);
		$e->setTo($member->Email);
		$e->send();
	}

	function getError() {
		return $this->error;
	}

	private function error($message) {
		$this->error = $message;
	}

}

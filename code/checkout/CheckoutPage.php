<?php
/**
 * CheckoutPage is a CMS page-type that shows the order
 * details to the customer for their current shopping
 * cart on the site. 
 *
 * @see CheckoutPage_Controller->Order()
 *
 * @package shop
 */
class CheckoutPage extends BasicPage {

	private static $db = array(
		'PurchaseComplete' => 'HTMLText'
	);

	private static $icon = 'shop/images/icons/money';

	/**
	 * Returns the link to the checkout page on this site
	 *
	 * @param boolean $urlSegment If set to TRUE, only returns the URLSegment field
	 * @return string Link to checkout page
	 */
	static function find_link($urlSegment = false, $action = null, $id = null) {
		if(!$page = CheckoutPage::get()->first()) {
			return Controller::join_links(
				Director::baseURL(),
				CheckoutPage_Controller::config()->url_segment
			);
		}
		$id = ($id)? "/".$id : "";
		return ($urlSegment) ?
			$page->URLSegment :
			Controller::join_links($page->Link($action),$id);
	}

	/**
	 * Only allow one checkout page
	 */
	function canCreate($member = null) {
		return !CheckoutPage::get()->exists();
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldsToTab('Root.Main', array(
			HtmlEditorField::create(
				'PurchaseComplete',
				_t('CheckoutPage.PURCHASECOMPLETE','Purchase Complete'),
			4)
				->setDescription(
					_t('CheckoutPage.PURCHASECOMPLETEDESC','This message is included in reciept email, after the customer submits the checkout')
				)
		),'Metadata');
		return $fields;
	}
	
}

class CheckoutPage_Controller extends BasicPage_Controller {

	private static $allowed_actions = array(
		'OrderForm',
		'payment',
		'PaymentForm',
		'complete'
	);
	
	/**
	 * Display a title if there is no model, or no title.
	 */
	public function Title() {
		if($this->Title)
			return $this->Title;
		return _t('CheckoutPage.TITLE',"Checkout");
	}

	function OrderForm() {
		if(!(bool)$this->Cart()){
			return false;
		}
		return new PaymentForm(
			$this,
			'OrderForm',
			Injector::inst()->create("CheckoutComponentConfig", ShoppingCart::curr())
		);
	}

	/**
	 * Action for making on-site payments
	 */
	function payment(){
		if(!$this->Cart()){
			return $this->redirect($this->Link());
		}

		return array(
			'Title' => _t('CheckoutPage.MAKEPAYMENT','Make Payment'),
			'OrderForm' => $this->PaymentForm()
		);
	}

	function complete(){
		return array(
			'Title' => _t('CheckoutPage.PURCHASECOMPLETETITLE','Congratiulations, your purchase is complete.'),
			'Content' => $this->PurchaseComplete,
			'OrderForm' => false
		);
	}

	function PaymentForm(){
		if(!(bool)$this->Cart()){
			return false;
		}
		$config = new CheckoutComponentConfig(ShoppingCart::curr(),false);
		$config->AddComponent(new OnsitePaymentCheckoutComponent());
		$form = new PaymentForm($this, "PaymentForm", $config);
		$form->setActions(new FieldList(
			FormAction::create("submitpayment","Submit Payment")
		));

		return $form;
	}

}
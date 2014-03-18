<?php

/**
 * View and edit the cart in a full page.
 * Visitor can continue shopping, or proceed to checkout.
 */
class CartPage extends BasicPage {

	private static $has_one = array(
		'CheckoutPage' => 'CheckoutPage',
		'ContinuePage' => 'SiteTree'
	);

	private static $icon = 'shop/images/icons/cart';

	/**
	 * Only allow one cart page
	 */
	public function canCreate($member = null) {
		return !CartPage::get()->exists();
	}

	public function getCMSFields(){
		$fields = parent::getCMSFields();
		if($checkouts = CheckoutPage::get()) {
			$fields->addFieldToTab('Root.Links',
				DropdownField::create(
					'CheckoutPageID',
					_t('CartPage.CHECKOUTPAGE','Checkout Page'),
					$checkouts->map("ID","Title")
				)
			);
			$fields->findOrMakeTab('Root.Links')->setTitle(_t('CartPage.LINKS','Links'));
		}
		if($pgroups = ProductCategory::get()) {
			$fields->addFieldToTab('Root.Links',
				DropdownField::create(
					'ContinuePageID',
					_t('CartPage.CONTINUEPAGE','Continue Product Group Page'),
					$pgroups->map("ID","Title")
				)
			);
			$fields->findOrMakeTab('Root.Links')->setTitle(_t('CartPage.LINKS','Links'));
		}
		
		return $fields;
	}
	
	/**
	 * Returns the link to the checkout page on this site
	 *
	 * @param boolean $urlSegment If set to TRUE, only returns the URLSegment field
	 * @return string Link to checkout page
	 */
	public static function find_link($urlSegment = false, $action = false, $id = false) {
		$page = CartPage::get()->first();
		$base = $page ? $page->Link() : CartPage_Controller::config()->url_segment;
		if($urlSegment){
			return $base;
		}
		return Controller::join_links($base,$action,$id);
	}

}

class CartPage_Controller extends BasicPage_Controller{
	
	private static $url_segment = 'cart';
	private static $allowed_actions = array(
		"CartForm",
		"updatecart"
	);
	
	/**
	 * Display a title if there is no model, or no title.
	 */
	public function Title(){
		if($this->Title)
			return $this->Title;
		return _t('CartPage.TITLE',"Shopping Cart");
	}

	/**
	 * A form for updating cart items
	 */
	public function CartForm(){
		$cart = $this->Cart();
		if(!$cart){
			return false;
		}
		return new CartForm($this,"CartForm",$cart);
	}

}
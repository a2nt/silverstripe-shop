<?php

/**
 * ViewableData extension that provides access to the cart from anywhere.
 * Also handles last-minute recalculation, if required.
 * All order updates: quantities, modifiers etc should be done before
 * this function is called.
 * 
 * @package shop
 */
class ViewableCart extends Extension{
	
	protected $calculateonce = false;
	
	/**
	 * Get the cart, and do last minute calculation if necessary.
	 */
	function Cart(){
		$order = ShoppingCart::curr();
		if(!$order || !$order->Items() || !$order->Items()->exists()){
			return false;
		}
		if(!$this->calculateonce && $order){
			$this->calculateonce = true;
			$order->calculate();
		}
		return $order->customise(array(
			'CartLink' => CartPage::find_link()
		));
	}
	
	public function getContinueLink(){
		$class = Config::inst()->get('ShopConfig','product_category_class')?
			Config::inst()->get('ShopConfig','product_category_class')
			: 'ProductCategory';
		if($maincategory = $class::get()->sort('ParentID ASC, ID ASC')->First()){
			return $maincategory->Link();
		}
		return Director::baseURL();
	}
	
	public function getCheckoutLink(){
		return CheckoutPage::find_link();
	}
	public function getCartLink(){
		return CartPage::find_link();
	}
}
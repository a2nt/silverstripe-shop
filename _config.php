<?php

define('SHOP_DIR',basename(__DIR__));
define('SHOP_PATH',BASE_PATH.DIRECTORY_SEPARATOR.SHOP_DIR);
if(!defined('BASKET_PATH')){
	define('BASKET_PATH', rtrim(basename(dirname(__FILE__))));
}

Object::useCustomClass('Currency','ShopCurrency', true);

if($checkoutsteps = CheckoutPage::config()->steps){
	SteppedCheckout::setupSteps($checkoutsteps, CheckoutPage::config()->first_step);
}
<?php

class CheckoutForm extends BootstrapForm {
	
	protected $config, $redirectlink;

	function __construct($controller, $name, CheckoutComponentConfig $config) {
		$this->config = $config;
		$fields = $config->getFormFields();

		$actions = FieldList::create(
<<<<<<< HEAD
			$btn = FormAction::create(
=======
			FormAction::create(
>>>>>>> 75f5fd7fd4c0ddcb5320b96839df4d3fd54d2cef
				'checkoutSubmit',
				_t('CheckoutForm.PROCEEDTOPAYMENT','Proceed to payment')
				.' <span class="icon icon-chevron-right"></span>'
			)->addExtraClass(Page_Controller::getBtnClass())
		);
		
		if(Config::inst()->get('ShopConfig','payment') != true){
			$btn->setTitle(
				'<span class="icon icon-ok"></span> '.
				_t('CheckoutForm.PLACEORDER','Place Order')
			);
		}

		$validator = new CheckoutComponentValidator($this->config);
		parent::__construct($controller, $name, $fields, $actions, $validator);
		$this->addExtraClass('vcard-input');
		$this->setAttribute('x-autocompletetype','registration');
		$this->setAttribute('autocomplete','on');
		$this->loadDataFrom($this->config->getData(), Form::MERGE_IGNORE_FALSEISH);
		if($sessiondata = Session::get("FormInfo.{$this->FormName()}.data")){
			$this->loadDataFrom($sessiondata, Form::MERGE_IGNORE_FALSEISH);
		}
	}

	function setRedirectLink($link){
		$this->redirectlink = $link;
	}

	function checkoutSubmit($data, $form) {
		//form validation has passed by this point, so we can save data
		$this->config->setData($form->getData());
		if($this->redirectlink){
			return $this->controller->redirect($this->redirectlink);
		}

		return $this->controller->redirectBack();
	}

}

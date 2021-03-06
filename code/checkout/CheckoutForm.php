<?php

class CheckoutForm extends Form {
	
	protected $config, $redirectlink;

	function __construct($controller, $name, CheckoutComponentConfig $config) {
		$this->config = $config;
		$fields = $config->getFormFields();

		$actions = new FieldList(
			FormAction::create(
				'checkoutSubmit',
				_t('CheckoutForm','Proceed to payment')
			)
		);
		$validator = new CheckoutComponentValidator($this->config);
		parent::__construct($controller, $name, $fields, $actions, $validator);
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

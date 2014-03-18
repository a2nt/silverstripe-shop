<?php

/**
 * Order validator makes sure everything is set correctly
 * and in place before an order can be placed.
 */
class CheckoutComponentValidator extends RequiredFields {

	protected $config;

	public function __construct(CheckoutComponentConfig $config) {
		$this->config = $config;
		parent::__construct($this->config->getRequiredFields());
	}

	public function php($data){
		$valid = parent::php($data);
		//do component validation
		try{
			$this->config->validateData($data);
		} catch(ValidationException $e){
			$result = $e->getResult();
			foreach($result->messageList() as $fieldname => $message){
				if(!$this->fieldHasError($fieldname)){
					$this->validationError($fieldname, $message, 'bad');
				}
			}
			$valid = false;
		}
		if(!$valid){
			$this->form->sessionMessage("There are problems with the data you entered. See below:","bad");
		}

		return $valid;
	}

	function fieldHasError($field) {
		if($this->errors){
			foreach($this->errors as $error){
				if($error['fieldName'] === $field){
					return true;
				}
			}
		}
		return false;
	}
	
}
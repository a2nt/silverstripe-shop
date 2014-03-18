<?php

class ContactCheckoutComponentConfig extends CheckoutComponentConfig {

	public function __construct(Order $order){
		parent::__construct($order);
		$this->addComponent(new CustomerDetailsCheckoutComponent());
		$this->addComponent(new NotesCheckoutComponent());
		$this->addComponent(new TermsCheckoutComponent());
	}

}

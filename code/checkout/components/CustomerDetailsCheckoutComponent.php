<?php

class CustomerDetailsCheckoutComponent extends CheckoutComponent {

	protected $requiredfields = array(
		'FirstName','Surname','Email'
	);

	public function getFormFields(Order $order){
		$fields = FieldList::create(
			CompositeField::create(
				CompositeField::create(
					$firstname = TextField::create('FirstName','')
						->addPlaceHolder(_t('CheckoutField.FIRSTNAME','First Name'))
						->prependText('<span class="icon icon-user"></span>')
						->setAttribute('data-minlength','3')
						->setAttribute('maxlength','30')
						->addExtraClass('given-name')
						->setAttribute('x-autocompletetype','given-name'),
					$email = EmailField::create('Email','')//_t('CheckoutField.EMAIL','Email')
						->addPlaceHolder(_t('Page.SUBSCRIBEFORMEMAIL','youremail@youremail.com'))
						->setAttribute('pattern','[a-z\.0-9]+@[a-z\.0-9]+')
						->setAttribute('data-minlength','5')
						->setAttribute('maxlength','30')
						->prependText('<span class="icon icon-envelope"></span>')
						->addExtraClass('email')
						->setAttribute('x-autocompletetype','email')
				)->addExtraClass('pull-left'),
				CompositeField::create(
					$surname = TextField::create('Surname','')
						->addPlaceHolder(_t('CheckoutField.SURNAME','Surname'))
						->prependText('<span class="icon icon-user"></span>')
						->setAttribute('data-minlength','3')
						->setAttribute('maxlength','30')
						->addExtraClass('family-name')
						->setAttribute('x-autocompletetype','surname')
				)->addExtraClass('pull-right')
			)->addExtraClass('customer-details clear-fix fn')
		);
		//populate fields with member details, if logged in
		if($member = Member::currentUser()){
			$firstname->setValue($member->FirstName);
			$surname->setValue($member->Surname);
			$email->setValue($member->Email);
		}

		return $fields;
	}
		
	public function validateData(Order $order, array $data){
		//all fields are required
	}

	public function getData(Order $order){
		return array(
			'FirstName' => $order->FirstName,
			'Surname' => $order->Surname,
			'Email' => $order->Email
		);
	}

	public function setData(Order $order, array $data){
		$order->update($data);
		$order->write();
	}
	
}
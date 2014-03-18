<?php

class MembershipCheckoutComponent extends CheckoutComponent{

	protected $confirmed, $passwordvalidator;

	protected $dependson = array(
		'CustomerDetailsCheckoutComponent'
	);

	function __construct($confirmed = true, $validator = null){
		$this->confirmed = $confirmed;
		if(!$validator){
			$this->passwordvalidator = Member::password_validator();
			if(!$this->passwordvalidator){
				$this->passwordvalidator = new PasswordValidator();
				$this->passwordvalidator->minLength(5);
				$this->passwordvalidator->characterStrength(2,array('lowercase', 'uppercase', 'digits', 'punctuation'));
			}
		}
	}
	
	public function getFormFields(Order $order, Form $form = null){
		$fields = new FieldList();
		if(Member::currentUserID()){
			return $fields;
		}
		$idfield = Config::inst()->get('Member','unique_identifier_field');
		if(!$order->{$idfield} &&
			($form && !$form->Fields()->fieldByName($idfield))){
				$fields->push(new TextField($idfield,$idfield)); //TODO: scaffold the correct id field
		}
		$fields->push($this->getPasswordField());
		return $fields;
	}

	public function getRequiredFields(Order $order) {
		if(Member::currentUserID() || !Checkout::membership_required()){
			return array();
		}
		return array(
			Config::inst()->get('Member','unique_identifier_field'),
			'Password'
		);
	}

	public function getPasswordField(){
		if($this->confirmed){
			//relies on fix: https://github.com/silverstripe/silverstripe-framework/pull/2757
			return BootstrapConfirmedPasswordField::create('Password', _t('CheckoutField.PASSWORD','Password'))
					->setCanBeEmpty(!Checkout::membership_required());
		}
		return PasswordField::create('Password','')
			->addPlaceHolder(_t('Member.PASSWORD', 'Password'))
			->prependText('<span class="icon icon-eye-close"></span>')
			->setAttribute('required','required')
			->setAttribute('pattern','[a-z 0-9 .-_@#$!^&*()+=]+')
			->setAttribute('data-minlength','6')
			->setAttribute('maxlength','30')
			->addHelpText(
				sprintf(_t(
					'BootstrapChangePasswordForm.ALLOWEDSYMBOLS',
					'Password length: %s - %s. You can use special symbols: %s'
				),'6','30','.-_@#$!^&amp;*()+=')
			);
	}

	public function validateData(Order $order, array $data){
		if(Member::currentUserID()){
			return;
		}
		$result = new ValidationResult();
		if(Checkout::membership_required() || !empty($data['Password'])){
			$member = new Member($data);
			$idfield = Config::inst()->get('Member','unique_identifier_field');
			$idval = $data[$idfield];
			if(ShopMember::get_by_identifier($idval)){
				$result->error(
					_t(
						'Checkout.MEMBEREXISTS',
						'A member already exists with the {field} {value}',
						array('field' => $idfield, 'value' => $idval)
					),
					$idval
				);
			}
			$passwordresult = $this->passwordvalidator->validate($data['Password'], $member);
			if(!$passwordresult->valid()){
				$result->error($passwordresult->message(), "Password");
			}
		}
		if(!$result->valid()){
			throw new ValidationException($result);
		}
	}

	public function getData(Order $order){
		$data = array();

		if($member = Member::currentUser()){
			$idf = Config::inst()->get('Member','unique_identifier_field');
			$data[$idf] = $member->{$idf};
		}
		return $data;
	}

	/**
	 * @throws ValidationException
	 */
	public function setData(Order $order, array $data){
		if(Member::currentUserID()){
			return;
		}
		if(!Checkout::membership_required() && empty($data['Password'])){
			return;
		}
		$member = Checkout::get($order)->createMembership($data);
		$member->write();
		$member->logIn();
	}

	function setConfirmed($confirmed){
		$this->confirmed = $confirmed;

		return $this;
	}

}
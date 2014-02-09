<?php
 /**
  * Allows shop members to update their details with the shop.
  *
  * @package shop
  * @subpackage forms
  */
class AccountForm extends BootstrapForm {

	public function __construct($controller, $name) {
		$member = Member::currentUser();
		$requiredFields = null;
		if($member && $member->exists()) {
			$fields = $member->getMemberFormFields();
			$fields->removeByName('Password');
			$fields->removeByName('Locale');
			$fields->removeByName('DateFormat');
			$fields->removeByName('TimeFormat');
			$fields->removeByName('ClientSiteID');

			// style fields
			$fields->fieldByName('FirstName')
				->setTitle('')
				->addPlaceHolder(_t('RegistrationPage.FIRSTNAME','First Name'))
				->prependText('<span class="icon icon-user"></span>')
				->setAttribute('required','required')
				->setAttribute('data-minlength','3')
				->setAttribute('maxlength','30');
			$fields->fieldByName('Surname')
				->setTitle('')
				->addPlaceHolder(_t('RegistrationPage.SURNAME','Surname'))
				->prependText('<span class="icon icon-user"></span>')
				->setAttribute('required','required')
				->setAttribute('data-minlength','2')
				->setAttribute('maxlength','30');
			$fields->fieldByName('Email')
				->setTitle('')
				->addPlaceHolder(_t('Page.SUBSCRIBEFORMEMAIL','youremail@youremail.com'))
				->setAttribute('required','required')
				->setAttribute('pattern','[a-z\.0-9]+@[a-z\.0-9]+')
				->setAttribute('data-minlength','5')
				->setAttribute('maxlength','30')
				->prependText('<span class="icon icon-envelope"></span>');
			//

			$requiredFields = $member->getValidator();
		} else {
			$fields = FieldList::create();
		}
		if(get_class($controller) == 'AccountPage_Controller'){
			$actions = FieldList::create(FormAction::create('submit', _t('MemberForm.SAVE','Save Changes')));
		}
		else{
			$actions = FieldList::create(
				FormAction::create('submit', _t('MemberForm.SAVE','Save Changes'))
			);
		}
		if($record = $controller->data()){
			$record->extend('updateAccountForm',$fields,$actions,$requiredFields);
		}
		parent::__construct($controller, $name, $fields, $actions, $requiredFields);
		if($member){
			$member->Password = ''; //prevents password field from being populated with encrypted password data 
			$this->loadDataFrom($member);
		}
		
	}

	/**
	 * Save the changes to the form
	 */
	public function submit($data, $form, $request) {
		$member = Member::currentUser();
		if(!$member) return false;

		$form->saveInto($member);
		$member->write();
		$form->sessionMessage(_t('MemberForm.DETAILSSAVED','Your details have been saved'), 'good');

		Controller::curr()->redirectBack();
		return true;
	}

	/**
	 * Save the changes to the form, and redirect to the account page
	 */
	function proceed($data, $form, $request) {
		$member = Member::currentUser();
		if(!$member){
			return false;
		}
		$form->saveInto($member);
		$member->write();
		$form->sessionMessage(_t('MemberForm.DETAILSSAVED','Your details has been saved'),'good');
		Director::redirect(AccountPage::find_link());
		return true;
	}

}

/**
* Validates the shop account form.
* @subpackage forms
*/
class AccountFormValidator extends RequiredFields {

	/**
	 * Ensures member unique id stays unique.
	 */
	public function php($data){
		$valid = parent::php($data);
<<<<<<< HEAD
		$field = Config::inst()->get('Member','unique_identifier_field');
		if(isset($data[$field])){
			$uid = $data[Config::inst()->get('Member','unique_identifier_field')];
=======
		$field = Member::get_unique_identifier_field();
		if(isset($data[$field])){
			$uid = $data[Member::get_unique_identifier_field()];
>>>>>>> 75f5fd7fd4c0ddcb5320b96839df4d3fd54d2cef
			$member = Member::currentUser();
			//can't be taken
			$otherMember = Member::get()->filter($field,$uid)->where('ID != '.$member->ID);
			if($otherMember->exists){
				$this->validationError(
					$field,
					'"'.$uid.'" is already taken by another member. Try another.',
					'required'
				);
				$valid = false;
			}
		}
		return $valid;
	}

}
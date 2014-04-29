<?php 
class ProfileEditor extends AccountEditor {
	private static $allowed_actions = array(
		'EditProfileForm',
		'ChangePasswordForm'
	);

	public function ChangePasswordForm(){
		return BootstrapChangePasswordForm::create($this,'ChangePasswordForm');
	}

	public function EditProfileForm(){
		return AccountForm::create($this,'EditProfileForm');
	}
}
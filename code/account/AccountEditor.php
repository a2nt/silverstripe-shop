<?php
class AccountEditor extends BasicPage_Controller {
	protected $data_less_controller = true;
	protected $member = false;
	public function init(){
		parent::init();
		$this->member = Member::currentUser();
		if(!$this->member) {
			$messages = array(
				'default' => '<p class="message good">'.
					_t(
						'AccountPage.Message',
						'You\'ll need to login before'
						.' you can access the account page.'
						.' If you are not registered, you won\'t'
						.' be able to access it until you make your first order,'
						.' otherwise please enter your details below.'
					)
				.'</p>',
				'logInAgain' => 'You have been logged out.'
				.' If you would like to log in again, please do so below.'
			);
			return Security::permissionFailure($this,$messages);
		}
		self::require_styled_type();
		self::require_js_type();
	}

	public static function require_styled_type($type = null){
		if(!$type){
			$type = get_called_class();
		}
		Compressor::compress_file('less_Profile'.$type.'.css',array(
			BASKET_PATH.'/less/Profile'.$type.'.less',
			//self::$themedir.'/less/Profile'.$type.'.less'
		));
		if(method_exists($type,'require_styled_type')){
			$type::require_styled_type();
		}
	}

	public static function require_js_type($type = null){
		if(!$type){
			$type = get_called_class();
		}
		Compressor::compress_file('less_Profile'.$type.'.js',array(
			BASKET_PATH.'/js/Profile'.$type.'.js',
			//self::$themedir.'/js/Profile'.$type.'.js'
		));
		if(method_exists($type,'require_js_type')){
			$type::require_js_type();
		}
	}

	public static function getTitle(){
		//$title = (isset(self::$title))?
			//self::$title
			//:preg_replace('!([A-Z])!',' $1',get_called_class());
		return _t(
			get_called_class().'.Title',
			preg_replace('!([A-Z])!',' $1',get_called_class())
		);
	}

	public static function getContent(){
		return _t(get_called_class().'.Content');
	}

	public static function AccountMenu() {
		return AccountPage_Controller::AccountMenu();
	}

	public function Layout(){
		if(!$this->template){
			$this->template = 'AccountPage_'.get_called_class();
			if(!SSViewer::hasTemplate($this->template)){
				$this->template = 'AccountPage';
			}
		}
		$this->extend('updateTemplate',$this->template);
		return $this->renderWith($this->template);
	}

	public function MetaTags($includeTitle = true){
		$tags = parent::MetaTags($includeTitle);
		$tags = preg_replace(
			'!<title>(.[^<]+)</title>!i',
			'<title>'.self::getTitle().' $1</title>',
			$tags
		).'<meta name="robots" content="noindex" />';
		return $tags;
	}
	public function Link($action = null,$http = true){
		$link = $this->RelativeLink($action);
		if($this->config()->secure_domain){
			$s = array(
				'http://',
				'http://www.'
			);
			$r = 'https://'.$this->config()->secure_domain;
		}else{
			$s = '';
			$r = '';
		}
		
		return
			str_ireplace(
				$s,
				$r,
				Director::absoluteURL(
					Controller::join_links(
						Director::baseURL(),
						$link
					)
				)
			);
	}
}